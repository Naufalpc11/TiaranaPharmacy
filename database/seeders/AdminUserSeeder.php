<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@yourdomain.com');
        $password = env('ADMIN_PASSWORD', 'PasswordKuat123!');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
            ]
        );

        if (!$user->is_admin) {
            $user->is_admin = true;
            $user->save();
        }
    }
}
