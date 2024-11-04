<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'user_id', 'product_id', 'quantity'];

    // Generate UUID for the cart ID
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cart) {
            $cart->id = (string)Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
