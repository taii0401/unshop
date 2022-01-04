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
        //判斷是否登入
        if(!UserAuth::isLoggedIn()) {
            //使用者登入
            return redirect("users/");
        } else {
            $user_uuid = session("userUuid");
        }

        $assign_data = $datas = array();
        $assign_data["title_txt"] = "購物車";
        //隱藏按鈕-結帳
        $assign_data["btn_none"] = "none";

        $total = 0; //合計

        $user_id = 0;
        if($user_uuid != "") {
            //使用者資料
            $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
            $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
        }
        $assign_data["user_id"] = $user_id;

        if($user_id > 0) {
            //取得購物車資料
            $cart_datas = $this->getCartData(array("user_id" => $user_id),true);
            //合計
            if(isset($cart_datas["total"])) {
                $total = $cart_datas["total"];
                unset($cart_datas["total"]);
            }
            
            $datas = $cart_datas;

            //顯示結帳按鈕
            if(!empty($cart_datas)) {
                $assign_data["btn_none"] = "";
            }
        }
        $assign_data["total"] = $total;
        //$this->pr($datas);
        
        return view("orders.cart",["assign_data" => $assign_data,"datas" => $datas]);
    }

    //購物車-結帳
    public function pay(Request $request)
    {
        //判斷是否登入
        if(!UserAuth::isLoggedIn()) {
            //使用者登入
            return redirect("users/");
        } else {
            $user_uuid = session("userUuid");
        }

        $assign_data = $option_datas = array();
        $user_id = 0;
        if($user_uuid != "") {
            //使用者資料
            $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
            $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
        }
        $assign_data = $unshop_user;
        
        $assign_data["title_txt"] = "付款方式";
        $total = 0;
        if($user_id > 0) {
            //取得購物車資料
            $cart_datas = $this->getCartData(array("user_id" => $user_id),true);
            //合計
            if(isset($cart_datas["total"])) {
                $total = $cart_datas["total"];
                unset($cart_datas["total"]);
            }
            if(empty($cart_datas)) {
                //購物車
                return redirect("orders/cart");
            } else {
                //選項-預設
                $assign_data["send"] = 4;
                $assign_data["payment"] = 6;
                //代碼-配送方式
                $option_datas["send"] = $this->getOptions("code","order_send");
                //代碼-付款方式
                $option_datas["payment"] = $this->getOptions("code","order_pay");
            }
        }
        $assign_data["total"] = $total;
        
        return view("orders.pay",["assign_data" => $assign_data,"option_datas" => $option_datas]);
    }
}
