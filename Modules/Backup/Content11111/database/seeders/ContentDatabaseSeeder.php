<?php

namespace Modules\Content\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Content\Models\Content;
use Modules\Menu\Models\Menu;

class ContentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Safe to run multiple times — uses updateOrCreate().
     */
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

            // Assign all content permissions to super-admin role if it exists
            $superAdmin = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first()
                       ?? \Spatie\Permission\Models\Role::where('name', 'admin')->first();

            if ($superAdmin) {
                $superAdmin->givePermissionTo($permissions);
                $this->command->info("Content permissions granted to [{$superAdmin->name}] role.");
            }
        }

        // ----------------------------------------------------------------
        // Sample content — only inserted when a matching menu exists
        // ----------------------------------------------------------------
        $samples = [
            [
                'menu_slug' => 'about',     // change to match your actual menu slugs
                'title'     => 'About Us',
                'slug'      => 'about-us',
                'type'      => 'content',
                'status'    => 'published',
                'body'      => '<p>Welcome to our organisation. We are dedicated to excellence.</p>',
                'published_at' => now(),
                'sort_order'   => 1,
                'meta_title'   => 'About Us',
                'meta_description' => 'Learn more about our organisation.',
            ],
            [
                'menu_slug' => 'resources',
                'title'     => 'Annual Report 2024',
                'slug'      => 'annual-report-2024',
                'type'      => 'file',
                'status'    => 'published',
                'file_path' => null,         // no real file in seeder
                'file_name' => 'annual-report-2024.pdf',
                'file_size' => 0,
                'file_mime' => 'application/pdf',
                'published_at' => now(),
                'sort_order'   => 1,
            ],
            [
                'menu_slug' => 'links',
                'title'     => 'Official Government Portal',
                'slug'      => 'govt-portal',
                'type'      => 'external',
                'status'    => 'published',
                'external_url' => 'https://www.gov.in',
                'published_at' => now(),
                'sort_order'   => 1,
            ],
        ];

        foreach ($samples as $sample) {
            $menuSlug = $sample['menu_slug'];
            unset($sample['menu_slug']);

            // Try to find the menu
            $menu = Menu::where('slug', $menuSlug)->first();

            if (! $menu) {
                $this->command->warn("Skipping content [{$sample['title']}] — menu slug [{$menuSlug}] not found.");
                continue;
            }

            $sample['menu_id'] = $menu->id;

            Content::updateOrCreate(
                ['menu_id' => $menu->id, 'slug' => $sample['slug']],
                $sample
            );

            $this->command->info("Seeded content [{$sample['title']}] → menu [{$menu->name}].");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
