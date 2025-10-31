<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_number', 'user_id', 'sub_total_amount', 'discount_amount', 'total_amount', 'payment_method', 'payment_status', 'shipping_address', 'shipping_status'];

    protected static function booted()
    {
        static::created(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . now()->format('Ymd') . '-' . $order->id;
                $order->save();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }
}
