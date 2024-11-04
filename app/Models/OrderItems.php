<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'product_id', 'product_name', 'product_price', 'quantity'];

    // Define the relationship with Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Define the relationship with Product (optional)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
