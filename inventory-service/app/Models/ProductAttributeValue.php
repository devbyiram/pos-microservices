<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $table = 'product_attribute_values';

    protected $fillable = [
        'product_id',
        'attribute_id',
        'value',
    ];

    /**
     * Get the product that owns the attribute value.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant attribute this value refers to.
     */
    public function attribute()
    {
        return $this->belongsTo(VariantAttribute::class, 'attribute_id');
    }
}
