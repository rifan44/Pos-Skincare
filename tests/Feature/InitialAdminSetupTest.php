<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InitialAdminSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_administrator_can_be_created_with_hidden_prompt_and_hashed_password(): void
    {
        $password = 'secure-test-password-123';
        config(['cache.default' => 'database']);

        $this->artisan('pos:admin:create')
            ->expectsQuestion('Nama admin', 'Admin Utama')
            ->expectsQuestion('Email admin', 'ADMIN@example.test')
            ->expectsQuestion('Password admin (minimal 12 karakter)', $password)
            ->expectsQuestion('Konfirmasi password admin', $password)
            ->assertExitCode(Command::SUCCESS);

        $admin = User::query()->sole();

        $this->assertSame('Admin Utama', $admin->name);
        $this->assertSame('admin@example.test', $admin->email);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check($password, $admin->password));

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => $password,
        ])->assertRedirect(route('dashboard.index'));
    }

    public function test_command_refuses_to_create_another_initial_administrator(): void
    {
        User::factory()->create(['role' => 'admin']);

        $this->artisan('pos:admin:create')
            ->expectsOutputToContain('Admin sudah tersedia.')
            ->assertExitCode(Command::FAILURE);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_command_rejects_short_or_mismatched_password_without_creating_user(): void
    {
        $this->artisan('pos:admin:create')
            ->expectsQuestion('Nama admin', 'Admin Utama')
            ->expectsQuestion('Email admin', 'admin@example.test')
            ->expectsQuestion('Password admin (minimal 12 karakter)', 'too-short')
            ->expectsQuestion('Konfirmasi password admin', 'different-password')
            ->assertExitCode(Command::FAILURE);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_database_seeder_does_not_create_a_shared_sample_account(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 0);
    }
}
