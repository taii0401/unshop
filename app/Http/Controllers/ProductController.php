<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//使用者權限
use App\Libraries\UserAuth;
//Model
use App\Models\UnshopUser;
use App\Models\UnshopProduct;

class ProductController extends Controller
{
    //商品首頁
    public function index(Request $request) 
    {
        //判斷是否登入
        if(!UserAuth::isLoggedIn()) {
            //使用者登入
            return redirect("users/");
        } else {
            $user_uuid = session("userUuid");
        }

        $assign_data = $conds = array();
        $search_link = "/products";
        $search_get_url = "";
        //取得目前頁數及搜尋條件
        $search_datas = array("page","keywords","types","is_display","orderby");
        foreach($search_datas as $search_data) {
            if($request->has($search_data)) {
                ${$search_data} = $request->input($search_data); //取得搜尋條件的值
                $assign_data[$search_data] = ${$search_data}; //顯示資料

                if(${$search_data} != "") {
                    //搜尋條件
                    if(in_array($search_data,array("keywords","types","is_display"))) {
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
                    ${$search_data} = "asc_serial";
                } else {
                    ${$search_data} = "";
                }

                $assign_data[$search_data] = ${$search_data}; //顯示資料
            }
        }

        //顯示資料
        $assign_data["search_link"] = $search_link;
        $assign_data["search_get_url"] = $search_get_url;

        //選項
        $option_datas = array();
        //排序
        $option_datas["orderby"] = $this->getOptions("product_orderby");
        //是否顯示
        $option_datas["is_display"] = $this->getOptions("product_is_display");
        //代碼-類別
        $option_datas["types"] = $this->getOptions("code","product_category",true);


        //取得使用者ID及短網址
        $user_id = 0;
        $short_link = "";
        if($user_uuid != "") {
            //使用者資料
            $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
            $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
            $short_link = isset($unshop_user["short_link"])?$unshop_user["short_link"]:"";
        }
        $assign_data["user_id"] = $user_id;
        $assign_data["short_link"] = $short_link;

        $datas = array();
        if($user_id > 0) {
            //搜尋條件
            $conds["user_id"] = $user_id;
            $conds["is_delete"] = 0;
            //排序
            $orderby_sort = "asc";
            $orderby_col = "serial";
            if(isset($orderby) && $orderby != "") {
                $str = explode("_",$orderby);
                $orderby_sort = isset($str[0])?$str[0]:$orderby_sort;
                $orderby_col = isset($str[1])?$str[1]:$orderby_col;
            }
            //分頁條件
            $page_conds = array("search_link" => $search_link,"page" => $page);
            //取得商品資料
            $all_datas = $this->getProductData($conds,$orderby_col,$orderby_sort,true,$page_conds);
            //分頁資料
            $page_data = isset($all_datas["page_data"])?$all_datas["page_data"]:array();
            //列表資料
            $datas = isset($all_datas["list_data"])?$all_datas["list_data"]:array();
        }
        
        return view("products.index",["assign_data" => $assign_data,"option_datas" => $option_datas,"datas" => $datas,"page_data" => $page_data]);
    }

    //新增商品
    public function create()
    {
        //判斷是否登入
        if(!UserAuth::isLoggedIn()) {
            //使用者登入
            return redirect("users/");
        }
        $data = $this->get_data("add");
        return view("products.data",["assign_data" => $data]);
    }

    //編輯商品
    public function edit(Request $request)
    {
        //判斷是否登入
        if(!UserAuth::isLoggedIn()) {
            //使用者登入
            return redirect("users/");
        }
        $uuid = $request->has("uuid")?$request->input("uuid"):"";
        $data = $this->get_data("edit",$uuid);
        //檔案
        $file_datas = isset($data["file_datas"])?$data["file_datas"]:array();
        if(!isset($data["uuid"])) { //無法取得資料
            return redirect("products");
        }
        return view("products.data",["assign_data" => $data,"file_datas" => $file_datas]);
    }

    //共用data樣板-新增商品、編輯商品
    public function get_data($action_type="add",$uuid="")
    {
        $data = array();
        $data["action_type"] = $action_type;

        $user_uuid = session("userUuid");
        $user_id = 0;
        if($user_uuid != "") {
            //使用者資料
            $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
            $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
        }
        $data["user_id"] = $user_id;

        //隱藏按鈕-刪除帳號
        $data["btn_none"] = "none";
        //是否顯示
        $data["is_display_checked"] = "";
        
        if($action_type == "add") { //新增商品
            $data["title_txt"] = "新增商品";
            //是否顯示-預設是
            $data["is_display_checked"] = "checked";
        } else if($action_type == "edit") { //編輯商品
            $data["title_txt"] = "編輯商品";
            $data["btn_none"] = "";

            //取得商品資料
            $conds = array();
            $conds["uuid"] = $uuid;
            $conds["user_id"] = $user_id;
            $conds["is_delete"] = 0;
            $all_datas = $this->getProductData($conds);
            //資料
            if(isset($all_datas["list_data"][0])) {
                foreach($all_datas["list_data"][0] as $key => $val) {
                    $data[$key] = $val;
                }
            }
            //$this->pr($data);exit;

            //是否顯示
            if(isset($data["is_display"]) && $data["is_display"] == 1) {
                $data["is_display_checked"] = "checked";
            }
        }

        return $data;
    }
}
