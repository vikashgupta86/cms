<?php

namespace Modules\ManageHomePage\Console\Commands;

use Illuminate\Console\Command;

class ManageHomePageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ManageHomePageCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ManageHomePage Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
