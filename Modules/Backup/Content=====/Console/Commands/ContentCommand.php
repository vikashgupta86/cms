<?php

namespace Modules\Content\Console\Commands;

use Illuminate\Console\Command;

class ContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ContentCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Content Command description';

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
