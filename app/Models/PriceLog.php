<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'wishlist_id',
        'recorded_price_usd',
        'exchange_rate_idr',
        'recorded_at',
    ];

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }
}
