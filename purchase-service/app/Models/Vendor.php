<?php

namespace App\Models;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'store_id'
    ];


    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
