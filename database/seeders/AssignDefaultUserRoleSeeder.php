<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignDefaultUserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure default role exists on 'web' guard
        Role::findOrCreate('User', 'web');

        $count = 0;

        User::doesntHave('roles')->chunkById(200, function ($users) use (&$count) {
            foreach ($users as $user) {
                $user->assignRole('User');
                $count++;
            }
        });

        $this->command?->info("Assigned 'User' role to {$count} users without roles.");
    }
}


