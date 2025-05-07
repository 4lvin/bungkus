<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'total_amount', 'status', 'payment_method',
        'payment_status', 'shipping_fullname', 'shipping_address', 'shipping_city',
        'shipping_state', 'shipping_zipcode', 'shipping_phone', 'notes'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
