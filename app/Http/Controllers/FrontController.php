<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Model
use App\Models\UnshopUser;
use App\Models\UnshopProduct;

class FrontController extends Controller
{
    //首頁
    public function index(Request $request)
    {
        //取得顯示資料
        $list_datas = $this->showProductList($request,"index");

        $assign_data = isset($list_datas["assign_data"])?$list_datas["assign_data"]:array();
        $option_datas = isset($list_datas["option_datas"])?$list_datas["option_datas"]:array();
        $datas = isset($list_datas["datas"])?$list_datas["datas"]:array();
        $page_data = isset($list_datas["page_data"])?$list_datas["page_data"]:array();
        
        return view("fronts.index",["assign_data" => $assign_data,"option_datas" => $option_datas,"datas" => $datas,"page_data" => $page_data]);
    }

    //我的頁面
    public function my_page(Request $request,$short_link="") 
    {
        if($short_link == "") {
            //使用者登入
            return redirect("users/");
        }

        //取得使用者ID
        $user_id = 0;
        if($short_link != "") {
            //使用者資料
            $unshop_user = UnshopUser::where(["short_link" => $short_link])->get()->toArray();
            if(!empty($unshop_user) && count($unshop_user) == 1) {
                $user_id = isset($unshop_user[0]["user_id"])?$unshop_user[0]["user_id"]:0;
            }
        }

        //取得顯示資料
        $conds = array();
        $conds["user_id"] = $user_id;
        $conds["short_link"] = $short_link;
        $list_datas = $this->showProductList($request,"my_page",$conds);

        $assign_data = isset($list_datas["assign_data"])?$list_datas["assign_data"]:array();
        $option_datas = isset($list_datas["option_datas"])?$list_datas["option_datas"]:array();
        $datas = isset($list_datas["datas"])?$list_datas["datas"]:array();
        $page_data = isset($list_datas["page_data"])?$list_datas["page_data"]:array();
        
        return view("fronts.index",["assign_data" => $assign_data,"option_datas" => $option_datas,"datas" => $datas,"page_data" => $page_data]);
    }

    //商品檢視
    public function product_view(Request $request)
    {
        $assign_data = array();
        //標題
        $assign_data["title_txt"] = "商品明細";
        //來源
        $source = $request->has("source")?$request->input("source"):"";
        $assign_data["source"] = $source;
        //短網址
        $short_link = $request->has("short_link")?$request->input("short_link"):"";
        $assign_data["short_link"] = $short_link;
        //商品UUID
        $uuid = $request->has("uuid")?$request->input("uuid"):""; 
        $assign_data["uuid"] = $uuid;
        //按鈕-加入購物車
        if($source == "order") { //訂單明細
            $assign_data["btn_none"] = "none";
        }
        //返回
        $assign_data["back_url"] = "/fronts";
        if($source == "cart") { //購物車
            $assign_data["back_url"] = "/orders/cart";
        } else if($source == "order") { //訂單明細
            $order_uuid = $request->has("order_uuid")?$request->input("order_uuid"):""; 
            $assign_data["back_url"] = "/orders/detail?order_uuid=".$order_uuid;
        } else if($source == "my_page" && $short_link != "") { //我的頁面
            $assign_data["back_url"] = "/fronts/my_page/".$short_link;
        }

        if($uuid != "") {
            //取得商品資料
            $cond = array();
            $cond["uuid"] = $uuid;
            $cond["is_delete"] = 0;
            $all_datas = $this->getProductData($cond);
            //資料
            if(isset($all_datas["list_data"])) {
                foreach($all_datas["list_data"] as $list_data) {
                    foreach($list_data as $key => $val) {
                        $assign_data[$key] = $val;
                    }
                }
            } else {
                return redirect("fronts/my_page/".$short_link);
            }
            //$this->pr($assign_data);exit;
            //檔案
            $assign_data["file_datas"] = isset($assign_data["file_datas"])?$assign_data["file_datas"]:array();
        }        
        
        return view("fronts.product_view",["assign_data" => $assign_data]);
    }
}
