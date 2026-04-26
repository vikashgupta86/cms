<?php

namespace Modules\Content\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Content\Entities\Content;
use Modules\Menu\Models\Menu;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | NOTE:
            | - We DO NOT create menus
            | - We ONLY use existing menus
            | - Seeder is SAFE (updateOrCreate)
            |--------------------------------------------------------------------------
            */

            // ===============================
            // 1. ABOUT US (Content Type)
            // ===============================
            $aboutMenu = Menu::where('slug', 'about-us')->first();

            if ($aboutMenu) {

                Content::updateOrCreate(
                    [
                        'menu_id' => $aboutMenu->id,
                        'slug'    => 'about-organisation',
                    ],
                    [
                        'title'        => 'About Organisation',
                        'type'         => 'content',
                        'body'         => '<p>This is sample About Organisation content. Replace with real CMS data.</p>',
                        'status'       => 'published',
                        'sort_order'   => 1,
                        'published_at' => now(),
                        'meta_title'   => 'About Organisation',
                    ]
                );
            }


            // ===============================
            // 2. TENDER (File Type)
            // ===============================
            $tenderMenu = Menu::where('slug', 'tenders')->first();

            if ($tenderMenu) {

                Content::updateOrCreate(
                    [
                        'menu_id' => $tenderMenu->id,
                        'slug'    => 'sample-tender-file',
                    ],
                    [
                        'title'        => 'Sample Tender Document',
                        'type'         => 'file',
                        'file_path'    => 'content-files/sample.pdf', // ensure file exists
                        'file_name'    => 'sample.pdf',
                        'file_size'    => 102400,
                        'file_mime'    => 'application/pdf',
                        'status'       => 'published',
                        'sort_order'   => 1,
                        'published_at' => now(),
                        'meta_title'   => 'Tender Document',
                    ]
                );
            }


            // ===============================
            // 3. EXTERNAL LINK (External Type)
            // ===============================
            $noticeMenu = Menu::where('slug', 'notices')->first();

            if ($noticeMenu) {

                Content::updateOrCreate(
                    [
                        'menu_id' => $noticeMenu->id,
                        'slug'    => 'official-website-link',
                    ],
                    [
                        'title'        => 'Visit Official Website',
                        'type'         => 'external',
                        'external_url' => 'https://www.india.gov.in',
                        'status'       => 'published',
                        'sort_order'   => 1,
                        'published_at' => now(),
                        'meta_title'   => 'External Link',
                    ]
                );
            }


            // ===============================
            // 4. DOWNLOADS (Multiple Sample)
            // ===============================
            $downloadMenu = Menu::where('slug', 'downloads')->first();

            if ($downloadMenu) {

                for ($i = 1; $i <= 3; $i++) {

                    Content::updateOrCreate(
                        [
                            'menu_id' => $downloadMenu->id,
                            'slug'    => "sample-download-$i",
                        ],
                        [
                            'title'        => "Sample Download $i",
                            'type'         => 'file',
                            'file_path'    => "content-files/sample$i.pdf",
                            'file_name'    => "sample$i.pdf",
                            'file_size'    => 204800,
                            'file_mime'    => 'application/pdf',
                            'status'       => 'published',
                            'sort_order'   => $i,
                            'published_at' => now(),
                        ]
                    );
                }
            }


            // ===============================
            // 5. SERVICES (Mixed Content)
            // ===============================
            $serviceMenu = Menu::where('slug', 'services')->first();

            if ($serviceMenu) {

                Content::updateOrCreate(
                    [
                        'menu_id' => $serviceMenu->id,
                        'slug'    => 'service-overview',
                    ],
                    [
                        'title'        => 'Service Overview',
                        'type'         => 'content',
                        'body'         => '<p>Details about services provided by department.</p>',
                        'status'       => 'published',
                        'sort_order'   => 1,
                        'published_at' => now(),
                    ]
                );

                Content::updateOrCreate(
                    [
                        'menu_id' => $serviceMenu->id,
                        'slug'    => 'service-guidelines',
                    ],
                    [
                        'title'        => 'Service Guidelines PDF',
                        'type'         => 'file',
                        'file_path'    => 'content-files/service-guidelines.pdf',
                        'file_name'    => 'service-guidelines.pdf',
                        'file_size'    => 304800,
                        'file_mime'    => 'application/pdf',
                        'status'       => 'published',
                        'sort_order'   => 2,
                        'published_at' => now(),
                    ]
                );
            }

        });
    }
}