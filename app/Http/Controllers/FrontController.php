<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Model
use App\Models\UnshopUser;
use App\Models\UnshopProduct;

class FrontController extends Controller
{
    //我的頁面
    public function my_page(Request $request,$short_link="") 
    {
        if($short_link == "") {
            //使用者登入
            return redirect("users/");
        }

        $assign_data = $conds = array();
        $search_link = "/fronts/my_page/".$short_link;
        $search_get_url = "";
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
                    ${$search_data} = "asc_serial";
                } else {
                    ${$search_data} = "";
                }

                $assign_data[$search_data] = ${$search_data}; //顯示資料
            }
        }

        //顯示資料
        $assign_data["short_link"] = $short_link;
        $assign_data["search_link"] = $search_link;
        $assign_data["search_get_url"] = $search_get_url;

        //選項
        $option_datas = array();
        //排序
        $option_datas["orderby"] = $this->getOptions("product_orderby");
        

        //取得使用者ID
        $user_id = 0;
        if($short_link != "") {
            //使用者資料
            $unshop_user = UnshopUser::where(["short_link" => $short_link])->get()->toArray();
            if(!empty($unshop_user) && count($unshop_user) == 1) {
                $user_id = isset($unshop_user[0]["user_id"])?$unshop_user[0]["user_id"]:0;
            }
        }

        $datas = array();
        if($user_id > 0) {
            //取得商品資料
            $conds["user_id"] = $user_id;
            $conds["is_delete"] = 0;
            $conds["is_display"] = 1;
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

        return view("fronts.my_page",["assign_data" => $assign_data,"option_datas" => $option_datas,"datas" => $datas,"page_data" => $page_data]);
    }

    //商品檢視
    public function product_view(Request $request)
    {
        $assign_data = array();
        //標題
        $assign_data["title_txt"] = "商品明細";
        //短網址
        $short_link = $request->has("short_link")?$request->input("short_link"):"";
        $assign_data["short_link"] = $short_link;
        //商品UUID
        $uuid = $request->has("uuid")?$request->input("uuid"):""; 
        $assign_data["uuid"] = $uuid;

        if($short_link != "" && $uuid != "") {
            //取得商品資料
            $all_datas = $this->getProductData(array("uuid" => $uuid));
            //資料
            //資料
            if(isset($all_datas["list_data"][0])) {
                foreach($all_datas["list_data"][0] as $key => $val) {
                    $assign_data[$key] = $val;
                }
            }
            //$this->pr($assign_data);exit;
            //檔案
            $assign_data["file_datas"] = isset($assign_data["file_datas"])?$assign_data["file_datas"]:array();
        }        
        
        return view("fronts.product_view",["assign_data" => $assign_data]);
    }
}
