<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cheapshark_game_id',
        'deal_id',
        'title',
        'thumb_url',
        'store_name',
        'normal_price_usd',
        'sale_price_usd',
        'cheapest_ever_usd',
        'target_price_idr',
        'status',
        'specs_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priceLogs()
    {
        return $this->hasMany(PriceLog::class);
    }
}
