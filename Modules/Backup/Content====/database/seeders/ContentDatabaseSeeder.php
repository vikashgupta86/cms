<?php

namespace Modules\Content\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Content\Models\Content;

class ContentDatabaseSeeder extends Seeder
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
         * Contents Seed
         * ------------------
         */

        // DB::table('contents')->truncate();
        // echo "Truncate: contents \n";

        Content::factory()->count(20)->create();
        $rows = Content::all();
        echo " Insert: contents \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
