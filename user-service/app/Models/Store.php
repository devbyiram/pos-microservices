<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    protected $fillable = [
        'name',
        'status'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_store');
    }
}
