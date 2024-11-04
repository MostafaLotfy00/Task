<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {        
        $this->middleware(['auth:sanctum','admin'])->only(['store','update','destroy']);
    }
    public function index(){
        $categories= Category::paginate();
       return ApiResponse::sendResponse(200,"Categories retrieved successfully", CategoryResource::collection($categories));
    }

    public function store(Request $req){
        $data= $req->validate([
            'name' => 'string|required|unique:categories,name|min:3',
        ]);
        $category = Category::create($data);
        return ApiResponse::sendResponse(201,"Category Created Successfully", new CategoryResource($category));

    }

    public function show($id){
        $category= Category::find($id);
        if(!$category){
            return ApiResponse::sendResponse(404, 'Category Not Found', null);
        }
        return ApiResponse::sendResponse(200, "Category Retrieved Successfully", new CategoryResource($category));
    }
    
    public function update(Request $req, $id){
        $category= Category::find($id);
        if(!$category){
            return ApiResponse::sendResponse(404, "Category not found", null);
        }
        $data= $req->validate([
            'name' => 'sometimes|required|string|min:3|unique:categories,name,' . $category->id,
        ]);
        $category->update($data);
       return ApiResponse::sendResponse(200, "Category Updated Successfully", new CategoryResource($category));
    }
    public function destroy($id){
        $category= Category::find($id);
        if(!$category){
            return ApiResponse::sendResponse(404, "Category not found", null);
        }
        $category->delete();
       return ApiResponse::sendResponse(200, "Category Deleted Successfully", null);
    }

    protected function uploadImage($request)
    {

        if (!$request->hasFile('img')) {
            return;
        }
        $img = $request->file('img');
        $path = $img->store('categories', 'uploads');
        return $path;
    }
}
