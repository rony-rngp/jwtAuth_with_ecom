<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    public $appends = [
        'image_url', 'discount_price'
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image != '' && file_exists(public_path('backend/upload/product/'.$this->image))){
            return asset('backend/upload/product/'.$this->image);
        }else{
            return asset('backend/upload/no_image.png');
        }
    }

    public function getDiscountPriceAttribute()
    {
        if($this->discount > 0){
            return $this->price - ($this->discount/100) * $this->price;
        }else{
            return 0;
        }
    }

    public function category()
    {
        return $this->belongsTo(category::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

}
