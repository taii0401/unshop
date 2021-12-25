<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //印出資料
    public function pr($data,$ret=false)
    {
        echo "<pre>";print_r($data,$ret);echo "</pre>";
    }

    //取得數字和字母隨機位數
    public function getRandom($num)
    {
        $ran_str = "";
        for($i = 0;$i < $num;$i++) {
            //定義一個隨機範圍，去猜i的值
            $current = rand(0,$num);
            if($current == $i) {                                
                //生成一個隨機的數字
                $current_code = rand(0,9);
            } else {
                //生成一個隨機的字母
                $current_code = Str::random(1);
            }
            $ran_str .= $current_code;
        }
        return $ran_str;
    }
    
    //分頁
    public function getPage($page_link="",$page=1,$datas)
    {
        $page_data = array();
        //頁面連結
        $page_data["page_link"] = $page_link;
    
        $paginator = $datas->paginate(env("GLOBAL_PAGE_NUM"));
        //資料總數
        $page_data["count"] = $paginator->total();
        //總頁數
        $last_page = $paginator->lastPage();
        $page_data["last_page"] = $last_page;
        //目前頁數
        $page_data["page"] = $page;
        //前一頁的頁碼
        $page_data["previous_page_number"] = 1;
        if($page != 1) {
            $page_data["previous_page_number"] = $page-1;
        }
        //後一頁的頁碼
        $page_data["next_page_number"] = $last_page;
        if($page < $last_page) {
            $page_data["next_page_number"] = $page+1;
        }
        //目前頁面資料
        $list_datas = $paginator->toArray();
        $page_data["list_data"] = isset($list_datas["data"])?$list_datas["data"]:array();
    
        return $page_data;
    }
}
