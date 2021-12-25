<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UnshopUser;
use App\Models\UnshopProduct;
use Illuminate\Support\Arr;

class FrontController extends Controller
{
    //我的頁面
    public function my_page(Request $request,$short_link="") 
    {
        $assign_data = array();
        $page_link = "/fronts/my_page/".$short_link;
        $search_get_url = "";
        //取得目前頁數及搜尋條件
        $search_datas = array("page","keywords","orderby");
        foreach($search_datas as $search_data) {
            if($request->has($search_data)) {
                ${$search_data} = $request->input($search_data); //取得搜尋條件的值
                $assign_data[$search_data] = ${$search_data}; //顯示資料
                //加入搜尋連結
                if($search_data != "page") {
                    if($search_get_url == "") {
                        $search_get_url .= "?";
                    } else {
                        $search_get_url .= "&";
                    }
                    $search_get_url .= $search_data."=".${$search_data};
                }
            } else {
                //預設目前頁數和排序
                if($search_data == "page") {
                    ${$search_data} = 1;
                } else if($search_data == "orderby") {
                    ${$search_data} = "asc_serial";
                } else {
                    continue;
                }

                $assign_data[$search_data] = ${$search_data}; //顯示資料
            }
        }

        //顯示資料
        $assign_data["short_link"] = $short_link;
        $assign_data["search_link"] = $page_link;
        $assign_data["search_get_url"] = $search_get_url;

        //選項-排序
        $orderby_datas = array();
        $orderby_datas["asc_serial"] = "編號 小 ~ 大";
        $orderby_datas["desc_serial"] = "編號 大 ~ 小";
        $orderby_datas["asc_sales"] = "售價 小 ~ 大";
        $orderby_datas["desc_sales"] = "售價 大 ~ 小";
        

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
            $conds = array();
            $conds["user_id"] = $user_id;
            $conds["is_delete"] = 0;
            $conds["is_display"] = 1;
            $all_datas = UnshopProduct::where($conds);
            //關鍵字
            if(isset($keywords) && $keywords != "") {
                $conds_or = array("name","serial");
                $all_datas = $all_datas->where(function ($query) use($conds_or,$keywords) {
                    foreach($conds_or as $value) {
                        $query->orWhere($value,"like","%".$keywords."%");
                    }
                });
            }
            //排序
            $orderby_sort = "asc";
            $orderby_col = "serial";
            if(isset($orderby) && $orderby != "") {
                $str = explode("_",$orderby);
                $orderby_sort = isset($str[0])?$str[0]:$orderby_sort;
                $orderby_col = isset($str[1])?$str[1]:$orderby_col;
            }
            $all_datas = $all_datas->orderBy($orderby_col,$orderby_sort);
            //print_r($all_datas->toSql());

            //取得分頁
            $page_data = $this->getPage($page_link,$page,$all_datas);
            $page_data["search_get_url"] = str_replace("?","&",$search_get_url);
            //分頁資料
            $list_data = isset($page_data["list_data"])?$page_data["list_data"]:array();
            if(!empty($list_data)) {
                foreach($list_data as $key => $val) {
                    $data = array();
                    $data = $val;
                    //取得圖片(未完成)

                    $datas[] = $data;
                }
            }
        }

        return view("fronts.my_page",["assign_data" => $assign_data,"orderby_datas" => $orderby_datas,"datas" => $datas,"page_data" => $page_data]);
    }

    //商品檢視
    public function product_view(Request $request)
    {
        $assign_data = $file_datas = array();
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
            $product = UnshopProduct::where(["uuid" => $uuid])->first()->toArray();
            if(!empty($product)) {
                foreach($product as $key => $val) {
                    $assign_data[$key] = $val;
                    //取得圖片(未完成)

                    
                }
            }
        }
        
        return view("fronts.product_view",["assign_data" => $assign_data,"file_datas" => $file_datas]);
    }
}
