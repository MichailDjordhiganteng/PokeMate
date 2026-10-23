<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountsAndReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_account_management_and_role_boundaries(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $trainer = User::factory()->create();
        $data = [
            'full_name' => 'Nurse Joy', 'email' => 'joy@example.com',
            'password' => 'joysecret123', 'password_confirmation' => 'joysecret123', 'role' => 'trainer',
        ];
        $this->get(route('admin.admins.index'))->assertRedirect(route('login'));
        $this->actingAs($trainer)->get(route('admin.admins.index'))->assertForbidden();
        $this->post(route('admin.admins.store'), $data)->assertForbidden();
        $this->actingAs($owner)->post(route('admin.admins.store'), $data)->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.admins.index'));
        $admin = User::where('email', 'joy@example.com')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('joysecret123', $admin->password));
        $this->post(route('admin.admins.store'), $data)->assertSessionHasErrors('email');
        $this->get(route('admin.admins.index', ['search' => 'joy@example.com']))
            ->assertOk()->assertViewHas('admins', fn ($admins) => $admins->total() === 1);
        $this->put(route('admin.admins.update', $admin), [
            'full_name' => 'Joy Updated', 'email' => 'joy2@example.com', 'password' => '', 'role' => 'trainer',
        ])->assertSessionHasNoErrors();
        $this->assertSame('Joy Updated', $admin->fresh()->full_name);
        $this->assertSame('admin', $admin->fresh()->role);
        $this->assertTrue(Hash::check('joysecret123', $admin->fresh()->password));
        $this->put(route('admin.admins.update', $trainer), $data)->assertNotFound();
        $this->delete(route('admin.admins.destroy', $trainer))->assertNotFound();
        $this->delete(route('admin.admins.destroy', $owner))->assertSessionHasErrors('admin');
        $this->delete(route('admin.admins.destroy', $admin))->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
        $this->assertDatabaseHas('users', ['id' => $owner->id, 'role' => 'admin']);
    }

    public function test_admin_with_processing_history_cannot_be_deleted_and_own_password_requires_current_password(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $processor = User::factory()->create(['role' => 'admin']);
        $trainer = User::factory()->create();
        $rental = Rental::create([
            'trainer_id' => $trainer->id, 'start_date' => today(), 'due_date' => today()->addDay(),
            'rental_status' => 'completed', 'processed_by_admin_id' => $processor->id,
        ]);
        $this->actingAs($owner)->delete(route('admin.admins.destroy', $processor))->assertSessionHasErrors('admin');
        $this->assertSame($processor->id, $rental->fresh()->processed_by_admin_id);
        $data = [
            'full_name' => $owner->full_name, 'email' => $owner->email,
            'password' => 'newsecret123', 'password_confirmation' => 'newsecret123',
        ];
        $this->put(route('admin.admins.update', $owner), $data)->assertSessionHasErrors('current_password');
        $this->put(route('admin.admins.update', $owner), $data + ['current_password' => 'password'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('newsecret123', $owner->fresh()->password));
    }

    public function test_print_contains_all_filtered_rows_and_matching_totals_without_pagination(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $trainer = User::factory()->create(['full_name' => 'Trainer Dalam Filter']);
        $other = User::factory()->create(['full_name' => 'Trainer Di Luar Filter']);
        for ($index = 0; $index < 23; $index++) {
            Rental::create([
                'trainer_id' => $trainer->id, 'start_date' => today(), 'due_date' => today()->addDay(),
                'rental_status' => 'completed', 'total_price' => 10000, 'late_fee' => 2000,
            ]);
        }
        Rental::create([
            'trainer_id' => $other->id, 'start_date' => today(), 'due_date' => today()->addDay(),
            'rental_status' => 'completed', 'total_price' => 999999,
        ]);
        Rental::create([
            'trainer_id' => $trainer->id, 'start_date' => today(), 'due_date' => today()->addDay(),
            'rental_status' => 'active', 'total_price' => 888888,
        ]);
        $filters = ['search' => 'Dalam Filter', 'status' => 'completed', 'from' => today()->toDateString(), 'to' => today()->toDateString()];
        $this->actingAs($admin)->get(route('admin.reports.print', $filters))
            ->assertOk()->assertViewHas('count', 23)->assertViewHas('income', 230000)->assertViewHas('fees', 46000)
            ->assertSee('#RNT-00001')->assertSee('#RNT-00023')->assertDontSee('#RNT-00024')
            ->assertDontSee('#RNT-00025')->assertSee('window.print()', false)
            ->assertDontSee($other->full_name);
        $this->get(route('admin.reports.index', $filters))->assertOk()->assertViewHas('count', 23)
            ->assertViewHas('income', 230000)->assertViewHas('fees', 46000)
            ->assertSee(route('admin.reports.print'), false);
        $this->get(route('admin.reports.print', ['to' => today()->toDateString()]))->assertOk()->assertViewHas('count', 25);
        $this->get(route('admin.reports.print', ['from' => today()->toDateString(), 'to' => today()->subDay()->toDateString()]))
            ->assertSessionHasErrors('to');
        $this->actingAs($trainer)->get(route('admin.reports.print'))->assertForbidden();
    }
}
