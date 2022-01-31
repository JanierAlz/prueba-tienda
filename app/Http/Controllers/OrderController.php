<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Order::all();
    }

    /**
     * Store a newly created resource in storage.
     * the request items should be an array items[0][product], items[0][quantity] 
     * where product is the product id, and quantity is the product quantity
     * the request order_details should be a array of order details Ids
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'array',
            'order_details' => 'array'
        ]);
        if(!isset($request->items, $request->order_details)) {
            return response('Cant create Order on empty items or order_details', 400);
        }
        $lastOrder = Order::last();
        $user = auth()->user()->id;
        $totalDetails = 0;
        $numOrder = isset($lastOrder) ? $lastOrder->id+1 : 1;
        $orderAttr = [
            'order_number' => $numOrder,
            'amount' => 0,
            'date' => Carbon::now()->toDateString(),
            'state' => 'active',
            'user_id' => $user,
        ];
        $order = Order::create($orderAttr);
        if(isset($request->order_details)) {
            foreach($request->order_details as $details) {
                $orderDetails = OrderDetails::find($details);
                $orderDetails->update([
                    'order_id' => $order->id
                ]);
                $totalDetails = $totalDetails + (float) $orderDetails->total;
            }
        }
        if(isset($request->items)) {
            $products = OrderDetails::createOrderDetails($request['items'], $order->id);
        } else {
            $products['total'] = 0;
        }
        $order->update([
            'amount' => (float)$products['total'] + $totalDetails
        ]);
        return response($order);
    }

    /**
     * Display the specified resource.
     * And save a file factura.txt in the public/uploads folder
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orders = DB::table('orders')
                    ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                    ->join('users', 'orders.user_id', '=', 'users.id')
                    ->select('orders.id', 'orders.date', 'orders.amount', 'orders.state', 'order_details.product_id',
                            'order_details.product', 'order_details.price', 'order_details.total','order_details.quantity',
                            'users.name')
                    ->where('orders.id', '=', (string)$id)
                    ->get();
        $items = [];
        $content = '';
        foreach($orders as $order) {
            $items[] =[
                'product_id' => $order->product_id,
                'product' => $order->product,
                'price' => $order->price,
                'quantity' => $order->quantity,
                'total' => $order->total
            ];
            if(isset($response[$order->id])) {
                $response[$order->id]['products'] = $items;
            } else {
                $response[$order->id] = [
                    'date' => $order->date,
                    'order_number' => $order->id,
                    'total' => $order->total,
                    'status' => $order->state,
                    'products' => $items,
                    'productTotal' => $order->amount,
                    'user' => $order->name
                ];
            }
        }
        foreach($response as $order) {
            foreach($order as $key => $values) {
                if($key == 'products') {
                    $item = ''.PHP_EOL;
                    foreach($values as $products) {
                        $item .= 'Product id: '.$products['product_id'].' name: '.$products['product'].' price '.$products['price'].' x '.$products['quantity'].' total= '.$products['total'].PHP_EOL;
                    }
                    $content .= $key.' = '.$item;
                } else {
                    $content .= $key.' : '.$values.PHP_EOL;
                }
            }
        }
        File::put('factura.txt', $content);
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'decimal',
            'state' => 'in:active,paid,void'
        ]);
        $order = Order::find($id);
        $order->update($request->all());
        
        return response($order);
    }

    /**
     * Set a order to void status
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        $order->update([
            'status' => 'void'
        ]);
        return response($order);
    }

    
}
