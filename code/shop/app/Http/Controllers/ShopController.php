<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Shop;

class ShopController extends Controller {
    public function syncShop() {
        DB::beginTransaction();
        try {
            Shop::query()->delete();
            $this->syncShopJson();
            $this->syncShopCsv();
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            dump($e->getMessage());
            \Log::error('Error while syncing shop data: '.$e->getMessage());
            return;
        }
    }
    
    /**
     * @throws \Exception
     */
    private function syncShopJson() {
        try {
            $ret = Http::get(env('API_DOMAIN').'/api/v1/shops.json', ['Accept' => 'application/json']);
            if ($ret->failed()) {
                throw new \Exception('Error while fetching shop data from API: ['.$ret->status()."]".$ret->body());
            }
            $json = $ret->json();
            if (!is_array($json)) {
                throw new \Exception('Error while fetching shop data from API: invalid json');
            }
            foreach (array_chunk($json, 1000) as $shops) { //chunk on mysql limit
                Shop::insert($shops);
            }
        }
        catch (\Exception $e) {
            throw new \Exception('Error while syncing shop data: '.$e->getMessage());
        }
    }
    
    private function syncShopCsv() {
        try {
            $ret = Http::get(env('API_DOMAIN').'/shops.csv', ['Accept' => 'text/csv']);
            if ($ret->failed()) {
                throw new \Exception('Error while fetching shop data from API: ['.$ret->status()."]".$ret->body());
            }
            $shops = $ret->body();
            $header = [];
            $data = [];
            foreach (explode("\n", $shops) as $row) { //FIXME: could give memory error if file is too big
               if (empty($row)) {
                    continue;
                }
                $row = str_getcsv($row);
                if (empty($header)) {
                    $header = $row;
                    continue;
                }
                if (count($header) != count($row)) {
                    throw new \Exception('Error while syncing shop data: invalid csv on line '.$row);
                }
                $data[] = array_combine($header, $row);
            }
            
            foreach (array_chunk($data, 1000) as $shops) { //chunk on mysql limit
                Shop::insert($shops);
            }
        }
        catch (\Exception $e) {
            throw new \Exception('Error while syncing shop data: '.$e->getMessage());
        }
    }
}