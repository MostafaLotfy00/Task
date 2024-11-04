<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PriceResource;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  
    // Retrieve all prices
    public function index()
    {
        $prices = Price::paginate();
        return ApiResponse::sendResponse(200, 'Prices Retrieved Successfully',PriceResource::collection($prices));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'price' => 'required|numeric',
        ]);

        // Create the price
        $price = Price::create($data);

        return ApiResponse::sendResponse(201, 'Price Created Successfully', new PriceResource($price)); 
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $price = Price::findOrFail($id);
        return ApiResponse::sendResponse(200, 'Price Retrieved Successfully', new PriceResource($price)); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $price = Price::findOrFail($id);

        // Validate the incoming request
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'price' => 'required|numeric',
        ]);

        // Update the price
        $price->update($data);

        return ApiResponse::sendResponse(200, 'Price Updated Successfully', new PriceResource($price)); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $price = Price::findOrFail($id);
        $price->delete();

        return ApiResponse::sendResponse(200, 'Price Deleted Successfully', null); 
    }
}
