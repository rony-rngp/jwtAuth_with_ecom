<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductAttribute;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function get_products()
    {
        $products = Product::with('category', 'attributes')->where('status', 1)->latest()->get();
        return response()->json([
            'products' => $products
        ], 200);
    }

    public function single_product($id)
    {
        $product = Product::with('category', 'attributes')->where('id', $id)->where('status', 1)->first();
        if($product->attributes->count() > 0){
            $total_stocks = $product->attributes()->where('status', 1)->sum('stock');
        }else{
            $total_stocks = $product->stock;
        }

        if($product->discount > 0){
            $discount_price = $product->price - ($product->discount/100)*$product->price;
        }else{
            $discount_price = 0;
        }
        return response()->json([
            'product' => $product,
            'discount_price' => $discount_price,
            'total_stocks' => $total_stocks
        ], 200);
    }

    public function single_attribute($attr_id)
    {
        $attribute = ProductAttribute::find($attr_id);
        $product = Product::find($attribute->product_id);
        if(@$product->discount > 0 ){
            $discount_price = $attribute->price - (@$product->discount/100)*$attribute->price;
        }else{
            $discount_price = 0;
        }
        return response()->json([
            'attribute' => $attribute,
            'discount_price' => $discount_price,
            'total_stocks' => $attribute->stock
        ], 200);
    }

    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'qty' => 'required|numeric|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $product = Product::with('attributes')->find($request->product_id);
        if($product->attributes->count() > 0){
            if($request->attribute_id == null)
            return response()->json([
                'size' => 'This size filed is required.'
            ], 422);
        }

        $attribute = ProductAttribute::where(['product_id' => $request->product_id, 'id' => $request->attribute_id])->first();
        if(!empty($attribute)){
            if ($attribute->stock < 1){
                return response()->json([
                    'message' => 'This product is out of stock.'
                ], 422);
            }elseif ($attribute->stock < $request->qty){
                return response()->json([
                    'message' => 'Sorry ! Required Quantity is not available.'
                ], 422);
            }
        }else{
            if ($product->stock < 1){
                return response()->json([
                    'message' => 'This product is out of stock.'
                ], 422);
            }elseif ($product->stock < $request->qty){
                return response()->json([
                    'message' => 'Sorry ! Required Quantity is not available.'
                ], 422);
            }
        }

        $contents = Cart::all();
        foreach ($contents as $content){
            if($content->product_attribute_id != null){
                if($request->attribute_id == $content->product_attribute_id && $content->product_id == $request->product_id && $content->admin_id ==  Auth::guard('admin')->user()->id){
                    return response()->json([
                        'message' => 'Sorry ! This att already exists in your cart!.'
                    ], 422);
                }
            }else{
                if($content->product_id == $request->product_id && $content->admin_id ==  Auth::guard('admin')->user()->id){
                    return response()->json([
                        'message' => 'Sorry ! This product already exists in your Cart!.'
                    ], 422);
                }
            }
        }

        $cart = new Cart();
        $cart->admin_id = Auth::guard('admin')->user()->id;
        $cart->product_id = $product->id;
        $cart->product_attribute_id = @$attribute->id;
        $cart->qty = $request->qty;
        $cart->save();

        return response()->json([
            'message' => 'Product has been added in your cart.'
        ], 200);

    }

    public function cart_product_list()
    {
        $cart_products = Cart::with('product', 'attribute')->where('admin_id', Auth::guard('admin')->user()->id)->get();
        $total = 0;
        if($cart_products){
            foreach($cart_products as $cart){
                if($cart->attribute){
                    if($cart->attribute->discount_price > 0){
                        $price = $cart->attribute->discount_price;
                    }else{
                        $price = $cart->attribute->price;
                    }
                }else{
                    if($cart->product->discount_price > 0){
                        $price = $cart->product->discount_price;
                    }else{
                        $price = $cart->price;
                    }
                }
                $total += $price*$cart->qty;
            }
        }

        return response()->json([
            'cart_products' => $cart_products,
            'total' => $total
        ], 200);
    }

    public function destroy_cart_item($id)
    {
        $cart_item = Cart::find($id);
        $cart_item->delete();
        return response()->json([
            'message' => 'Product has been removed in your cart.'
        ], 200);
    }

    public function update_cart_qty(Request $request)
    {
        $cart_item = Cart::find($request->cart_id);
        $new_qty = $cart_item->qty + $request->qty;
        if($new_qty < 1){
            return response()->json([
                'status' => false,
                'message' => 'Minimun quantity 1.'
            ], 200);
        }

        $product_attribute = ProductAttribute::where('id', $cart_item->product_attribute_id)->first();
        if(!empty($product_attribute)){
            if ($new_qty > $product_attribute->stock){
                return response()->json([
                    'status' => false,
                    'message' => 'Product Stock is not available.'
                ], 200);
            }
        }else{
            $product = Product::find($cart_item->product_id);
            if ($new_qty > $product->stock){
                return response()->json([
                    'status' => false,
                    'message' => 'Product Stock is not available.'
                ], 200);
            }
        }

        $cart_item->qty = $new_qty;
        $cart_item->save();
        return response()->json([
            'status' => true,
            'message' => 'Quantity successfully updated.'
        ], 200);
    }

}
