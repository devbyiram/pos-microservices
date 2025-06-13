<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';

    protected $fillable = ['name', 'status'];
    
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
