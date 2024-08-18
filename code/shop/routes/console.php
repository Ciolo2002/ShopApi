<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('app:sync-all', function () {
    \Log::info('Sync All Data');
    Artisan::call('app:sync-shop');
    Artisan::call('app:sync-offer');
    \Log::info('Sync All Data Done');
})->describe('Sync All Data')->hourly();