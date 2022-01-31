<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public static function last()
    {
        return self::latest()->first();
    }

    protected $fillable = [
        'order_number',
        'amount',
        'date',
        'state',
        'user_id'
    ];

    public static function updateOrder(string $order, float $deduct, float $add)
    {
        $order = Order::find($order);
        $amount = (float)$order->amount;
        $attr = [
            'amount' => $amount - $deduct + $add
        ];
        $order->update($attr);
    }
}
