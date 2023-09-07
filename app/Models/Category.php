<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image != '' && file_exists(public_path('backend/upload/category/'.$this->image))){
            return asset('backend/upload/category/'.$this->image);
        }else{
            return asset('backend/upload/no_image.png');
        }
    }

}
