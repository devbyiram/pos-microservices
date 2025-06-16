<?php

namespace App\Models;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
     protected $fillable = ['name', 'status'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
