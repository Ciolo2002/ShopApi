<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class syncOffer extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-offer';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download offer data from API and sync with local database.';
    
    /**
     * Execute the console command.
     */
    public function handle() {
        \Log::info('Syncing offer data');
        (new \App\Http\Controllers\OfferController())->syncOffer();
        \Log::info('Syncing offer data done');
    }
}