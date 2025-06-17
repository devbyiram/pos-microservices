<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
   protected $fillable = [
    'purchase_id',
    'product_id',
    'quantity',
    'purchase_price',
    'discount',
    'tax',
    'tax_amount',
    'unit_cost',
    'total_cost',
];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}