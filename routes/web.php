<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

//首頁
Route::get('/', [FrontController::class, 'index']);
Route::get('/fronts', [FrontController::class, 'index']);
//我的頁面
Route::get('/fronts/my_page/{short_link}', [FrontController::class, 'my_page']);
//我的頁面-商品檢視
Route::get('/fronts/product_view', [FrontController::class, 'product_view']);


//登入
Route::get('/users', [UserController::class, 'index']);
//登出
Route::get('/users/logout', [UserController::class, 'logout']);
//忘記密碼
Route::get('/users/forget', [UserController::class, 'forget']);
//新增使用者
Route::get('/users/create', [UserController::class, 'create']);
//編輯使用者
Route::get('/users/edit', [UserController::class, 'edit']);
//編輯使用者密碼
Route::get('/users/edit_password', [UserController::class, 'edit_password']);

//登入-送出
Route::post('/users/login', [UserController::class, 'login'])->name("users.login");


Route::prefix('products')->name('products.')->group(function() {

});

//商品
Route::get('/products', [ProductController::class, 'index']);
//新增商品
Route::get('/products/create', [ProductController::class, 'create']);
//編輯商品
Route::get('/products/edit', [ProductController::class, 'edit']);


//訂單
Route::get('/orders', [OrderController::class, 'index']);
//購物車
Route::get('/orders/cart', [OrderController::class, 'cart']);
//購物車結帳
Route::get('/orders/pay', [OrderController::class, 'pay']);
//訂單明細
Route::get('/orders/detail', [OrderController::class, 'detail']);


//AJAX
$ajaxs = array();
$ajaxs[] = "upload_file"; //檔案-上傳檔案
$ajaxs[] = "upload_file_delete"; //檔案-刪除檔案實際路徑
$ajaxs[] = "user_exist"; //使用者資料-檢查帳號是否存在
$ajaxs[] = "user_link_exist"; //使用者資料-檢查商品頁面網址是否存在
$ajaxs[] = "user_forget"; //使用者資料-忘記密碼
$ajaxs[] = "user_data"; //使用者資料-新增、編輯、刪除
$ajaxs[] = "product_data"; //商品資料-新增、編輯、刪除
$ajaxs[] = "cart_data"; //購物車-新增、編輯、刪除
$ajaxs[] = "order_data"; //訂單-新增、編輯、刪除
foreach($ajaxs as $ajax) {
    Route::post('/ajax/'.$ajax, [AjaxController::class, $ajax]);
}