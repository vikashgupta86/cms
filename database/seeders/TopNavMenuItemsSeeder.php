<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds menu_items for the "Top Nav" menu (menus.id = 1, slug "top-nav"),
 * transcribed from the site's main navbar markup.
 *
 * Run with: php artisan db:seed --class=TopNavMenuItemsSeeder
 */
class TopNavMenuItemsSeeder extends Seeder
{
    /**
     * ID of the "Top Nav" row in the `menus` table.
     */
    private const MENU_ID = 1;

    public function run(): void
    {
        // Make this seeder repeatable: wipe out any previous Top Nav items first.
        DB::table('menu_items')->where('menu_id', self::MENU_ID)->delete();

        $this->insertItems($this->items(), null, 0, '');
    }

    /**
     * Recursively insert a tree of menu items, wiring up parent_id, depth,
     * sort_order, and a materialized `path` (slash-separated ancestor IDs).
     */
    private function insertItems(array $items, ?int $parentId, int $depth, string $parentPath): void
    {
        foreach ($items as $index => $item) {
            $children = $item['children'] ?? [];

            $id = DB::table('menu_items')->insertGetId([
                'menu_id'          => self::MENU_ID,
                'parent_id'        => $parentId,
                'name'             => $item['name'],
                'slug'             => Str::slug($item['name']),
                'description'      => $item['description'] ?? null,
                'type'             => $item['type'] ?? 'link',
                'url'              => $item['url'] ?? null,
                'route_name'       => $item['route_name'] ?? null,
                'route_parameters' => isset($item['route_parameters']) ? json_encode($item['route_parameters']) : null,
                'opens_new_tab'    => $item['opens_new_tab'] ?? false,
                'sort_order'       => $index,
                'depth'            => $depth,
                'path'             => null, // filled in below once $id is known
                'icon'             => $item['icon'] ?? null,
                'badge_text'       => $item['badge_text'] ?? null,
                'badge_color'      => $item['badge_color'] ?? null,
                'css_classes'      => $item['css_classes'] ?? null,
                'html_attributes'  => isset($item['html_attributes']) ? json_encode($item['html_attributes']) : null,
                'permissions'      => isset($item['permissions']) ? json_encode($item['permissions']) : null,
                'roles'            => isset($item['roles']) ? json_encode($item['roles']) : null,
                'is_visible'       => $item['is_visible'] ?? true,
                'is_active'        => $item['is_active'] ?? true,
                'locale'           => $item['locale'] ?? 'en',
                'meta_title'       => $item['meta_title'] ?? null,
                'meta_description' => $item['meta_description'] ?? null,
                'meta_keywords'    => $item['meta_keywords'] ?? null,
                'custom_data'      => isset($item['custom_data']) ? json_encode($item['custom_data']) : null,
                'note'             => $item['note'] ?? null,
                'status'           => $item['status'] ?? 1,
                'created_by'       => null,
                'updated_by'       => null,
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
            ]);

            $path = $parentPath === '' ? (string) $id : $parentPath.'/'.$id;

            DB::table('menu_items')->where('id', $id)->update(['path' => $path]);

            if (! empty($children)) {
                $this->insertItems($children, $id, $depth + 1, $path);
            }
        }
    }

