<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductAttributeController extends Controller
{

    public function getProduct($id)
    {
        $product = Product::with('attributes')->find($id);
        return response()->json([
            'product' => $product
        ], 200);
    }


    public function store_attribute(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sku.*' => 'required',
            'size.*' => 'required',
            'price.*' => 'required|numeric',
            'stock.*' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        foreach ($request->sku as $key => $sku){
            if (!empty($sku)){

                $sku_count = ProductAttribute::where('sku', $sku)->count();
                if($sku_count > 0){
                    return response()->json([
                       'error' => 'Sku already exist!'
                    ], 422);
                }
                $size_count = ProductAttribute::where('product_id', $id)->where('size', $request->size[$key])->count();
                if($size_count > 0){
                    return response()->json([
                        'error' => 'Size already exist!'
                    ], 422);
                }
                $attribute = new ProductAttribute();
                $attribute->product_id = $id;
                $attribute->sku = $sku;
                $attribute->size = $request->size[$key];
                $attribute->price = $request->price[$key];
                $attribute->stock = $request->stock[$key];
                $attribute->status = 1;
                $attribute->save();
            }
        }

        return response()->json(['message' => 'Product Attribute added.']);

    }


    public function destroy_attribute($id)
    {
        $attribute = ProductAttribute::find($id);
        $attribute->delete();

        return response()->json(['message' => 'Attribute Successfully Deleted']);
    }

    public function edit_attribute($id)
    {
        $attribute = ProductAttribute::find($id);
        return response()->json([
            'attribute' => $attribute
        ], 200);
    }

    public function update_attribute(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required',
            'size' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $attribute = ProductAttribute::find($id);
        $attribute->sku = $request->sku;
        $attribute->size = $request->size;
        $attribute->price = $request->price;
        $attribute->stock = $request->stock;
        $attribute->status = $request->status;
        $attribute->save();
        return response()->json(['message' => 'Product Attribute updated.']);
    }

}
