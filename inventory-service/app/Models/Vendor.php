<?php

namespace App\Models;

use App\Models\Store;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    //
    protected $fillable = ['store_id', 'name', 'email', 'phone', 'address'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
