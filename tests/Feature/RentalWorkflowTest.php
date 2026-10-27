<?php

namespace Tests\Feature;

use App\Models\Pokemon;
use App\Models\Rental;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function partner(string $code = 'PKM001'): Pokemon
    {
        $pokemon = Pokemon::create([
            'pokemon_code' => $code,
            'pokemon_name' => 'Bulbasaur',
            'pokemon_level' => 18,
            'description' => 'Partner hutan',
            'rental_price' => 60000,
            'status' => 'available',
        ]);

        $pokemon->types()->attach(
            Type::firstOrCreate(['type' => 'Grass'])->id
        );

        return $pokemon;
    }

    private function payload(Pokemon $pokemon): array
    {
        return [
            'pokemon_id' => $pokemon->id,
            'start_date' => today()->toDateString(),
            'duration' => 3,
            'destination' => 'Hutan Viridian',
            'agreement' => 1,
        ];
    }

    public function test_rental_confirmation_creation_and_admin_return_workflow(): void
    {
        $trainer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $pokemon = $this->partner();
        $data = $this->payload($pokemon);

        $this->actingAs($trainer)
            ->post(route('trainer.rentals.confirm'), $data)
            ->assertOk()
            ->assertSee('Konfirmasi Petualanganmu');

        $this->assertDatabaseCount('rentals', 0);

        $this->post(
            route('trainer.rentals.store'),
            $data + ['total_price' => 1]
        )->assertRedirect();

        $rental = Rental::firstOrFail();

        $this->assertSame('180000.00', $rental->total_price);
        $this->assertSame('on_adventure', $pokemon->fresh()->status);

        $this->get(route('trainer.rentals.show', $rental))
            ->assertOk()
            ->assertSee('Hutan Viridian');

        $this->travel(5)->days();

        $this->post(
            route('trainer.rentals.request-return', $rental),
            ['agreement' => 1]
        )->assertRedirect();

        $this->assertSame(
            'return_requested',
            $rental->fresh()->rental_status
        );

        $this->assertSame(
            'on_adventure',
            $pokemon->fresh()->status
        );

        $this->travel(2)->days();

        $this->actingAs($admin)
            ->post(
                route('admin.returns.complete', $rental),
                ['condition' => 'resting']
            )
            ->assertRedirect();

        $this->assertSame(
            'completed',
            $rental->fresh()->rental_status
        );

        $this->assertSame('200000.00', $rental->fresh()->late_fee);

        $this->assertSame(
            $admin->id,
            $rental->fresh()->processed_by_admin_id
        );

        $this->assertSame('resting', $pokemon->fresh()->status);

        $this->post(
            route('admin.returns.complete', $rental),
            ['condition' => 'available']
        )->assertSessionHasErrors('rental');

        $this->assertSame('resting', $pokemon->fresh()->status);
    }

    public function test_quota_and_partner_availability_prevent_duplicate_rentals(): void
    {
        $trainer = User::factory()->create();

        $first = $this->partner();
        $second = $this->partner('PKM002');
        $third = $this->partner('PKM003');

        $this->actingAs($trainer)
            ->post(
                route('trainer.rentals.store'),
                $this->payload($first)
            )
            ->assertRedirect();

        $this->post(
            route('trainer.rentals.store'),
            $this->payload($first)
        )->assertSessionHasErrors('pokemon_id');

        $this->post(
            route('trainer.rentals.store'),
            $this->payload($second)
        )->assertRedirect();

        $this->post(
            route('trainer.rentals.store'),
            $this->payload($third)
        )->assertSessionHasErrors('pokemon_id');

        $this->assertDatabaseCount('rentals', 2);

        $this->assertSame(
            'available',
            $third->fresh()->status
        );

        $other = User::factory()->create();

        $this->actingAs($other)
            ->post(
                route('trainer.rentals.store'),
                $this->payload($first)
            )
            ->assertSessionHasErrors('pokemon_id');
    }

    public function test_duration_dates_and_role_ownership_are_enforced(): void
    {
        $trainer = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $pokemon = $this->partner();

        $this->get(route('trainer.catalog.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($trainer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->post(
            route('admin.types.store'),
            ['type' => 'Fire']
        )->assertForbidden();

        $this->post(
            route('trainer.rentals.store'),
            array_replace(
                $this->payload($pokemon),
                ['duration' => 6]
            )
        )->assertSessionHasErrors('duration');

        $this->post(
            route('trainer.rentals.store'),
            array_replace(
                $this->payload($pokemon),
                ['start_date' => today()->subDay()->toDateString()]
            )
        )->assertSessionHasErrors('start_date');

        $this->post(
            route('trainer.rentals.store'),
            $this->payload($pokemon)
        )->assertRedirect();

        $rental = Rental::firstOrFail();

        $this->actingAs($other)
            ->get(route('trainer.rentals.show', $rental))
            ->assertForbidden();

        $this->post(
            route('trainer.rentals.request-return', $rental),
            ['agreement' => 1]
        )->assertForbidden();

        $this->actingAs($admin)
            ->get(route('trainer.dashboard'))
            ->assertForbidden();

        $this->post(
            route('admin.returns.complete', $rental),
            ['condition' => 'available']
        )->assertSessionHasErrors('rental');
    }

    public function test_all_main_pages_render_with_data_and_reports_export(): void
    {
        $pokemon = $this->partner();
        $trainer = User::factory()->create();

        $trainer->trainerProfile()->create([
            'phone' => '08123',
            'address' => 'Pallet Town',
            'trainer_level' => 1,
        ]);

        $this->actingAs($trainer)
            ->post(
                route('trainer.rentals.store'),
                $this->payload($pokemon)
            );

        $rental = Rental::firstOrFail();

        foreach ([
            'trainer.dashboard',
            'trainer.catalog.index',
            'trainer.match',
            'trainer.profile.edit',
            'trainer.rentals.index',
            'trainer.rentals.history',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }

        $this->get(route('trainer.catalog.show', $pokemon))
            ->assertOk();

        $this->get(route('trainer.rentals.create', $pokemon))
            ->assertOk();

        $this->get(route('trainer.rentals.return', $rental))
            ->assertOk();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        foreach ([
            'admin.dashboard',
            'admin.pokemons.index',
            'admin.pokemons.create',
            'admin.types.index',
            'admin.trainers.index',
            'admin.rentals.index',
            'admin.returns.index',
            'admin.reports.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }

        $this->get(route('admin.pokemons.edit', $pokemon))
            ->assertOk();

        $this->get(route('admin.reports.export'))
            ->assertOk()
            ->assertDownload(
                'laporan-rental-'.today()->toDateString().'.csv'
            );

        $this->get(
            route('admin.reports.index', [
                'from' => today()->toDateString(),
                'to' => today()->toDateString(),
            ])
        )->assertOk();
    }

    public function test_match_filters_and_referenced_master_data_are_preserved(): void
    {
        $pokemon = $this->partner();
        $trainer = User::factory()->create();

        $this->actingAs($trainer)
            ->get(route('trainer.match', [
                'terrain' => 'forest',
                'budget' => 70000,
            ]))
            ->assertSee('Bulbasaur');

        $this->get(route('trainer.match', [
            'terrain' => 'water',
        ]))->assertDontSee('Bulbasaur');

        $this->post(
            route('trainer.rentals.store'),
            $this->payload($pokemon)
        );

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->delete(
                route(
                    'admin.types.destroy',
                    $pokemon->types->first()
                )
            )
            ->assertSessionHasErrors('type');

        $this->delete(
            route('admin.trainers.destroy', $trainer)
        )->assertSessionHasErrors('trainer');

        $this->delete(
            route('admin.pokemons.destroy', $pokemon)
        )->assertSessionHas('error');

        $this->assertDatabaseHas('pokemons', [
            'id' => $pokemon->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $trainer->id,
        ]);
    }

    public function test_late_and_return_requested_rentals_use_slots_and_completed_rentals_release_them(): void
    {

        $trainer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $first = $this->partner();
        $second = $this->partner('PKM002');
        $third = $this->partner('PKM003');

        // Trainer menyewa dua Pokémon.
        $this->actingAs($trainer)
            ->post(
                route('trainer.rentals.store'),
                $this->payload($first)
            );

        $this->post(
            route('trainer.rentals.store'),
            $this->payload($second)
        );

        $rentals = Rental::orderBy('id')->get();

        // Rental pertama berubah menjadi late.
        $rentals[0]->update([
            'rental_status' => 'late',
        ]);

        // Rental kedua mengajukan pengembalian.
        $this->post(
            route(
                'trainer.rentals.request-return',
                $rentals[1]
            ),
            ['agreement' => 1]
        )->assertSessionHasNoErrors();

        // Kedua rental tetap menggunakan dua slot.
        $this->get(route('trainer.dashboard'))
            ->assertOk()
            ->assertViewHas('slots', 2);

        // Pokémon ketiga ditolak karena kuota awal masih dua.
        $this->post(
            route('trainer.rentals.store'),
            $this->payload($third)
        )->assertSessionHasErrors('pokemon_id');

        // Admin menyelesaikan pengembalian.
        $this->actingAs($admin)
            ->post(
                route(
                    'admin.returns.complete',
                    $rentals[1]
                ),
                ['condition' => 'available']
            )
            ->assertSessionHasNoErrors();

        // Setelah selesai, satu slot dibebaskan.
        $this->actingAs($trainer)
            ->get(route('trainer.dashboard'))
            ->assertOk()
            ->assertViewHas('slots', 1);

        // Pokémon ketiga sekarang boleh disewa.
        $this->post(
            route('trainer.rentals.store'),
            $this->payload($third)
        )->assertSessionHasNoErrors();

        // Rental selesai harus masuk ke riwayat.
        $completedId = $rentals[1]->id;

        $this->get(route('trainer.rentals.history'))
            ->assertViewHas(
                'rentals',
                fn ($history) =>
                    $history->total() === 1 &&
                    $history->first()->id === $completedId
            );
    }

    public function test_rental_validation_returns_indonesian_messages_without_reserving_a_partner(): void
    {
        $trainer = User::factory()->create();
        $pokemon = $this->partner();

        $this->actingAs($trainer)
            ->post(route('trainer.rentals.confirm'), [
                'pokemon_id' => $pokemon->id,
                'start_date' => today()->subDay()->toDateString(),
                'duration' => 6,
                'destination' => '   ',
            ])
            ->assertSessionHasErrors([
                'start_date' =>
                    'Tanggal mulai tidak boleh sebelum hari ini.',
                'duration' =>
                    'Durasi sewa harus antara 1 sampai 5 hari.',
                'destination' =>
                    'Isi tujuan dan medan petualangan, misalnya Hutan Viridian.',
                'agreement' =>
                    'Centang persetujuan aturan sewa sebelum melanjutkan.',
            ]);

        $this->assertDatabaseCount('rentals', 0);

        $this->assertSame(
            'available',
            $pokemon->fresh()->status
        );
    }
}
