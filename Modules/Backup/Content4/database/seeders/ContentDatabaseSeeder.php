<?php

namespace Modules\Content\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Content\Models\Content;
use Modules\Menu\Models\Menu;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ContentDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ── Permissions ───────────────────────────────────────────────────
        if (class_exists(Permission::class)) {
            $permissions = [
                'content.index',
                'content.create',
                'content.edit',
                'content.delete',
                'content.publish',
            ];

            foreach ($permissions as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }

            $role = Role::where('name', 'super-admin')->first()
                 ?? Role::where('name', 'admin')->first();

            if ($role) {
                $role->givePermissionTo($permissions);
                $this->command->info("Permissions assigned to [{$role->name}].");
            }
        }

        // ── Fetch real menus from DB ───────────────────────────────────────
        $topNav    = Menu::where('slug', 'top-nav')->first();
        $footerMenu = Menu::where('slug', 'footer-menu')->first();
        $adminMenu  = Menu::where('slug', 'admin-sidebar')->first();

        // ── Sample content for Top Nav (id=1) ────────────────────────────
        if ($topNav) {
            Content::updateOrCreate(
                ['menu_id' => $topNav->id, 'slug' => 'welcome'],
                [
                    'title'        => 'Welcome to Our Website',
                    'type'         => 'content',
                    'status'       => 'published',
                    'body'         => '<h2>Welcome!</h2><p>This is the homepage content managed via the CMS. You can edit this from the admin panel under <strong>Content Management</strong>.</p><p>This content is linked to the <em>Top Nav</em> menu.</p>',
                    'sort_order'   => 1,
                    'published_at' => now(),
                    'meta_title'   => 'Welcome',
                    'meta_description' => 'Welcome to our website.',
                ]
            );

            Content::updateOrCreate(
                ['menu_id' => $topNav->id, 'slug' => 'about-us'],
                [
                    'title'        => 'About Us',
                    'type'         => 'content',
                    'status'       => 'published',
                    'body'         => '<h2>About Us</h2><p>We are a dedicated team committed to excellence. Our mission is to deliver quality solutions.</p>',
                    'sort_order'   => 2,
                    'published_at' => now(),
                    'meta_title'   => 'About Us',
                ]
            );

            Content::updateOrCreate(
                ['menu_id' => $topNav->id, 'slug' => 'external-link-demo'],
                [
                    'title'        => 'Visit Google',
                    'type'         => 'external',
                    'status'       => 'published',
                    'external_url' => 'https://www.google.com',
                    'sort_order'   => 3,
                    'published_at' => now(),
                ]
            );

            $this->command->info("✓ Seeded 3 contents for menu: Top Nav (id={$topNav->id})");
        }

        // ── Sample content for Footer Menu (id=2) ────────────────────────
        if ($footerMenu) {
            Content::updateOrCreate(
                ['menu_id' => $footerMenu->id, 'slug' => 'privacy-policy'],
                [
                    'title'        => 'Privacy Policy',
                    'type'         => 'content',
                    'status'       => 'published',
                    'body'         => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy explains how we collect and use your data.</p>',
                    'sort_order'   => 1,
                    'published_at' => now(),
                    'meta_title'   => 'Privacy Policy',
                ]
            );

            Content::updateOrCreate(
                ['menu_id' => $footerMenu->id, 'slug' => 'terms-of-service'],
                [
                    'title'        => 'Terms of Service',
                    'type'         => 'content',
                    'status'       => 'published',
                    'body'         => '<h2>Terms of Service</h2><p>By using our services you agree to these terms and conditions.</p>',
                    'sort_order'   => 2,
                    'published_at' => now(),
                    'meta_title'   => 'Terms of Service',
                ]
            );

            Content::updateOrCreate(
                ['menu_id' => $footerMenu->id, 'slug' => 'contact-us'],
                [
                    'title'        => 'Contact Us',
                    'type'         => 'content',
                    'status'       => 'published',
                    'body'         => '<h2>Contact Us</h2><p>Email: contact@example.com<br>Phone: +91 00000 00000</p>',
                    'sort_order'   => 3,
                    'published_at' => now(),
                ]
            );

            $this->command->info("✓ Seeded 3 contents for menu: Footer Menu (id={$footerMenu->id})");
        }

        // ── Sample content for Admin Sidebar (id=3) ──────────────────────
        if ($adminMenu) {
            Content::updateOrCreate(
                ['menu_id' => $adminMenu->id, 'slug' => 'admin-guide'],
                [
                    'title'        => 'Admin User Guide',
                    'type'         => 'content',
                    'status'       => 'published',
                    'body'         => '<h2>Admin Guide</h2><p>This guide explains how to use the admin panel. Navigate using the sidebar.</p>',
                    'sort_order'   => 1,
                    'published_at' => now(),
                ]
            );

            Content::updateOrCreate(
                ['menu_id' => $adminMenu->id, 'slug' => 'draft-announcement'],
                [
                    'title'        => 'Upcoming Feature Announcement (Draft)',
                    'type'         => 'content',
                    'status'       => 'draft',
                    'body'         => '<p>This is a draft announcement not yet visible to the public.</p>',
                    'sort_order'   => 2,
                ]
            );

            $this->command->info("✓ Seeded 2 contents for menu: Admin Sidebar (id={$adminMenu->id})");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Content seeding complete.');
    }
}
