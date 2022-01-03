<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//使用者權限
use App\Libraries\UserAuth;
//Model
use App\Models\UnshopUser;
use App\Models\UnshopCart;

class OrderController extends Controller
{
    //訂單首頁
    public function index(Request $request) 
    {
        //判斷是否登入
        if(!UserAuth::isLoggedIn()) {
            //使用者登入
            return redirect("users/");
        } else {
            $user_uuid = session("userUuid");
        }
    }

    //購物車
    public function cart(Request $request)
    {
        $assign_data = $datas = array();
        $assign_data["title_txt"] = "購物車";
        //隱藏按鈕-結帳
        $assign_data["btn_none"] = "none";

        $total = 0; //合計

        //判斷是否登入
        if(UserAuth::isLoggedIn()) {
            $user_uuid = session("userUuid");
            $user_id = 0;
            if($user_uuid != "") {
                //使用者資料
                $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
                $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
            }
            $assign_data["user_id"] = $user_id;

            if($user_id > 0) {
                $cart = UnshopCart::where(["user_id" => $user_id])->orderBy("create_time","asc");
                //取得商品ID
                $product_ids = $cart->pluck("product_id")->toArray();
                //$this->pr($product_ids);exit;
                //取得購物車資料
                $cart_datas = $cart->get()->toArray();
                
                //取得商品資料
                $conds = array();
                $conds["id"] = $product_ids;
                $product_datas = $this->getProductData($conds,"serial","asc",false,array(),true);
                //$this->pr($product_datas);

                if(!empty($cart_datas)) {
                    //按鈕-結帳
                    $assign_data["btn_none"] = "";

                    foreach($cart_datas as $cart_data) {
                        //商品ID
                        $product_id = isset($cart_data["product_id"])?$cart_data["product_id"]:0;
                        //商品資料
                        $product_data = isset($product_datas["list_data"][$product_id])?$product_datas["list_data"][$product_id]:array();
                        //購買數量
                        $amount = isset($cart_data["amount"])?$cart_data["amount"]:0;
                        $product_data["amount"] = $amount;
                        //售價
                        $price = 0;
                        $sales = isset($product_data["sales"])?$product_data["sales"]:0; //售價
                        if($sales > 0) {
                            $price = $sales;
                        } else { //原價
                            $price = isset($product_data["price"])?$product_data["price"]:0;
                        }
                        $product_data["price"] = $price;
                        //小計
                        $subtotal = $amount*$price;
                        $product_data["subtotal"] = $subtotal;
                        //合計
                        $total += $subtotal;

                        $datas[] = $product_data;
                    }
                }
            }
        }
        $assign_data["total"] = $total;
        //$this->pr($datas);
        
        return view("orders.cart",["assign_data" => $assign_data,"datas" => $datas]);
    }
}
