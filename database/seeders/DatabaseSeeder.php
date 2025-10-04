<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Ensure base admin user exists
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Example Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ],
        );

        $this->call([
            RolePermissionSeeder::class,
            BlogSeeder::class,
            AssignDefaultUserRoleSeeder::class,
        ]);

        // Create test users for each role
        $usersToSeed = [
            [
                'name' => 'Super Admin Tester',
                'email' => 'superadmin@test.com',
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Admin Tester',
                'email' => 'admin@test.com',
                'role' => 'Admin',
            ],
            [
                'name' => 'Editor Tester',
                'email' => 'editor@test.com',
                'role' => 'Editor',
            ],
            [
                'name' => 'Author Tester',
                'email' => 'author@test.com',
                'role' => 'Author',
            ],
        ];

        foreach ($usersToSeed as $seed) {
            $user = User::firstOrCreate(
                ['email' => $seed['email']],
                [
                    'name' => $seed['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ],
            );

            // Assign role if not already assigned
            if (! $user->hasRole($seed['role'])) {
                $user->assignRole($seed['role']);
            }
        }
    }
}
