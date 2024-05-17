<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    public $timestamps = false;


    protected $fillable = [
        'user_id',
        'status',
        'birthday',
        'phone',
        'city',
        'location',
    ];

    
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
