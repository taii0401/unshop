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
//前台
Route::prefix('fronts')->name('fronts.')->group(function() {
    //首頁
    Route::get('/', [FrontController::class, 'index']);
    //我的頁面
    Route::get('/my_page/{short_link}', [FrontController::class, 'my_page']);
    //我的頁面-商品檢視
    Route::get('/product_view', [FrontController::class, 'product_view']);
});


//會員管理
Route::prefix('users')->name('users.')->group(function() {
    //登入
    Route::get('/', [UserController::class, 'index']);
    //登出
    Route::get('/logout', [UserController::class, 'logout']);
    //忘記密碼
    Route::get('/forget', [UserController::class, 'forget']);
    //新增使用者
    Route::get('/create', [UserController::class, 'create']);
    //編輯使用者
    Route::get('/edit', [UserController::class, 'edit']);
    //編輯使用者密碼
    Route::get('/edit_password', [UserController::class, 'edit_password']);    
});

//登入-送出
Route::post('/users/login', [UserController::class, 'login'])->name("users.login");


//商品管理
Route::prefix('products')->name('products.')->group(function() {
    //商品列表
    Route::get('/', [ProductController::class, 'index']);
    //新增商品
    Route::get('/create', [ProductController::class, 'create']);
    //編輯商品
    Route::get('/edit', [ProductController::class, 'edit']);
});


//訂單管理
Route::prefix('orders')->name('orders.')->group(function() {
    //訂單列表
    Route::get('/', [OrderController::class, 'index']);
    //訂單明細
    Route::get('/detail', [OrderController::class, 'detail']);
    //購物車
    Route::get('/cart', [OrderController::class, 'cart']);
    //購物車-收件人資料
    Route::get('/pay', [OrderController::class, 'pay']);
    //購物車結帳
    Route::get('/pay_check', [OrderController::class, 'pay_check']);
    //購物車結帳-串接金流
    //串接金流-回傳是否成功
    Route::post('/mpg_return', [OrderController::class, 'mpg_return']);
    //串接金流-按鈕觸發是否付款
    Route::post('/notify', [OrderController::class, 'notify']);
    //串接金流-待客戶付款
    Route::post('/customer', [OrderController::class, 'customer']);
     //購物車結帳-結果
    Route::get('/pay_result', [OrderController::class, 'pay_result']);
});



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