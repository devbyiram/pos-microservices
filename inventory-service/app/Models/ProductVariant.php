<?php

namespace App\Models;

use App\Models\Product;
use App\Models\VariantAttribute;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    //
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock_quantity',
        'tax',
        'tax_type',
        'discount',
        'discount_type',
    ];
   public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variantAttributes()
    {
        return $this->hasMany(VariantAttribute::class);
    }
}
