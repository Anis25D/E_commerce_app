<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    public $timestamps = false;


    protected $fillable = [
        'user_id',
        'birthday',
        'gender',
        'city',
        'address',
        'postalcode',
        'phone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteProducts()
    {
        return $this->hasManyThrough(
            Product::class,
            Favorite::class,
            'customer_id', // Clé étrangère dans la table "favorites"
            'id', // Clé primaire dans la table "products"
            'id', // Clé primaire dans la table "customers"
            'product_id' // Clé étrangère dans la table "favorites"
        );
    }
}