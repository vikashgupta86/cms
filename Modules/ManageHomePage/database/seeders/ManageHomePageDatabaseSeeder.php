<?php

namespace Modules\ManageHomePage\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ManageHomePage\Models\ManageHomePage;

class ManageHomePageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /*
         * ManageHomePages Seed
         * ------------------
         */

        // DB::table('managehomepages')->truncate();
        // echo "Truncate: managehomepages \n";

        ManageHomePage::factory()->count(20)->create();
        $rows = ManageHomePage::all();
        echo " Insert: managehomepages \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
