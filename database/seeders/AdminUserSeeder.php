<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'tiaranafarma@gmail.com');
        $password = env('ADMIN_PASSWORD', 'TiaranaFarma1774');

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
