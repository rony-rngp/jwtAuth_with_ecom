<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    public $appends = [
        'discount_price'
    ];

    public function getDiscountPriceAttribute()
    {
        $product = Product::find($this->product_id);
        if($product->discount > 0){
            return $this->price - ($product->discount/100) * $this->price;
        }else{
            return 0;
        }
    }
}
