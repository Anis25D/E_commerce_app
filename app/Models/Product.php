<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'brand',
        'price',
        'onsale_price',
        'category_id',
        'description',
        'rating',
        'quantity',
        'created_at',
        'purchase_nbr',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function pictures()
    {
        return $this->hasMany(Picture::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }


}
