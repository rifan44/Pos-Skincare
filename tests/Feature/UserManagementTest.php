<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_cashier_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Kasir Baru',
            'email' => 'kasir@example.test',
            'password' => 'long-password',
            'role' => 'kasir',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'kasir@example.test',
            'role' => 'kasir',
        ]);
    }

    public function test_cashiers_cannot_manage_other_accounts(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($cashier)->post(route('users.store'), [
            'name' => 'Unauthorized',
            'email' => 'unauthorized@example.test',
            'password' => 'long-password',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('pos.index'));
        $this->assertDatabaseCount('users', 1);
    }

    public function test_admin_cannot_change_the_role_of_the_active_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->from(route('users.index'))->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'kasir',
        ]);

        $response->assertRedirect(route('users.index'))->assertSessionHas('error');
        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_admin_cannot_delete_the_active_account_from_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->from(route('users.index'))->delete(route('users.destroy', $admin));

        $response->assertRedirect(route('users.index'))->assertSessionHas('error');
        $this->assertNotNull($admin->fresh());
    }
}
