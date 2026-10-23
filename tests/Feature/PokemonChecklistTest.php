<?php

namespace Tests\Feature;

use App\Models\Pokemon;
use App\Models\Rental;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PokemonChecklistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function data(string $code = 'PKM010', string $name = 'Eevee'): array
    {
        return [
            'pokemon_code' => $code, 'pokemon_name' => $name, 'pokemon_level' => 10,
            'description' => 'Partner untuk pengujian checklist', 'rental_price' => 50000,
            'status' => 'available', 'type_ids' => [Type::firstOrCreate(['type' => 'Normal'])->id],
        ];
    }

    private function image(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jf1sAAAAASUVORK5CYII='
        ));
    }

    public function test_admin_and_trainer_can_search_by_name_and_code(): void
    {
        $this->post(route('admin.pokemons.store'), $this->data())->assertSessionHasNoErrors();
        $this->post(route('admin.pokemons.store'), $this->data('PKM011', 'Pidgey'))->assertSessionHasNoErrors();
        foreach (['Eevee', 'PKM010'] as $search) {
            $this->get(route('admin.pokemons.index', ['search' => $search]))
                ->assertOk()->assertSee('Eevee')->assertDontSee('Pidgey')
                ->assertViewHas('pokemons', fn ($pokemons) => $pokemons->total() === 1);
        }
        $this->actingAs(User::factory()->create());
        foreach (['Eevee', 'PKM010'] as $search) {
            $this->get(route('trainer.catalog.index', ['search' => $search]))
                ->assertOk()->assertSee('Eevee')->assertDontSee('Pidgey');
        }
        $this->get(route('trainer.catalog.index', ['search' => 'TidakAda']))
            ->assertOk()->assertViewHas('pokemons', fn ($pokemons) => $pokemons->total() === 0);
    }

    public function test_duplicate_codes_are_rejected_on_create_and_update_but_unchanged_code_is_allowed(): void
    {
        $data = $this->data();
        $this->post(route('admin.pokemons.store'), $data)->assertSessionHasNoErrors();
        $pokemon = Pokemon::firstOrFail();
        $this->post(route('admin.pokemons.store'), $data)->assertSessionHasErrors('pokemon_code');
        $this->assertDatabaseCount('pokemons', 1);
        $this->put(route('admin.pokemons.update', $pokemon), $data)->assertSessionHasNoErrors();
        $otherData = $this->data('PKM011', 'Pidgey');
        $this->post(route('admin.pokemons.store'), $otherData)->assertSessionHasNoErrors();
        $other = Pokemon::where('pokemon_code', 'PKM011')->firstOrFail();
        $this->put(route('admin.pokemons.update', $other), array_replace($otherData, ['pokemon_code' => 'PKM010']))
            ->assertSessionHasErrors('pokemon_code');
        $this->assertSame('PKM011', $other->fresh()->pokemon_code);
    }

    public function test_images_are_uploaded_replaced_and_removed_when_deleting_unused_pkm010(): void
    {
        $data = $this->data();
        $this->post(route('admin.pokemons.store'), $data + ['pokemon_image' => $this->image('first.png')])
            ->assertSessionHasNoErrors();
        $pokemon = Pokemon::firstOrFail();
        $oldPath = $pokemon->pokemon_image;
        Storage::disk('public')->assertExists($oldPath);
        $this->put(route('admin.pokemons.update', $pokemon), $data + ['pokemon_image' => $this->image('second.png')])
            ->assertSessionHasNoErrors();
        $newPath = $pokemon->fresh()->pokemon_image;
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
        $invalid = UploadedFile::fake()->createWithContent('invalid.png', 'not an image');
        $this->put(route('admin.pokemons.update', $pokemon), $data + [
            'pokemon_image' => new UploadedFile($invalid->getPathname(), 'invalid.png', null, null, true),
        ])->assertSessionHasErrors('pokemon_image');
        $this->assertSame($newPath, $pokemon->fresh()->pokemon_image);
        Storage::disk('public')->assertExists($newPath);
        $this->get(route('admin.pokemons.index'))->assertSee(route('admin.pokemons.destroy', $pokemon), false)->assertSee('Hapus');
        $this->delete(route('admin.pokemons.destroy', $pokemon))->assertSessionHas('success');
        $this->assertDatabaseMissing('pokemons', ['pokemon_code' => 'PKM010']);
        $this->assertDatabaseMissing('pokemon_types', ['pokemon_id' => $pokemon->id]);
        Storage::disk('public')->assertMissing($newPath);
    }

    public function test_completed_history_and_on_adventure_independently_block_deletion_and_show_flash_errors(): void
    {
        $data = $this->data();
        $this->post(route('admin.pokemons.store'), $data + ['pokemon_image' => $this->image('history.png')]);
        $pokemon = Pokemon::firstOrFail();
        $trainer = User::factory()->create();
        $rental = Rental::create([
            'trainer_id' => $trainer->id, 'start_date' => today()->subDays(3), 'due_date' => today(),
            'rental_status' => 'completed', 'total_price' => 150000,
        ]);
        $rental->rentalDetails()->create(['pokemon_id' => $pokemon->id, 'rental_price' => 50000]);
        $this->delete(route('admin.pokemons.destroy', $pokemon))->assertSessionHas('error');
        $this->get(route('admin.pokemons.index'))->assertSee('sudah memiliki riwayat penyewaan');
        $this->assertDatabaseHas('rental_details', ['rental_id' => $rental->id, 'pokemon_id' => $pokemon->id]);
        $this->assertDatabaseHas('pokemon_types', ['pokemon_id' => $pokemon->id]);
        Storage::disk('public')->assertExists($pokemon->pokemon_image);

        $this->post(route('admin.pokemons.store'), $this->data('PKM011', 'Pidgey'));
        $adventuring = Pokemon::where('pokemon_code', 'PKM011')->firstOrFail();
        $adventuring->update(['status' => 'on_adventure']);
        $this->delete(route('admin.pokemons.destroy', $adventuring))->assertSessionHas('error');
        $this->get(route('admin.pokemons.index'))->assertSee('sedang disewa dan tidak dapat dihapus');
        $this->put(route('admin.pokemons.update', $adventuring), $this->data('PKM011', 'Changed'))
            ->assertSessionHasErrors('status');
        $this->assertSame('Pidgey', $adventuring->fresh()->pokemon_name);
    }

    public function test_catalog_and_inventory_filters_and_sorting_use_database_values(): void
    {
        $this->post(route('admin.pokemons.store'), $this->data());
        $normal = Pokemon::firstOrFail();
        $normal->update(['status' => 'resting']);
        $fire = Type::create(['type' => 'Fire']);
        $this->post(route('admin.pokemons.store'), array_replace($this->data('PKM011', 'Charmander'), [
            'rental_price' => 100000, 'pokemon_level' => 40, 'type_ids' => [$fire->id],
        ]))->assertSessionHasNoErrors();
        $this->get(route('admin.pokemons.index', ['type_id' => $fire->id, 'status' => 'available']))
            ->assertOk()->assertViewHas('pokemons', fn ($pokemons) => $pokemons->total() === 1 && $pokemons->first()->pokemon_name === 'Charmander');
        $this->get(route('admin.pokemons.index', ['sort' => 'level']))
            ->assertOk()->assertViewHas('pokemons', fn ($pokemons) => $pokemons->first()->pokemon_level === 40);
        $this->actingAs(User::factory()->create())->get(route('trainer.catalog.index', ['sort' => 'price_high']))
            ->assertOk()->assertViewHas('pokemons', fn ($pokemons) => $pokemons->first()->pokemon_name === 'Charmander');
        $this->get(route('trainer.catalog.index', ['type_id' => $fire->id, 'status' => 'resting']))
            ->assertOk()->assertViewHas('pokemons', fn ($pokemons) => $pokemons->total() === 0);
    }
}
