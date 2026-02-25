<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInquiry extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'name',
        'email',
        'message',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
