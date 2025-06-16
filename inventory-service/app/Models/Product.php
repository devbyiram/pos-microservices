<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = ['store_id', 'user_id', 'name', 'category_id', 'brand_id', 'vendor_id', 'cost_price', 'sale_price', 'stock_quantity', 'status'];
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
