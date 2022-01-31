<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Products;
use Illuminate\Http\Request;

class OrderDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OrderDetails::all();
    }

    /**
     * Store a newly created resource in storage.
     *  Creates a order detail detached from any order
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attr = $request->validate([
            'product' => 'required',
            'quantity' => 'required'
        ]);

        $product = Products::where('id', $attr['product'])->first();

        if(!$product){
            return response([
                'message' => 'Product not found'
            ], 404);
        }

        $details = OrderDetails::create([
            'product_id' => $product->id,
            'product' => $attr['product'],
            'quantity' => $attr['quantity'],
            'price' => $product->price,
            'total' => $product->price * $attr['quantity'],
            'order_id' => ''
        ]);

        return response([
            'message' => 'order created',
            'details' => $details], 201);
    }

    /**
     * Display the specified resource. Based in the order ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orderDetails = OrderDetails::where('order_id', (string)$id)->get();
        return $orderDetails;
    }

    /**
     * Update the specified resource in storage.
     * And updates the related order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product' => 'required',
            'quantity' => 'int',
            'price' => 'decimal'
        ]);
        $details = OrderDetails::find($id);
        $product = Products::find($request->product);
        $quantity = 0;
        $price = 0;
        if(isset($request->quantity)) {
            $quantity = $request->quantity;
        } else {
            $quantity = $details->quantity;
        }
        if(isset($request->price)) {
            $price = $request->price;
        } else {
            $price = $product->price;
        }
        
        
        $attr = [
            'product_id' => $request->product,
            'product' => $product->name,
            'quantity' => (int)$quantity,
            'price' => (float)$price,
            'total' => (float)$price * (float)$quantity
        ];
        $deduct = $details->total;
        $details->update($attr);
        $add = $attr['total'];
        Order::updateOrder($details->order_id, $deduct, $add);
        return response($details);
    }

    /**
     * Remove the specified resource from storage.
     *  If the order amount is 0, the order is therefore void
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = OrderDetails::where('id', $id)->first();
        $order = Order::where('id', $detail->order_id)->first();

        $total = $order->amount - $detail->price;

        if($total == 0) {
            $order->update([
                'status' => 'void'
            ]);
        } else {
            $order->update([
                'amount' => $total
            ]);
        }
        return OrderDetails::destroy($id);

    }
}
