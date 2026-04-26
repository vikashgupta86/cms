<?php

namespace Modules\Content\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Content\Models\Content;
use Modules\Menu\Models\MenuItem;

class ContentDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissions ───────────────────────────────────────────────────
        if (class_exists(\Spatie\Permission\Models\Permission::class)) {
            $perms = ['content.index','content.create','content.edit','content.delete','content.publish'];
            foreach ($perms as $p) {
                \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
            }
            $role = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first()
                 ?? \Spatie\Permission\Models\Role::where('name', 'admin')->first();
            if ($role) {
                $role->givePermissionTo($perms);
                $this->command->info("Permissions assigned to [{$role->name}].");
            }
        }

        // ── Seed content per real menu_items from your DB ─────────────────
        // Matches the menu_items seeded in your DB dump
        $samples = [
            // About Us section
            ['slug_mi' => 'overview',       'title' => 'Overview',              'type' => 'content',  'body' => '<h2>Overview</h2><p>Welcome to our organisation overview page.</p>'],
            ['slug_mi' => 'organisation',   'title' => 'Organisation',          'type' => 'content',  'body' => '<h2>Organisation</h2><p>Details about our organisational structure.</p>'],
            ['slug_mi' => 'leadership',     'title' => 'Leadership Team',       'type' => 'content',  'body' => '<h2>Leadership</h2><p>Meet our leadership team.</p>'],
            // Tenders section
            ['slug_mi' => 'open-tenders',   'title' => 'Current Open Tenders',  'type' => 'content',  'body' => '<h2>Open Tenders</h2><p>List of currently open tenders.</p>'],
            ['slug_mi' => 'closed-tenders', 'title' => 'Closed Tenders',        'type' => 'content',  'body' => '<h2>Closed Tenders</h2><p>Archive of closed tenders.</p>'],
            // Publications section
            ['slug_mi' => 'annual-reports', 'title' => 'Annual Report 2024',    'type' => 'file',     'body' => null,
                'file_name' => 'annual-report-2024.pdf', 'external_url' => null],
            ['slug_mi' => 'policy-documents','title' => 'National Policy 2024', 'type' => 'content',  'body' => '<h2>National Policy</h2><p>Key policy documents and guidelines.</p>'],
            // Footer links
            ['slug_mi' => 'privacy',        'title' => 'Privacy Policy',        'type' => 'content',  'body' => '<h2>Privacy Policy</h2><p>How we handle your data.</p>'],
            ['slug_mi' => 'terms',          'title' => 'Terms of Service',      'type' => 'content',  'body' => '<h2>Terms</h2><p>Terms and conditions of use.</p>'],
            ['slug_mi' => 'contact-us',     'title' => 'Contact Us',            'type' => 'content',  'body' => '<h2>Contact</h2><p>Email: info@example.gov.in</p>'],
        ];

        foreach ($samples as $s) {
            $mi = MenuItem::where('slug', $s['slug_mi'])->where('is_active', true)->first();
            if (! $mi) {
                $this->command->warn("Skipping [{$s['title']}] — menu_item slug [{$s['slug_mi']}] not found.");
                continue;
            }

            $data = [
                'menu_item_id' => $mi->id,
                'title'        => $s['title'],
                'slug'         => \Illuminate\Support\Str::slug($s['title']),
                'type'         => $s['type'],
                'body'         => $s['body'] ?? null,
                'status'       => 'published',
                'sort_order'   => 1,
                'published_at' => now(),
            ];

            if ($s['type'] === 'file') {
                $data['file_name'] = $s['file_name'] ?? null;
                $data['file_path'] = null; // no real file in seeder
                $data['file_size'] = 0;
                $data['file_mime'] = 'application/pdf';
            }

            Content::updateOrCreate(
                ['menu_item_id' => $mi->id, 'slug' => $data['slug']],
                $data
            );

            $this->command->info("✓ [{$s['title']}] → menu item: {$mi->name}");
        }

        $this->command->info('Done.');
    }
}
