<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'manage user roles', // Kullanıcı rollerini değiştirme yetkisi
            
            // Blog Management
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
            
            // Category Management
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Tag Management
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            
            // Activity Logs
            'view activity logs',
            
            // Settings
            'view settings',
            'edit settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles and assign permissions
        
        // Super Admin - All permissions
        $superAdmin = Role::findOrCreate('Super Admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        // Admin - Most permissions except user management
        $admin = Role::findOrCreate('Admin', 'web');
        $admin->syncPermissions([
            'view users',
            'edit users',
            'view roles',
            'manage user roles', // Admin kullanıcı rollerini değiştirebilir
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            'view activity logs',
            'view settings',
        ]);

        // Editor - Content management
        $editor = Role::findOrCreate('Editor', 'web');
        $editor->syncPermissions([
            'view posts',
            'create posts',
            'edit posts',
            'publish posts',
            'view categories',
            'create categories',
            'edit categories',
            'view tags',
            'create tags',
            'edit tags',
        ]);

        // Author - Limited content creation
        $author = Role::findOrCreate('Author', 'web');
        $author->syncPermissions([
            'view posts',
            'create posts',
            'edit posts', // only their own posts (handled in policies)
            'view categories',
            'view tags',
        ]);

        // Assign Super Admin role to first user (admin)
        $adminUser = User::first();
        if ($adminUser && ! $adminUser->hasRole('Super Admin')) {
            $adminUser->assignRole('Super Admin');
        }

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Available roles: Super Admin, Admin, Editor, Author');
    }
}