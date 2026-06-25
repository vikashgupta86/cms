<?php

namespace Database\Seeders\Auth;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        $this->CreateDefaultPermissions();

        /**
         * Create Roles and Assign Permissions to Roles.
         */
        $super_admin = Role::firstOrCreate(['id' => '1'], ['name' => 'super admin']);

        $admin = Role::firstOrCreate(['id' => '2'], ['name' => 'administrator']);
        $admin->givePermissionTo(['view_backend', 'edit_settings']);

        $manager = Role::firstOrCreate(['id' => '3'], ['name' => 'manager']);
        $manager->givePermissionTo('view_backend');

        $executive = Role::firstOrCreate(['id' => '4'], ['name' => 'executive']);
        $executive->givePermissionTo('view_backend');

        $user = Role::firstOrCreate(['id' => '5'], ['name' => 'user']);
    }

    public function CreateDefaultPermissions()
    {
        // Create Permissions
        $permissions = Permission::defaultPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Artisan::call('auth:permissions', [
            'name' => 'posts',
        ]);
        if (! app()->runningUnitTests()) {
            $this->command->info('_Posts_ Permissions Created.');
        }

        Artisan::call('auth:permissions', [
            'name' => 'categories',
        ]);
        if (! app()->runningUnitTests()) {
            $this->command->info('_Categories_ Permissions Created.');
        }

        Artisan::call('auth:permissions', [
            'name' => 'tags',
        ]);
        if (! app()->runningUnitTests()) {
            $this->command->info('_Tags_ Permissions Created.');
        }

        Artisan::call('auth:permissions', [
            'name' => 'comments',
        ]);
        if (! app()->runningUnitTests()) {
            $this->command->info('_Comments_ Permissions Created.');
        }
    }
}
