<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'product',
        'quantity',
        'price',
        'total',
        'order_id'
    ];

    public static function createOrderDetails(array $orderDetails, string $order) 
    {
        $return = [
            'notFound' => [],
            'total' => 0
        ];
        foreach($orderDetails as $item) {
            $product = Products::where('id',$item['product'])->first();
            if(!isset($product)){
                $return['notFound'][] = $item['product'];
            } else {
                $price = $product->price * $item['quantity'];
                $return['total'] = $return['total'] + $price;
                OrderDetails::create([
                    'product_id' => $product->id,
                    'product' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $price,
                    'order_id' => $order
                ]);
            }
        }
        return $return;
    }
}
