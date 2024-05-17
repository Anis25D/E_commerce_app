<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * Les attributs qui doivent être assignables.
     *
     * @var array
     */
    protected $fillable = [
        // 'seller_id',
        // 'customer_id',
        'sender_id',
        'receiver_id',
        'content',
        'status',

    ];

    /**
     * Obtenez l'utilisateur (vendeur ou client) qui a envoyé le message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }


    /**
     * Obtenez le vendeur associé au message.
     */
    // public function seller()
    // {
    //     return $this->belongsTo(Seller::class);
    // }

    /**
     * Obtenez le client associé au message.
     */
    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }
}
