<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected array $fillable = ['id', 'name', 'address', 'country'];
    use HasFactory;
    
    public function offers(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(Offer::class);
    }
}