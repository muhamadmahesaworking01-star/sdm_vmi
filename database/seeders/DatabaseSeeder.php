<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        User::updateOrCreate(['email' => 'superadmin@example.com'], [
            'name' => 'Super Admin',
            'password' => $password,
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        User::updateOrCreate(['email' => 'karyawan@example.com'], [
            'name' => 'User Karyawan',
            'password' => $password,
            'role' => User::ROLE_KARYAWAN,
        ]);

        User::updateOrCreate(['email' => 'pengajar@example.com'], [
            'name' => 'User Pengajar',
            'password' => $password,
            'role' => User::ROLE_PENGAJAR,
        ]);

        User::updateOrCreate(['email' => 'direksi@example.com'], [
            'name' => 'User Direksi',
            'password' => $password,
            'role' => User::ROLE_DIREKSI,
        ]);
    }
}
