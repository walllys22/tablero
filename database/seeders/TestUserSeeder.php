<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $userRole = Role::where('name', 'user')->firstOrFail();

        $admin = User::updateOrCreate([
            'email' => 'admin@torneos.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $user = User::updateOrCreate([
            'email' => 'test@torneos.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $user->roles()->syncWithoutDetaching([$userRole->id]);

        $this->command?->info('Usuario Admin listo: admin@torneos.com / password123');
        $this->command?->info('Usuario Test listo: test@torneos.com / password123');
    }
}
