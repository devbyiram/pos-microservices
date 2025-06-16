<?php

namespace App\Models;

use App\Models\User;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
     protected $fillable = [
        'store_id',
        'user_id',
        'vendor_id',
        'purchase_date',
        'total_amount',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}

