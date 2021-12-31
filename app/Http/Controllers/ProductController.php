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

        //取得使用者ID及短網址
        $user_id = 0;
        $short_link = "";
        if($user_uuid != "") {
            //使用者資料
            $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
            $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
            $short_link = isset($unshop_user["short_link"])?$unshop_user["short_link"]:"";
        }

        //取得顯示資料
        $conds = array();
        $conds["user_id"] = $user_id;
        $conds["short_link"] = $short_link;
        $list_datas = $this->showProductList($request,"product",$conds);

        $assign_data = isset($list_datas["assign_data"])?$list_datas["assign_data"]:array();
        $option_datas = isset($list_datas["option_datas"])?$list_datas["option_datas"]:array();
        $datas = isset($list_datas["datas"])?$list_datas["datas"]:array();
        $page_data = isset($list_datas["page_data"])?$list_datas["page_data"]:array();
        
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
