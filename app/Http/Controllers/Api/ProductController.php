<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Models\Category;

class ProductController extends Controller
{
    public function show()
    {
        $products = Product::with('category')->latest()->paginate(5);
        return response()->json([
            'products' => $products
        ], 200);
    }

    public function getCategories()
    {
        $categories = Category::where('status', 1)->get();
        return response()->json([
            'categories' => $categories
        ],200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'category_id' => 'required|numeric',
            'name' => 'required',
            'code' => 'required',
            'color' => 'required',
            'discount' => 'required|numeric',
            'weight' => 'required|numeric',
            'stock' => 'nullable|numeric',
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $product = new Product();
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->code = $request->code;
        $product->color = $request->color;
        $product->discount = $request->discount;
        $product->weight = $request->weight;
        $product->stock = $request->stock;
        $product->price = $request->price;
        $product->description  = $request->description;
        $image = $request->file('image');
        if ($image){
            $name = uniqid();
            $ext = $image->getClientOriginalExtension();
            $image_name = $name.'.'.$ext;
            $upload_path = public_path('backend/upload/product/'.$image_name);
            Image::make($image)->resize(315,315)->save($upload_path);
            $product->image = $image_name;
        }
        $product->save();
        return response()->json([
            'message' => 'Product Successfully Added.'
        ], 200);
    }

    public function edit($id)
    {
        $product = Product::find($id);
        return response()->json([
            'product' => $product
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'category_id' => 'required|numeric',
            'name' => 'required',
            'code' => 'required',
            'color' => 'required',
            'discount' => 'required|numeric',
            'weight' => 'required|numeric',
            'stock' => 'nullable|numeric',
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $product = Product::find($id);
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->code = $request->code;
        $product->color = $request->color;
        $product->discount = $request->discount;
        $product->weight = $request->weight;
        $product->stock = $request->stock;
        $product->price = $request->price;
        $product->description  = $request->description;
        $image = $request->file('image');
        if ($image){
            $name = uniqid();
            $ext = $image->getClientOriginalExtension();
            $image_name = $name.'.'.$ext;
            $upload_path = public_path('backend/upload/product/'.$image_name);
            Image::make($image)->resize(315,315)->save($upload_path);
            if($product->image != '' &&file_exists(public_path('backend/upload/product/'.$product->image))){
                unlink(public_path('backend/upload/product/'.$product->image));
            }
            $product->image = $image_name;
        }
        $product->save();
        return response()->json([
            'message' => 'Product Successfully Updated.'
        ], 200);

    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if($product->image != '' &&file_exists(public_path('backend/upload/product/'.$product->image))){
            unlink(public_path('backend/upload/product/'.$product->image));
        }
        $product->delete();
        return response()->json([
            'message' => 'Product Successfully Deleted.'
        ],200);
    }

}
