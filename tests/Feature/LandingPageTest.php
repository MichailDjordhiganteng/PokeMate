<?php

namespace Tests\Feature;

use App\Models\Pokemon;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    private function partner(string $code, string $name, string $status = 'available'): Pokemon
    {
        $pokemon = Pokemon::create([
            'pokemon_code' => $code, 'pokemon_name' => $name, 'pokemon_level' => 25,
            'description' => 'Companion untuk petualangan.', 'rental_price' => 75000, 'status' => $status,
        ]);
        $pokemon->types()->attach(Type::firstOrCreate(['type' => 'Electric'])->id);

        return $pokemon;
    }

    public function test_public_landing_uses_real_partner_data_and_keeps_authentication_links(): void
    {
        $resting = $this->partner('PKM001', 'Pikachu', 'resting');
        $available = $this->partner('PKM002', 'Bulbasaur');
        $available->update(['pokemon_image' => 'pokemons/custom-companion.png']);
        $this->partner('PKM003', 'Charmander');
        $this->partner('PKM004', 'Squirtle');
        $this->partner('PKM005', 'Rowlet', 'unavailable');
        Type::create(['type' => 'Water']);

        $this->get(route('home'))->assertOk()->assertViewIs('landing')
            ->assertViewHas('partnerCount', 5)->assertViewHas('typeCount', 2)
            ->assertViewHas('heroPartner', fn ($partner) => $partner->is($available))
            ->assertViewHas('featuredPartners', fn ($partners) => $partners->count() === 4 && $partners->last()->is($resting))
            ->assertSee('pokemons/custom-companion.png', false)
            ->assertSee('Rp 75.000')->assertSee('Istirahat')->assertDontSee('150+')
            ->assertSee('href="'.route('login').'"', false)
            ->assertSee('href="'.route('register').'"', false)
            ->assertDontSee('Rowlet');
    }

    public function test_empty_pokedex_still_has_a_usable_public_landing(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertViewHas('partnerCount', 0)->assertViewHas('typeCount', 0)
            ->assertViewHas('heroPartner', null)
            ->assertSee('Petualangan baru sedang disiapkan.')
            ->assertSee('images/pokeball.png', false)
            ->assertSee('href="'.route('register').'"', false);
    }

    public function test_authenticated_users_keep_the_marketing_layout_and_role_appropriate_links(): void
    {
        $pokemon = $this->partner('PKM001', 'Pikachu');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('home'))->assertOk()
            ->assertSee('class="lp-body"', false)->assertDontSee('class="admin-sidebar"', false)
            ->assertSee('href="'.route('admin.dashboard').'"', false)
            ->assertSee('href="'.route('admin.pokemons.edit', $pokemon).'"', false)
            ->assertDontSee('/trainer/rentals');

        $trainer = User::factory()->create();
        $this->actingAs($trainer)->get(route('home'))->assertOk()
            ->assertSee('class="lp-body"', false)
            ->assertSee('href="'.route('trainer.dashboard').'"', false)
            ->assertSee('href="'.route('trainer.catalog.show', $pokemon).'"', false)
            ->assertDontSee('/admin/pokemons');
    }
}
