<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {        
        $this->middleware(['auth:sanctum','admin'])->only(['store','update','destroy']);
    }
    public function index()
    {
        $products= Product::paginate();
        return ApiResponse::sendResponse(200, 'Products Retrieved Successfully', ProductResource::collection($products));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data= $request->validate([
            'name' => 'required|string|unique:products,name|min:3',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|min:3',
            'categories' => 'sometimes|nullable'
        ]);
        $data['image'] = $this->uploadImage($request);
        $product= Product::create($data);
        if(isset($data['categories'])){
            $product->categories()->attach(json_decode($data['categories'], true));
        }
        return ApiResponse::sendResponse(201, 'Product Created Successfully', new ProductResource($product));
    }

    /**
     * Display the specified resource.
     */
    public function getProductWithCurrentPrice($productId)
    {
    $currentDate = now()->toDateString(); // Get the current date

    // Query to get the product with its current price
    $productWithPrice = DB::table('products')
        ->leftJoin('prices', function ($join) use ($currentDate) {
            $join->on('products.id', '=', 'prices.product_id')
                 ->where('prices.start_date', '<=', $currentDate)
                 ->where('prices.end_date', '>=', $currentDate);
        })
        ->where('products.id', $productId) // Filter by product ID
        ->select('products.*', 'prices.price')
        ->first();

    // Check if the product with the current price was found
    if (!$productWithPrice) {
        return ApiResponse::sendResponse(404, 'Product not found or no current price available', null);
    }
    return response()->json($productWithPrice, 200); // Return the product with its current price
    }

 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product= Product::find($id);
        if(!$product){
            return ApiResponse::sendResponse(404, 'product not found', null);
        }
        
        $data= $request->validate([
            'name' => 'sometimes|required|string|min:3|unique:products,name,' . $product->id,
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|min:3'
        ]);

        $old_image= $product->image;
        $data['image']= $this->uploadImage($request);
        if($data['image'] == null){
            unset($data['image']);
        }
        
        $product->update($data);
        if($old_image && isset($data['image'])){
            Storage::disk('uploads')->delete($old_image);
        }
        return ApiResponse::sendResponse(200, 'product updated successfully', new ProductResource($product));


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product= Product::find($id);
        if(!$product){
            return ApiResponse::sendResponse(404, 'Product Not Found', null);
        }
        $product->delete();
        return ApiResponse::sendResponse(200, 'Product Deleted successfully', null);

    }

    protected function uploadImage($request)
    {

        if (!$request->hasFile('image')) {
            return;
        }
        $img = $request->file('image');
        $path = $img->store('products', 'uploads');
        return $path;
    }
}