    /**
     * The full Top Nav tree, transcribed from the navbar markup.
     */
    private function items(): array
    {
        return [
            [
                'name' => 'Home',
                'type' => 'link',
                'url'  => '/',
            ],
            [
                'name' => 'About',
                'type' => 'dropdown',
                'children' => [
                    [
                        'name' => 'About Us',
                        'type' => 'link',
                        'url'  => '/home',
                        'children' => [
                            ['name' => 'About Ayush', 'type' => 'link', 'url' => '/about/ayush'],
                            ['name' => 'About Ayurveda', 'type' => 'link', 'url' => '/about/ayurveda'],
                            ['name' => 'About Homoeopathy', 'type' => 'link', 'url' => '/about/homeopathy'],
                            ['name' => 'About Unani', 'type' => 'link', 'url' => '/about/unani'],
                            ['name' => 'About Naturopathy', 'type' => 'link', 'url' => '/about/naturopathy'],
                            [
                                'name' => 'About National Ayush Mission',
                                'type' => 'link',
                                'url'  => '/about/national',
                                'children' => [
                                    ['name' => 'SPMU/DPMU', 'type' => 'link', 'url' => '/about/national'],
                                    ['name' => 'Ayush Services', 'type' => 'link', 'url' => '/about/national'],
                                    ['name' => 'Ayush Educational Institutions', 'type' => 'link', 'url' => '/about/national'],
                                    ['name' => 'Flexi Pool', 'type' => 'link', 'url' => '/about/national'],
                                    ['name' => 'Ayush Health & Wellness Centres', 'type' => 'link', 'url' => '/about/national'],
                                ],
                            ],
                            ['name' => 'Vision & Objectives', 'type' => 'link', 'url' => '/about/objectives'],
                            ['name' => 'Acheivements At A Glance', 'type' => 'link', 'url' => '/about/acheivements'],
                            ['name' => 'Administration', 'type' => 'link', 'url' => '/about/administration'],
                        ],
                    ],
                    [
                        'name' => 'History',
                        'type' => 'link',
                        'url'  => '/about/history',
                        'children' => [
                            ['name' => 'Ayush Department History', 'type' => 'link', 'url' => '/history/departmenthistory'],
                            ['name' => 'Former Commissioners', 'type' => 'link', 'url' => '/history/excommissioners'],
                        ],
                    ],
                    [
                        'name' => 'Who Is Who',
                        'type' => 'dropdown',
                        'children' => [
                            ['name' => 'Directorate', 'type' => 'link', 'url' => '/whoiswho/commissionerate'],
                            ['name' => 'RDDs', 'type' => 'link', 'url' => '/whoiswho/rdds'],
                            ['name' => 'Ayush Educational Institutions', 'type' => 'link', 'url' => '/whoiswho/educationalinstitutions'],
                            ['name' => 'Drugs Control Authority', 'type' => 'link', 'url' => '/whoiswho/drugscontrol'],
                            ['name' => 'Board Of Indian Medicine', 'type' => 'link', 'url' => '/whoiswho/medicineboard'],
                        ],
                    ],
                    [
                        'name' => 'Organogram',
                        'type' => 'dropdown',
                        'children' => [
                            ['name' => 'Ayush Department Organogram', 'type' => 'link', 'url' => '/organogram/departmentorganogram'],
                            ['name' => 'RDD Office', 'type' => 'link', 'url' => '/organogram/rddorganogram'],
                            ['name' => 'Ayush Educational Institutions', 'type' => 'link', 'url' => '/organogram/educationalinstitutionsorganogram'],
                            ['name' => 'Ayush Hospitals', 'type' => 'link', 'url' => '/organogram/hospitalsorganogram'],
                            ['name' => 'Drugs Control Authority', 'type' => 'link', 'url' => '/organogram/drugscontrolorganogram'],
                            ['name' => 'Board Of Indian Medicine', 'type' => 'link', 'url' => '/organogram/medicineboardorganogram'],
                            ['name' => 'Ayush Dispensaries', 'type' => 'link', 'url' => '/organogram/dispensariesorganogram'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Institutions',
                'type' => 'dropdown',
                'children' => [
                    ['name' => 'Ayush Educational Institutions', 'type' => 'link', 'url' => '/institutions/educationalinstitutions'],
                    ['name' => 'Ayush Teaching Hospitals', 'type' => 'link', 'url' => '/institutions/teachinghospitals'],
                    ['name' => '50 Bedded Integrated Ayush Hospitals', 'type' => 'link', 'url' => '/institutions/integratedhospitals'],
                    ['name' => 'Ayush Mini-Hospitals', 'type' => 'link', 'url' => '/institutions/minihospitals'],
                    [
                        'name' => 'Ayush Dispensaries',
                        'type' => 'dropdown',
                        'children' => [
                            ['name' => 'Regular Dispensary', 'type' => 'link', 'url' => '/institutions/regulardispensaries'],
                            ['name' => 'Ayush Speciality Wellness Centres', 'type' => 'link', 'url' => '/institutions/speciality'],
                            ['name' => 'Ayush Health & Wellness Centres', 'type' => 'link', 'url' => '/institutions/wellnesscentres'],
                            ['name' => 'Collocated Ayush Dispensaries', 'type' => 'link', 'url' => '/institutions/collocated'],
                            ['name' => 'Ayush Arogya Mandir', 'type' => 'link', 'url' => '/institutions/standalone'],
                            ['name' => 'NRHM Dispensary', 'type' => 'link', 'url' => '/institutions/nrhmdispensaries'],
                        ],
                    ],
                    ['name' => 'Quality Control Of ASU & H Drugs', 'type' => 'link', 'url' => '/institutions/qualitycontrol'],
                ],
            ],
            [
                'name' => 'Programs',
                'type' => 'dropdown',
                'children' => [
                    ['name' => 'Ayush Public Health Out Reach', 'type' => 'link', 'url' => '/programs/healtreach'],
                    ['name' => 'School Health Program', 'type' => 'link', 'url' => '/programs/schoolhealth'],
                ],
            ],
            [
                'name' => 'Downloads',
                'type' => 'dropdown',
                'children' => [
                    ['name' => 'IEC Materials', 'type' => 'link', 'url' => '/downloads/iecmaterials'],
                    ['name' => 'Forms', 'type' => 'link', 'url' => '/downloads/forms'],
                    ['name' => 'Govt. Orders', 'type' => 'link', 'url' => '/downloads/govtorders'],
                    ['name' => 'Publications', 'type' => 'link', 'url' => '/downloads/publication'],
                    [
                        'name' => 'Guidelines',
                        'type' => 'dropdown',
                        'children' => [
                            ['name' => 'NAM Guidelines', 'type' => 'link', 'url' => '/downloads/namguidelines'],
                            ['name' => 'AHWC Guidelines', 'type' => 'link', 'url' => '/downloads/ahwcguidelines'],
                            ['name' => 'Andhra Board For Ayurveda And Homoeo', 'type' => 'link', 'url' => '/downloads/andhraboard'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Notice Board',
                'type' => 'dropdown',
                'children' => [
                    ['name' => 'Notifications', 'type' => 'link', 'url' => '/noticeboard/notifications'],
                    ['name' => 'Recruitments', 'type' => 'link', 'url' => '/noticeboard/recruitments'],
                    ['name' => 'News', 'type' => 'link', 'url' => '/noticeboard/news'],
                    ['name' => 'Circulars', 'type' => 'link', 'url' => '/noticeboard/circulars'],
                    ['name' => 'Tenders', 'type' => 'link', 'url' => '/noticeboard/tenders'],
                ],
            ],
            [
                'name' => 'Services',
                'type' => 'dropdown',
                'children' => [
                    ['name' => 'RTI', 'type' => 'link', 'url' => '/services/rti'],
                    [
                        'name' => 'E-Services',
                        'type' => 'dropdown',
                        'children' => [
                            ['name' => 'Andhra Board For Ayurveda And Homoeo', 'type' => 'link', 'url' => '/services/medicineboardserv'],
                            ['name' => 'Drugs Control Authority', 'type' => 'link', 'url' => '/services/medicineboardserv'],
                        ],
                    ],
                    [
                        'name' => 'Other Services',
                        'type' => 'dropdown',
                        'children' => [
                            ['name' => 'Grievances', 'type' => 'link', 'url' => '/services/greviences'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Gallery',
                'type' => 'dropdown',
                'children' => [
                    ['name' => 'Gallery', 'type' => 'link', 'url' => '/galary/galary'],
                ],
            ],
            [
                'name' => 'Contact',
                'type' => 'dropdown',
                'children' => [
                    ['name' => 'Suggestions & Feedback', 'type' => 'link', 'url' => '/contact/suggestions'],
                    [
                        'name' => 'Contact',
                        'type' => 'dropdown',
                        'children' => [
                            ['name' => 'Directorate', 'type' => 'link', 'url' => '/contact/contactus'],
                            ['name' => 'RDDs', 'type' => 'link', 'url' => '/contact/contactus'],
                            ['name' => 'Ayush Educational Institutions', 'type' => 'link', 'url' => '/contact/suggestions'],
                            ['name' => 'Drugs Control Authority', 'type' => 'link', 'url' => '/contact/suggestions'],
                            ['name' => 'Board Of Indian Medicine', 'type' => 'link', 'url' => '/contact/suggestions'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Department Login',
                'type' => 'link',
                'url'  => '/login',
            ],
            [
                'name'          => 'Drug License',
                'type'          => 'external',
                'url'           => 'https://noc.ap.nic.in/AYUSHDLS/Login.aspx',
                'opens_new_tab' => true,
            ],
            [
                'name'          => 'Practitioner',
                'type'          => 'external',
                'url'           => 'https://noc.ap.nic.in/AYUSHDLS/Login.aspx',
                'opens_new_tab' => true,
            ],
        ];
    }
}