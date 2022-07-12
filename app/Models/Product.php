<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'id','title', 'sku', 'description'
    ];

    public function variantPrices()
    {
        return $this->hasMany('App\Models\ProductVariantPrice')->with('variantOne', 'variantTwo', 'variantThree');
    }

}
