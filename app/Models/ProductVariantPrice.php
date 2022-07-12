<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $guarded = [];

    public function variantOne(){
        return $this->belongsTo('App\Models\ProductVariant', 'product_variant_one');
    }
    public function variantTwo(){
        return $this->belongsTo('App\Models\ProductVariant', 'product_variant_two');
    }
    public function variantThree(){
        return $this->belongsTo('App\Models\ProductVariant', 'product_variant_three');
    }
}
