<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Models\District;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use App\Models\ShippingCharge;
use App\Models\DeliveryAddress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function get_delivery_address()
    {
        $delivery_addresses = DeliveryAddress::where('admin_id', Auth::guard('admin')->user()->id)->get();

        return response()->json([
            'delivery_addresses' => $delivery_addresses
        ], 200);
    }

    public function get_country_list()
    {
        $countries = Country::all();
        return response()->json(['countries' => $countries], 200);
    }

    public function get_district_lists()
    {
        $districts = District::all();
        return response()->json(['districts' => $districts], 200);
    }

    public function delivery_address_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country' => 'required',
            'division' => 'required',
            'district' => 'required',
            'zip_code' => 'required|numeric',
            'mobile' => 'required|numeric',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $delivery_address = new DeliveryAddress();
        $delivery_address->admin_id = Auth::guard('admin')->user()->id;
        $delivery_address->name = $request->name;
        $delivery_address->country = $request->country;
        $delivery_address->division = $request->division;
        $delivery_address->district = $request->district;
        $delivery_address->zip_code = $request->zip_code;
        $delivery_address->mobile = $request->mobile;
        $delivery_address->address = $request->address;
        $delivery_address->save();

        return response()->json([
            'message' => 'Delivery address added.'
        ], 200);

    }

    public function get_single_delivery_address($id)
    {
        $delivery_address = DeliveryAddress::find($id);
        if($delivery_address->admin_id == Auth::guard('admin')->user()->id){
            return response()->json(['delivery_address' => $delivery_address], 200);
        }else{
            return response()->json(['message' => 'unauthorized access.'], 401);
        }

    }

    public function delivery_address_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country' => 'required',
            'division' => 'required',
            'district' => 'required',
            'zip_code' => 'required|numeric',
            'mobile' => 'required|numeric',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $delivery_address = DeliveryAddress::find($id);

        if($delivery_address->admin_id != Auth::guard('admin')->user()->id){
            return response()->json(['message' => 'unauthorized access.'], 401);
        }

        $delivery_address->name = $request->name;
        $delivery_address->country = $request->country;
        $delivery_address->division = $request->division;
        $delivery_address->district = $request->district;
        $delivery_address->zip_code = $request->zip_code;
        $delivery_address->mobile = $request->mobile;
        $delivery_address->address = $request->address;
        $delivery_address->save();

        return response()->json([
            'message' => 'Delivery address added.'
        ], 200);
    }

    public function delivery_address_destroy($id)
    {
        $delivery_address = DeliveryAddress::find($id);

        if($delivery_address->admin_id != Auth::guard('admin')->user()->id){
            return response()->json(['message' => 'unauthorized access.'], 401);
        }

        $delivery_address->delete();
        return response()->json(['message' => 'Delivery address deleted.'], 200);
    }

    public function check_shipping_charge($district)
    {

        $cart_products = Cart::with('product')->where('admin_id', Auth::guard('admin')->user()->id)->get();
        $total_weight = 0;
        if($cart_products){
            foreach($cart_products as $cart){
                $product_weight = $cart->product->weight * $cart->qty;
                $total_weight += $product_weight;
            }
        }

        $shipping_charges = ShippingCharge::getShippingCharges($district, $total_weight);
        if($shipping_charges == false){
            return response()->json([
                'status' => false,
                'message' => 'Sorry, Shipping area not found! Please select another area'
            ]);
        }else{
             return response()->json([
                'shipping_charges' => $shipping_charges,
            ], 200);
        }

    }

    public function place_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
            'payment_method' => 'required',
            'shipping_charge' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cart_products = Cart::with('product', 'attribute')->where('admin_id', Auth::guard('admin')->user()->id)->get();
        $total_weight = 0;
        $total_price = 0;
        if($cart_products){
            foreach($cart_products as $cart){
                //check product avilable or not
                if($cart->product == ''){
                    Cart::where('admin_id', Auth::guard('admin')->user()->id)->delete();
                    return response()->json([
                        'refresh' => true,
                        'message' => 'Sorry, Product is not available'
                    ],422);
                }
                //if product stock is available of not
                if($cart->attribute){
                    if ($cart->attribute->stock < $cart->qty){
                        Cart::where('id', $cart->id)->delete();
                        return response()->json([
                            'message' => 'Sorry, Required Quantity is not available'
                        ],422);
                    }
                }else{
                    if ($cart->product->stock < $cart->qty){
                        Cart::where('id', $cart->id)->delete();
                        return response()->json([
                            'message' => 'Sorry, Required Quantity is not available'
                        ],422);
                    }
                }

                //total_weight
                $product_weight = $cart->product->weight * $cart->qty;
                $total_weight += $product_weight;

                //total_price
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
                $total_price += $price*$cart->qty;
            }
        }


        $delivery_address = DeliveryAddress::find($request->address_id);

        $shipping_charges = ShippingCharge::getShippingCharges($delivery_address->district, $total_weight);
        if($shipping_charges == false){
            return response()->json([
                'message' => 'Sorry, Shipping area not found! Please select another area'
            ],422);
        }

        $grand_total = $total_price+$shipping_charges;


        $order = new Order();
        $order->admin_id = Auth::guard('admin')->user()->id;
        $order->name = $delivery_address->name;
        $order->address = $delivery_address->address;
        $order->country = $delivery_address->country;
        $order->division = $delivery_address->division;
        $order->district = $delivery_address->district;
        $order->zip_code = $delivery_address->zip_code;
        $order->mobile = $delivery_address->mobile;
        $order->shipping_charges = $shipping_charges;
        $order->order_status = 'New';
        $order->payment_method = $request->payment_method;
        $order->grand_total = $grand_total;
        $order->save();

        foreach($cart_products as $cart_product){
            $order_details = new OrderDetails();
            $order_details->order_id = $order->id;
            $order_details->product_id = $cart_product->product_id;
            $product = Product::find($cart_product->product_id);
            $order_details->product_code = $product->code;
            $order_details->product_name = $product->name;
            $order_details->product_color = $product->color;
            $order_details->product_size = @$cart_product->attribute->size;
            if($cart_product->attribute){
                if($cart_product->attribute->discount_price > 0){
                    $product_price = $cart_product->attribute->discount_price;
                }else{
                    $product_price = $cart_product->attribute->price;
                }
            }else{
                if($cart_product->product->discount_price > 0){
                    $product_price = $cart_product->product->discount_price;
                }else{
                    $product_price = $cart_product->price;
                }
            }
            $order_details->product_price = $product_price;
            $order_details->product_qty = $cart_product->qty;
            $order_details->save();


            if($cart_product->attribute){
                $cart_product->attribute->stock = $cart_product->attribute->stock - $cart_product->qty;
                $cart_product->attribute->save();
            }else{
                $cart_product->product->stock =  $cart_product->product->stock - $cart_product->qty;
                $cart_product->product->save();
            }

            $cart_product->delete();

        }


        return response()->json([
            'message' => 'Order Successfully Complete.'
        ],200);


    }

    public function order_list()
    {
        $orders = Order::with('order_details')->where('admin_id', Auth::guard('admin')->user()->id)->latest()->get();
        return response()->json([
            'orders' => $orders
        ], 200);
    }

    public function single_order($id)
    {
        $order = Order::with('order_details', 'customer')->find($id);
        if($order->admin_id != Auth::guard('admin')->user()->id){
            return response()->json([
                'message' => 'Unauthorized Access'
            ],422);
        }

        return response()->json([
            'order' => $order
        ], 200);
    }

}
