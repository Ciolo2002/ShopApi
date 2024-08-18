<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected array $fillable = ['id', 'shop_id', 'product', 'price', 'currency','description'];
    use HasFactory;
    
    public function shop(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Shop::class);
    }
}