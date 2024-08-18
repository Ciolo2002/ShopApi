<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class syncShop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-shop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download shop data from API and sync with local database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('Syncing shop data');
        (new \App\Http\Controllers\ShopController())->syncShop();
        \Log::info('Syncing shop data done');
    }
}