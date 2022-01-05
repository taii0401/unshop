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

        $search_link = "orders/";
        $search_get_url = "";

        $assign_data = $conds = array();
        $assign_data["title_txt"] = "訂單管理";

        $user_id = 0;
        if($user_uuid != "") {
            //使用者資料
            $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
            $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
        }
        $conds["user_id"] = $user_id;
        $assign_data["user_id"] = $user_id;

        //選項
        $option_datas = array();
        //排序
        $option_datas["orderby"] = $this->getOptions("order_orderby");

        //取得目前頁數及搜尋條件
        $search_datas = array("page","keywords","orderby");
        foreach($search_datas as $search_data) {
            if($request->has($search_data)) {
                ${$search_data} = $request->input($search_data); //取得搜尋條件的值
                $assign_data[$search_data] = ${$search_data}; //顯示資料
                if(${$search_data} != "") {
                    //搜尋條件
                    if(in_array($search_data,array("keywords"))) {
                        $conds[$search_data] = ${$search_data};
                    }
                    //加入搜尋連結
                    if($search_data != "page") {
                        if($search_get_url == "") {
                            $search_get_url .= "?";
                        } else {
                            $search_get_url .= "&";
                        }
                        $search_get_url .= $search_data."=".${$search_data};
                    }
                }
            } else {
                //預設目前頁數和排序
                if($search_data == "page") {
                    ${$search_data} = 1;
                } else if($search_data == "orderby") {
                    ${$search_data} = "desc_serial";
                } else {
                    ${$search_data} = "";
                }

                $assign_data[$search_data] = ${$search_data}; //顯示資料
            }
        }
        //$this->pr($conds);

        $datas = array();
        //排序
        $orderby_sort = "asc";
        $orderby_col = "serial";
        if(isset($orderby) && $orderby != "") {
            $str = explode("_",$orderby);
            $orderby_sort = isset($str[0])?$str[0]:$orderby_sort;
            $orderby_col = isset($str[1])?str_replace($orderby_sort."_","",$orderby):$orderby_col;
        }
        //分頁條件
        $page_conds = array("search_link" => $search_link,"page" => $page);
        //取得訂單資料
        $all_datas = $this->getOrderData($conds,$orderby_col,$orderby_sort,true,$page_conds,true);
        //分頁資料
        $page_data = isset($all_datas["page_data"])?$all_datas["page_data"]:array();
        //列表資料
        $datas = isset($all_datas["list_data"])?$all_datas["list_data"]:array();
        
        return view("orders.index",["assign_data" => $assign_data,"option_datas" => $option_datas,"datas" => $datas,"page_data" => $page_data]);
    }

    //訂單明細資料
    public function detail(Request $request) 
    {
        //判斷是否登入
        if(!UserAuth::isLoggedIn()) {
            //使用者登入
            return redirect("users/");
        } else {
            $user_uuid = session("userUuid");
        }

        $assign_data = $datas = array();
        $assign_data["title_txt"] = "訂單明細";
        //來源
        $assign_data["source"] = "order";
        //顯示欄位
        $assign_data["order_none"] = "none";
        $assign_data["cart_none"] = "";

        $user_id = 0;
        if($user_uuid != "") {
            //使用者資料
            $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
            $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
        }
        $assign_data["user_id"] = $user_id;

       
        //取得訂單資料
        $conds = array();
        $conds["uuid"] = $request->has("order_uuid")?$request->input("order_uuid"):"";
        $all_datas = $this->getOrderData($conds,"serial","asc",false,array(),true);
        //資料
        if(isset($all_datas["list_data"])) {
            foreach($all_datas["list_data"] as $list_data) {
                foreach($list_data as $key => $val) {
                    $assign_data[$key] = $val;
                }
            }
        }

        //訂單明細資料
        $datas = isset($assign_data["item_datas"])?$assign_data["item_datas"]:array();
        
        return view("orders.data",["assign_data" => $assign_data,"datas" => $datas]);
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
        //來源
        $assign_data["source"] = "cart";
        //顯示欄位
        $assign_data["order_none"] = "";
        $assign_data["cart_none"] = "none";
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
        
        return view("orders.data",["assign_data" => $assign_data,"datas" => $datas]);
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
