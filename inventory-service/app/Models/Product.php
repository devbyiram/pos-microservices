<?php

namespace App\Models;

use App\Models\User;
use App\Models\Brand;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = ['store_id', 'user_id', 'name', 'item_code', 'category_id', 'brand_id', 'vendor_id', 'status'];
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
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function singlevariant(){
        return $this->hasOne(ProductVariant::class);
    }
    public function multiplevariants(){
        return $this->hasMany(ProductVariant::class);
    }
}
