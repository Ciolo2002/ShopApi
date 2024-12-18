<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OfferController extends Controller {
    public function syncOffer() {
        DB::beginTransaction();
        try {
            Offer::query()->delete();
            $this->syncOfferJson();
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            dump($e->getMessage());
            \Log::error('Error while syncing offer data: '.$e->getMessage());
            return;
        }
    }
    
    private function syncOfferJson() {
        try {
            $ret = Http::get(env('API_DOMAIN').'/api/v1/offers.json', ['Accept' => 'application/json']);
            if ($ret->failed()) {
                throw new \Exception('Error while fetching offer data from API: ['.$ret->status()."]".$ret->body());
            }
            $json = $ret->json();
            if (!is_array($json)) {
                throw new \Exception('Error while fetching offer data from API: invalid json');
            }
            $shop_ids = Shop::distinct()->pluck('id')->toArray();
            foreach ($json as $key => $value) {
                if (!in_array($value['shop_id'], $shop_ids)) {
                    \Log::warning('Shop id '.$value['shop_id'].' not found in shop table');
                    unset($json[$key]);
                }
            }
            
            foreach (array_chunk($json, 1000) as $offers) { //chunk on mysql limit
                Offer::insert($offers);
            }
        }
        catch (\Exception $e) {
            throw new \Exception('Error while syncing offer data: '.$e->getMessage());
        }
    }
    
    public function get(Request $request, $value) {
        try {
            if (empty($value)) {
                return response()->json(['error' => 'Invalid parameter'], 400);
            }
            
            //not properly a best practice to use currency or shop_id in the same parameter
            if (!is_numeric($value)) {
                $data = $this->getByCountryCode($value);
            }
            else {
                $data = $this->getByShopId($value);
            }
            
            return response()->json($data);
        }
        catch (\Exception $e) {
            \Log::error('Error while fetching offer data: '.$e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    
    private function getByCountryCode(string $value): array {
        $ret = Shop::with('offers')->where('country', $value)->get();
        if (empty($ret)) {
            return [];
        }
        $ret = $ret->groupBy('id')->toArray();
        //we know that id is unique so we can avoid the use of subarray generated by groupBy
        foreach ($ret as &$value) {
            $value = $value[0];
        }
        return $ret;
    }
    
    private function getByShopId(string $value): array {
        $ret = Shop::with('offers')->where('id', $value)
                   ->first(); //I cannot sort from mysql since with does not join tables, so I need to sort manually. Use join() instead of with() if you want to sort from mysql
        if (empty($ret)) {
            return [];
        }
        
        //sort ret offers by price
        $ret = $ret->toArray();
        usort($ret['offers'], function ($a, $b) {
            return $b['price'] <=> $a['price'];
        });
        
        return $ret;
    }
    
}