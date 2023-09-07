<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('get_products', [\App\Http\Controllers\Api\Frontend\HomeController::class, 'get_products']);
Route::get('get_single_product/{id}', [\App\Http\Controllers\Api\Frontend\HomeController::class, 'single_product']);
Route::get('get_single_attribute/{attr_id}', [\App\Http\Controllers\Api\Frontend\HomeController::class, 'single_attribute']);

Route::group(['middleware' => 'admin'], function () {
    Route::post('add_to_cart', [\App\Http\Controllers\Api\Frontend\HomeController::class, 'add_to_cart']);
    Route::get('cart_product_list', [\App\Http\Controllers\Api\Frontend\HomeController::class, 'cart_product_list']);
    Route::get('destroy_cart_item/{id}', [\App\Http\Controllers\Api\Frontend\HomeController::class, 'destroy_cart_item']);
    Route::post('update_cart_qty', [\App\Http\Controllers\Api\Frontend\HomeController::class, 'update_cart_qty']);

    Route::get('get_delivery_address', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'get_delivery_address']);
    Route::get('get_single_delivery_address/{id}', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'get_single_delivery_address']);


    Route::get('get_country_list', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'get_country_list']);
    Route::get('get_district_list', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'get_district_lists']);

    Route::post('delivery_address_store', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'delivery_address_store']);
    Route::post('delivery_address_update/{id}', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'delivery_address_update']);
    Route::post('delivery_address_destroy/{id}', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'delivery_address_destroy']);
    Route::get('check_shipping_charge/{district}', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'check_shipping_charge']);

    Route::post('place_order', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'place_order']);

    Route::get('order_list', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'order_list']);

    Route::get('single_order/{id}', [\App\Http\Controllers\Api\Frontend\CheckoutController::class, 'single_order']);


});

//backend routes
Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('refresh', [\App\Http\Controllers\Api\AuthController::class, 'refresh']);

Route::group(['middleware' => 'admin'], function () {
    Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    Route::post('user', [\App\Http\Controllers\Api\AuthController::class, 'user']);

    Route::group(['prefix'=>'category'], function(){
        Route::get('/list', [App\Http\Controllers\Api\CategoryController::class, 'show']);
        Route::post('/store', [App\Http\Controllers\Api\CategoryController::class, 'store']);
        Route::get('/edit/{id}', [App\Http\Controllers\Api\CategoryController::class, 'edit']);
        Route::post('/update/{id}', [App\Http\Controllers\Api\CategoryController::class, 'update']);
        Route::post('/destroy/{id}', [App\Http\Controllers\Api\CategoryController::class, 'destroy']);
    });

    Route::group(['prefix'=>'product'], function(){
        Route::get('/list', [App\Http\Controllers\Api\ProductController::class, 'show']);
        Route::get('/get_categories', [App\Http\Controllers\Api\ProductController::class, 'getCategories']);
        Route::post('/store', [App\Http\Controllers\Api\ProductController::class, 'store']);
        Route::get('/edit/{id}', [App\Http\Controllers\Api\ProductController::class, 'edit']);
        Route::post('/update/{id}', [App\Http\Controllers\Api\ProductController::class, 'update']);
        Route::post('/destroy/{id}', [App\Http\Controllers\Api\ProductController::class, 'destroy']);

        Route::post('/attribute/store/{id}', [App\Http\Controllers\Api\ProductAttributeController::class, 'store_attribute']);

         Route::get('/attribute/{id}', [App\Http\Controllers\Api\ProductAttributeController::class, 'getProduct']);
         Route::post('/attribute/destroy/{id}', [App\Http\Controllers\Api\ProductAttributeController::class, 'destroy_attribute']);
         Route::get('/attribute/edit/{id}', [App\Http\Controllers\Api\ProductAttributeController::class, 'edit_attribute']);
         Route::post('/attribute/update/{id}', [App\Http\Controllers\Api\ProductAttributeController::class, 'update_attribute']);

    });


});


