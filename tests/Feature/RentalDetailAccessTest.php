<?php

namespace Tests\Feature;

use App\Models\Pokemon;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalDetailAccessTest extends TestCase
{
    use RefreshDatabase;

    private function rental(User $trainer, string $status): Rental
    {
        $pokemon = Pokemon::create([
            'pokemon_code' => 'PKM-'.$status, 'pokemon_name' => 'Bulbasaur', 'pokemon_level' => 18,
            'description' => 'Partner hutan', 'rental_price' => 90000, 'status' => 'available',
        ]);
        $rental = $trainer->rentals()->create([
            'start_date' => today()->subDays(2), 'due_date' => today()->addDay(),
            'rental_status' => $status, 'total_price' => 180000, 'destination' => 'Hutan Viridian',
            'return_requested_at' => $status === 'return_requested' ? now() : null,
            'returned_at' => $status === 'completed' ? now() : null,
        ]);
        $rental->rentalDetails()->create(['pokemon_id' => $pokemon->id, 'rental_price' => 60000]);

        return $rental;
    }

    public function test_admin_tables_do_not_render_blade_directives_or_trainer_detail_links(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $trainer = User::factory()->create();
        $rentals = collect(['active', 'return_requested', 'completed', 'cancelled'])
            ->map(fn ($status) => $this->rental($trainer, $status));

        $this->actingAs($admin);
        foreach (['admin.dashboard', 'admin.rentals.index', 'admin.reports.index'] as $route) {
            $response = $this->get(route($route))->assertOk()
                ->assertDontSee('@else')->assertDontSee('@endif')->assertDontSee('/trainer/rentals/');
            foreach ($rentals as $rental) {
                $response->assertSee('href="'.route('admin.rentals.show', $rental).'"', false);
            }
        }
        $this->get(route('admin.returns.index'))->assertOk()->assertDontSee('@else')
            ->assertDontSee('/trainer/rentals/')
            ->assertSee('href="'.route('admin.rentals.show', $rentals[1]).'"', false);
    }

    public function test_admin_can_open_rental_details_for_all_statuses_with_the_actual_trainer_and_historical_price(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'full_name' => 'Administrator Pokecenter']);
        $trainer = User::factory()->create(['full_name' => 'Trainer Pemilik Rental']);
        $this->actingAs($admin);
        foreach (['pending', 'active', 'late', 'return_requested', 'completed', 'cancelled'] as $status) {
            $rental = $this->rental($trainer, $status);
            $response = $this->get(route('admin.rentals.show', $rental))->assertOk()
                ->assertViewHas('adminView', true)
                ->assertSeeInOrder(['Trainer Penyewa', $trainer->full_name])
                ->assertSee('Rp 60.000')->assertDontSee('Rp 90.000')->assertDontSee('/trainer/rentals/');
            if ($status === 'return_requested') {
                $response->assertSee('href="'.route('admin.returns.index').'#return-'.$rental->id.'"', false);
            } else {
                $response->assertDontSee('Proses Inspeksi');
            }
        }
    }

    public function test_detail_routes_preserve_role_and_trainer_ownership_restrictions(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $rental = $this->rental($owner, 'active');
        $this->actingAs($owner)->get(route('trainer.rentals.show', $rental))->assertOk()
            ->assertViewHas('adminView', false)->assertSee('Ajukan Pengembalian')
            ->assertDontSee('href="'.route('admin.rentals.show', $rental).'"', false);
        $this->get(route('admin.rentals.show', $rental))->assertForbidden();
        $this->actingAs($other)->get(route('trainer.rentals.show', $rental))->assertForbidden();
        $this->get(route('admin.rentals.show', $rental))->assertForbidden();
        $this->actingAs($admin)->get(route('trainer.rentals.show', $rental))->assertForbidden();
        $this->get(route('admin.rentals.show', $rental))->assertOk();
        $this->get(route('admin.rentals.show', 999999))->assertNotFound();
    }

    public function test_rental_details_require_login(): void
    {
        $trainer = User::factory()->create();
        $rental = $this->rental($trainer, 'active');
        $this->get(route('admin.rentals.show', $rental))->assertRedirect(route('login'));
        $this->get(route('trainer.rentals.show', $rental))->assertRedirect(route('login'));
    }
}
