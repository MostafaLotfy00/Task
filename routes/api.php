<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PriceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
# AUTH MODULE
Route::controller(AuthController::class)->group(function(){
    Route::post('/register','register');
    Route::post('/login','login');
    Route::post('/logout','logout')->middleware('auth:sanctum');
});

# CATEGORY MODULE
Route::controller(CategoryController::class)->group(function(){
    Route::get('/categories', 'index');
    Route::get('/categories/{id}', 'show');
    Route::post('/categories', 'store');
    Route::put('categories/{id}', 'update');
    Route::delete('/categories/{id}', 'destroy');
});
# PRODUCT MODULE
Route::controller(ProductController::class)->group(function(){
    Route::get('/products', 'index');
    Route::get('/products/{id}', 'getProductWithCurrentPrice');
    Route::post('/products', 'store');
    Route::put('/products/{id}', 'update');
    Route::delete('/products/{id}', 'destroy');
});
# PRICE MODULE
Route::apiResource('prices', PriceController::class)->except('create','edit');
# CART MODULE
Route::apiResource('carts', CartController::class)->middleware('auth:sanctum')->except('create','edit');
# ORDER MODULE
Route::apiResource('orders', OrderController::class)->middleware('auth:sanctum')->except('create','edit');


