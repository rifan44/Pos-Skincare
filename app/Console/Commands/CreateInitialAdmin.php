<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateInitialAdmin extends Command
{
    protected $signature = 'pos:admin:create';

    protected $description = 'Buat akun admin pertama melalui prompt aman';

    public function handle(): int
    {
        if (User::query()->where('role', 'admin')->exists()) {
            $this->components->error('Admin sudah tersedia. Kelola akun berikutnya melalui menu Pengguna.');

            return self::FAILURE;
        }

        $input = [
            'name' => trim((string) $this->ask('Nama admin')),
            'email' => mb_strtolower(trim((string) $this->ask('Email admin'))),
            'password' => $this->secret('Password admin (minimal 12 karakter)'),
            'password_confirmation' => $this->secret('Konfirmasi password admin'),
        ];

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $attributes = $validator->validated();

        try {
            $created = Cache::lock('pos-skincare:first-admin-setup', 30)->block(10, function () use ($attributes): bool {
                return DB::transaction(function () use ($attributes): bool {
                    if (User::query()->where('role', 'admin')->lockForUpdate()->exists()) {
                        return false;
                    }

                    User::create([
                        'name' => $attributes['name'],
                        'email' => $attributes['email'],
                        'password' => $attributes['password'],
                        'role' => 'admin',
                    ]);

                    return true;
                });
            });
        } catch (LockTimeoutException) {
            $this->components->error('Penyiapan admin lain sedang berjalan. Jalankan ulang perintah sebentar lagi.');

            return self::FAILURE;
        }

        if (! $created) {
            $this->components->error('Admin sudah dibuat oleh proses lain. Kelola akun berikutnya melalui menu Pengguna.');

            return self::FAILURE;
        }

        $this->components->info('Admin pertama berhasil dibuat. Silakan masuk melalui halaman login.');

        return self::SUCCESS;
    }
}
