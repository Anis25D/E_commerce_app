<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Picture extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
       'product_id',
       'picture'
    ];
   
   
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
