<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'store_id',
        'user_id',
        'category_id',
        'brand_id',
        'vendor_id',
        'cost_price',
        'sale_price',
        'stock_quantity',
        'status'
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
