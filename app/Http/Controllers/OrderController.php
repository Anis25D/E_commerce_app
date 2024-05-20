<?php

namespace App\Http\Controllers;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create_order(Request $request)
    {
        DB::beginTransaction();

        try {
            // Create the order
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'total_price' => $request->total_price,
                'status' => 'pending',
                
            ]);

            // Create the associated order items
            $orderItems = $request->order_items; // Assuming order_items is an array of items

            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'prod_qte' => $item['prod_qte'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Order created successfully.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }
    }

    public function getOrdersBySellerId($sellerId)
    {
        $orders = Order::whereHas('orderItems.product', function($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })->with(['orderItems.product' => function($query) {
            $query->select('id', 'name', 'seller_id'); // Include other necessary fields
        }])->get();

        return response()->json([
            'orders' => $orders,
        ], 200);
    }


    public function createOrder(Request $request)
    {
        // Validate the request data
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.prod_qte' => 'required|integer|min:1',
        ]);
    
        // Group order items by seller_id
        $orderItemsBySeller = [];
        foreach ($request->order_items as $item) {
            $product = Product::find($item['product_id']);
            $sellerId = $product->seller_id;
            if (!isset($orderItemsBySeller[$sellerId])) {
                $orderItemsBySeller[$sellerId] = [];
            }
            $orderItemsBySeller[$sellerId][] = [
                'product_id' => $item['product_id'],
                'prod_qte' => $item['prod_qte'],
            ];
        }
    
        // Create an order for each seller
        foreach ($orderItemsBySeller as $sellerId => $items) {
            $order = new Order();
            $order->customer_id = $request->customer_id;
            $order->total_price = 0; // You can calculate total price here
            $order->status = 'pending'; // Or any other default status
            $order->save();
    
            // Add order items to the order
            foreach ($items as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->prod_qte = $item['prod_qte'];
                $orderItem->save();
    
                // Update the total price of the order
                $product = Product::find($item['product_id']);
                $order->total_price += $product->price * $item['prod_qte'];
            }
    
            // Save the total price after adding all items
            $order->save();
        }
    
        return response()->json(['message' => 'Order created successfully'], 201);
    }

    
}
