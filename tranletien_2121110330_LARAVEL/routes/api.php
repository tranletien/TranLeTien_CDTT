<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\SaleProductController;
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







///
Route::prefix('brand')->group(function () {
    Route::get('index', [BrandController::class, 'index']);
    Route::get('show/{id}', [BrandController::class, 'show']);
    Route::post('store', [BrandController::class, 'store']);
    Route::post('update/{id}', [BrandController::class, 'update']);
    Route::delete('destroy/{id}', [BrandController::class, 'destroy']);
});

Route::prefix('category')->group(function () {
    Route::get('category_list/{parent_id?}', [CategoryController::class, 'category_list']);
    Route::get('index', [CategoryController::class, 'index']);
    Route::get('show/{id}', [CategoryController::class, 'show']);
    Route::post('store', [CategoryController::class, 'store']);
    Route::post('update/{id}', [CategoryController::class, 'update']);
    Route::delete('destroy/{id}', [CategoryController::class, 'destroy']);
    Route::get('trash/{id}', [CategoryController::class, 'trash']);
    Route::get('rescover_trash/{id}', [CategoryController::class, 'RescoverTrash']);
    Route::get('trash', [CategoryController::class, 'getTrashAll']);
});
Route::prefix('product')->group(function () {
    Route::get('index/{limit?}/{page?}', [ProductController::class, 'index']);
    Route::get('show/{id}', [ProductController::class, 'show']);
    Route::post('store', [ProductController::class, 'store']);
    Route::post('update/{id}', [ProductController::class, 'update']);
    Route::delete('destroy/{id}', [ProductController::class, 'destroy']);
    Route::get('product_home/{limit}/{category_id?}', [ProductController::class, 'product_home']);
    Route::get('product_all/{limit}/{page?}', [ProductController::class, 'product_all']);
    Route::get('product_category/{limit}/{category_id}', [ProductController::class, 'product_category']);
    Route::get('product_brand/{brand_id}/{limit}', [ProductController::class, 'product_brand']);
    Route::get('product_detail/{slug}', [ProductController::class, 'product_detail']);
    Route::get('product_other/{id}/{limit}', [ProductController::class, 'product_other']);
    Route::get('search_product/{key}/{limit}/{page}', [ProductController::class, 'search_product']);
    Route::get('compare_product/{id}', [ProductController::class, 'compare_product']);
    Route::get('ProductNew/{sale}/{limit}', [ProductController::class, 'ProductNew']);
    Route::get('trash/{id}', [ProductController::class, 'trash']);
    Route::get('rescover_trash/{id}', [ProductController::class, 'RecoverTrash']);
    Route::get('trash', [ProductController::class, 'getTrashAll']);
});
Route::prefix('menu')->group(function () {
    Route::get('index', [MenuController::class, 'index']);
    Route::get('show/{id}', [MenuController::class, 'show']);
    Route::post('store', [MenuController::class, 'store']);
    Route::post('update/{id}', [MenuController::class, 'update']);
    Route::delete('destroy/{id}', [MenuController::class, 'destroy']);
    Route::get('menu_list/{position}/{parent_id?}', [MenuController::class, 'menu_list']);
    Route::get('trash/{id}', [MenuController::class, 'trash']);
    Route::get('rescover_trash/{id}', [MenuController::class, 'RescoverTrash']);
    Route::get('trash', [MenuController::class, 'getTrashAll']);
});
Route::prefix('contact')->group(function () {
    Route::get('index', [ContactController::class, 'index']);
    Route::get('show/{id}', [ContactController::class, 'show']);
    Route::post('store', [ContactController::class, 'store']);
    Route::post('update/{id}', [ContactController::class, 'update']);
    Route::delete('destroy/{id}', [ContactController::class, 'destroy']);
    Route::post('submit', [ContactController::class, 'submit']);
    Route::post('submitN', [ContactController::class, 'submitN']);
    Route::get('trash/{id}', [ContactController::class, 'trash']);
    Route::get('rescover_trash/{id}', [ContactController::class, 'RescoverTrash']);
    Route::get('trash', [ContactController::class, 'getTrashAll']);
});
Route::prefix('order')->group(function () {
    Route::get('index', [OrderController::class, 'index']);
    Route::get('show/{id}', [OrderController::class, 'show']);
    Route::post('store', [OrderController::class, 'store']);
    Route::post('update/{id}', [OrderController::class, 'update']);
    Route::delete('destroy/{id}', [OrderController::class, 'destroy']);
});
Route::prefix('post')->group(function () {
    Route::get('index/{type}', [PostController::class, 'index']);
    Route::get('getPostFE/{type}', [PostController::class, 'getPostFE']);
    Route::get('show/{id}', [PostController::class, 'show']);
    Route::post('store', [PostController::class, 'store']);
    Route::post('update/{id}', [PostController::class, 'update']);
    Route::delete('destroy/{id}', [PostController::class, 'destroy']);
    Route::get('trash/{id}', [PostController::class, 'trash']);
    Route::get('rescover_trash/{id}', [PostController::class, 'RescoverTrash']);
    Route::get('getTrash/{type}', [PostController::class, 'getTrashAll']);

    Route::get('post_list/{limit}/{type}', [PostController::class, 'post_list']);
    Route::get('post_all/{limit}/{page?}', [PostController::class, 'post_all']);
    Route::get('post_topic/{topic_id}/{limit}/{page?}', [PostController::class, 'post_topic']);
    Route::get('post_detail/{id}', [PostController::class, 'post_detail']);
    Route::get('post_other/{id}/{limit}', [PostController::class, 'post_other']);
});
Route::prefix('slider')->group(function () {
    Route::get('slider_list/{position}', [SliderController::class, 'slider_list']);
    Route::get('index', [SliderController::class, 'index']);
    Route::get('show/{id}', [SliderController::class, 'show']);
    Route::post('store', [SliderController::class, 'store']);
    Route::post('update/{id}', [SliderController::class, 'update']);
    Route::delete('destroy/{id}', [SliderController::class, 'destroy']);
    Route::get('trash/{id}',[SliderController::class,'trash']);
    Route::get('rescover_trash/{id}',[SliderController::class,'RescoverTrash']);
    Route::get('trash',[SliderController::class,'getTrashAll']);

});
Route::prefix('topic')->group(function () {
    Route::get('index', [TopicController::class, 'index']);
    Route::get('show/{id}', [TopicController::class, 'show']);
    Route::post('store', [TopicController::class, 'store']);
    Route::post('update/{id}', [TopicController::class, 'update']);
    Route::delete('destroy/{id}', [TopicController::class, 'destroy']);
    Route::get('trash/{id}', [TopicController::class, 'trash']);
    Route::get('rescover_trash/{id}',[TopicController::class,'RescoverTrash']);
    Route::get('trash',[TopicController::class,'getTrashAll']);

});
Route::prefix('user')->group(function () {
    Route::get('index/{roles}', [UserController::class, 'index']);
    Route::get('show/{id}', [UserController::class, 'show']);
    Route::post('store', [UserController::class, 'store']);
    Route::post('update/{id}', [UserController::class, 'update']);
    Route::delete('destroy/{id}', [UserController::class, 'destroy']);
    Route::post('login', [UserController::class, 'Login']);
    Route::post('adduser', [UserController::class, 'AddUser']);
    Route::get('trash/{id}', [UserController::class, 'trash']);
    Route::get('rescover_trash/{id}', [UserController::class, 'RescoverTrash']);
    Route::get('getTrash/{relos}', [UserController::class, 'getTrashAll']);
});
Route::prefix('SaleProduct')->group(function () {
    Route::get('index', [SaleProductController::class, 'index']);
    Route::get('show/{id}', [SaleProductController::class, 'show']);
    Route::post('store', [SaleProductController::class, 'store']);
    Route::post('update/{id}', [SaleProductController::class, 'update']);
    Route::delete('destroy/{id}', [SaleProductController::class, 'destroy']);
    Route::get('product_sale/{product_id}/{limit}', [SaleProductController::class, 'product_sale']);
});
