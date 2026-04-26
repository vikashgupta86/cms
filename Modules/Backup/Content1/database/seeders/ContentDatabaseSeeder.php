<?php

namespace Modules\Content\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Content\Models\Content;
use Modules\Menu\Models\Menu;

class ContentDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ----------------------------------------------------------------
        // Permission seeding (Spatie laravel-permission)
        // ----------------------------------------------------------------
        if (class_exists(\Spatie\Permission\Models\Permission::class)) {
            $permissions = [
                'content.index',
                'content.create',
                'content.edit',
                'content.delete',
                'content.publish',
            ];

            foreach ($permissions as $permission) {
                \Spatie\Permission\Models\Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => 'web']
                );
            }

            $this->command->info('Content permissions seeded.');

            $superAdmin = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first()
                       ?? \Spatie\Permission\Models\Role::where('name', 'admin')->first();

            if ($superAdmin) {
                $superAdmin->givePermissionTo($permissions);
                $this->command->info("Permissions granted to [{$superAdmin->name}] role.");
            }
        }

        // ----------------------------------------------------------------
        // Fetch all existing menus
        // ----------------------------------------------------------------
        $menus = Menu::all();

        if ($menus->isEmpty()) {
            $this->command->warn('No menus found in database. Skipping content seeding.');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return;
        }

        $this->command->info('Found ' . $menus->count() . ' menu(s). Seeding content...');

        // ----------------------------------------------------------------
        // Seed sample content into every menu that exists
        // ----------------------------------------------------------------
        foreach ($menus as $menu) {

            // Sample 1 — content type
            Content::updateOrCreate(
                ['menu_id' => $menu->id, 'slug' => 'welcome'],
                [
                    'title'       => 'Welcome to ' . $menu->name,
                    'type'        => 'content',
                    'status'      => 'published',
                    'body'        => '<h2>Welcome</h2><p>This is a sample content page under the <strong>' . $menu->name . '</strong> section. You can edit or delete this from the admin panel.</p>',
                    'sort_order'  => 1,
                    'published_at' => now(),
                    'meta_title'  => 'Welcome to ' . $menu->name,
                    'meta_description' => 'Sample content page under ' . $menu->name,
                ]
            );

            // Sample 2 — external type
            Content::updateOrCreate(
                ['menu_id' => $menu->id, 'slug' => 'sample-external-link'],
                [
                    'title'        => 'Sample External Link',
                    'type'         => 'external',
                    'status'       => 'published',
                    'external_url' => 'https://www.google.com',
                    'sort_order'   => 2,
                    'published_at' => now(),
                ]
            );

            // Sample 3 — draft content
            Content::updateOrCreate(
                ['menu_id' => $menu->id, 'slug' => 'draft-article'],
                [
                    'title'      => 'Draft Article',
                    'type'       => 'content',
                    'status'     => 'draft',
                    'body'       => '<p>This article is still in draft mode and will not appear on the frontend.</p>',
                    'sort_order' => 3,
                ]
            );

            $this->command->info("  ✓ Seeded 3 items for menu: [{$menu->name}] (id={$menu->id})");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Content seeding complete.');
    }
}