<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'items' => 'required|array', // Array of order items
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.product_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Create the order
        $order = Order::create(['user_id' => $data['user_id']]);

        // Create order items
        foreach ($data['items'] as $item) {
            OrderItems::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'product_price' => $item['product_price'],
                'quantity' => $item['quantity'],
            ]);
        }

        return response()->json($order->load('OrderItemss'), 201); // Return the created order with items
    }

    // Retrieve all orders
    public function index()
    {
        $orders = Order::with('OrderItemss')->get();
        return response()->json($orders, 200); // 200 OK
    }

    // Retrieve a specific order
    public function show($id)
    {
        $order = Order::with('OrderItemss')->findOrFail($id);
        return response()->json($order, 200); // 200 OK
    }

    // Update a specific order (optional)
    public function update(Request $request, $id)
    {
        // Implementation for updating an order
    }

    // Delete a specific order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully'], 200); // 200 OK
    }
}
