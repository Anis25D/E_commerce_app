<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'product_id',
        'type',
        'value',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
