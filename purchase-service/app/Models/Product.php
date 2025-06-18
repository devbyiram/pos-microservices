<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'item_code',
        'store_id',
        'user_id',
        'category_id',
        'brand_id',
        'vendor_id',
        'status'
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
