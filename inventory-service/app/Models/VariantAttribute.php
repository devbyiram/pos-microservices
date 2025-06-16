<?php

namespace App\Models;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    //
    protected $fillable = [
        'name',
        'value',
        'status',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
