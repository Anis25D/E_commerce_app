<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    /**
     * Les attributs qui doivent être assignables.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'customer_id',
    ];

    /**
     * Obtenez le produit favori associé.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtenez le client associé au favori.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}