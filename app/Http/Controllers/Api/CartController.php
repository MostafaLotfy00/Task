<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Create the cart item
        $cartItem = Cart::create($data);

        return response()->json($cartItem, 201); // 201 Created
    }

    // Retrieve all cart items
    public function index()
    {
        $carts = Cart::with(['user', 'product'])->get();
        return response()->json($carts, 200); // 200 OK
    }

    // Retrieve a specific cart item
    public function show($id)
    {
        $cartItem = Cart::with(['user', 'product'])->findOrFail($id);
        return response()->json($cartItem, 200); // 200 OK
    }

    // Update a specific cart item
    public function update(Request $request, $id)
    {
        $cartItem = Cart::findOrFail($id);

        // Validate the incoming request
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Update the cart item
        $cartItem->update($data);

        return response()->json($cartItem, 200); // 200 OK
    }

    // Delete a specific cart item
    public function destroy($id)
    {
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Cart item deleted successfully'], 200); // 200 OK
    }
}
