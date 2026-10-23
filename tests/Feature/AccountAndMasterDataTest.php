<?php

namespace Tests\Feature;

use App\Models\Pokemon;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountAndMasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_login_logout_and_guest_redirects(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
        $this->post(route('register.process'), [
            'full_name' => 'Misty', 'email' => 'misty@example.com', 'phone' => '0812345',
            'address' => 'Cerulean City', 'password' => 'secret123', 'password_confirmation' => 'secret123',
            'role' => 'admin',
        ])->assertRedirect(route('trainer.dashboard'));
        $trainer = User::where('email', 'misty@example.com')->firstOrFail();
        $this->assertSame('trainer', $trainer->role);
        $this->assertSame('Cerulean City', $trainer->trainerProfile->address);
        $this->get(route('login'))->assertRedirect(route('trainer.dashboard'));
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->post(route('login.process'), ['email' => $trainer->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post(route('login.process'), ['email' => $trainer->email, 'password' => 'secret123'])->assertRedirect(route('trainer.dashboard'));
    }

    public function test_profile_changes_require_current_password_and_protect_role_and_level(): void
    {
        Storage::fake('public');
        $trainer = User::factory()->create();
        $trainer->trainerProfile()->create(['phone' => '08123', 'address' => 'Pallet Town', 'trainer_level' => 1]);
        $data = [
            'full_name' => 'Ash Updated', 'email' => 'ash@example.com', 'phone' => '08999',
            'address' => 'Viridian City', 'password' => 'newsecret123', 'password_confirmation' => 'newsecret123',
            'role' => 'admin', 'trainer_level' => 100,
        ];
        $this->actingAs($trainer)->put(route('trainer.profile.update'), $data)->assertSessionHasErrors('current_password');
        // A real tiny PNG avoids dependence on the optional GD extension.
        $avatar = UploadedFile::fake()->createWithContent('avatar.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jf1sAAAAASUVORK5CYII='));
        $this->put(route('trainer.profile.update'), $data + ['current_password' => 'password', 'avatar' => $avatar])->assertSessionHasNoErrors()->assertRedirect();
        $trainer->refresh();
        $this->assertSame('trainer', $trainer->role);
        $this->assertSame(1, $trainer->trainerProfile->trainer_level);
        $this->assertTrue(Hash::check('newsecret123', $trainer->password));
        Storage::disk('public')->assertExists($trainer->trainerProfile->avatar);
    }

    public function test_admin_can_manage_types_pokemon_and_trainers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $trainer = User::factory()->create();
        $this->actingAs($admin)->get(route('login'))->assertRedirect(route('admin.dashboard'));
        $this->post(route('admin.types.store'), ['type' => 'Fire', 'description' => 'Api'])->assertSessionHasNoErrors();
        $type = Type::firstOrFail();
        $this->put(route('admin.types.update', $type), ['type' => 'Fire', 'description' => 'Api baru'])->assertSessionHasNoErrors();
        $data = [
            'pokemon_code' => 'PKM004', 'pokemon_name' => 'Charmander', 'pokemon_level' => 20,
            'description' => 'Partner api', 'rental_price' => 70000, 'status' => 'available', 'type_ids' => [$type->id],
        ];
        $this->post(route('admin.pokemons.store'), $data)->assertSessionHasNoErrors()->assertRedirect(route('admin.pokemons.index'));
        $pokemon = Pokemon::firstOrFail();
        $this->put(route('admin.pokemons.update', $pokemon), array_replace($data, ['rental_price' => 80000]))->assertSessionHasNoErrors();
        $this->assertSame('80000.00', $pokemon->fresh()->rental_price);
        $this->put(route('admin.trainers.update', $trainer), [
            'full_name' => 'Brock', 'email' => $trainer->email, 'phone' => '08123',
            'address' => 'Pewter City', 'trainer_level' => 15,
        ])->assertSessionHasNoErrors();
        $this->assertSame(15, $trainer->fresh()->trainerProfile->trainer_level);
        $this->delete(route('admin.pokemons.destroy', $pokemon))->assertSessionHas('success');
        $this->delete(route('admin.types.destroy', $type))->assertSessionHas('success');
        $this->delete(route('admin.trainers.destroy', $trainer))->assertSessionHas('success');
        $this->assertDatabaseCount('pokemons', 0);
        $this->assertDatabaseCount('types', 0);
        $this->assertDatabaseMissing('users', ['id' => $trainer->id]);
    }
}
