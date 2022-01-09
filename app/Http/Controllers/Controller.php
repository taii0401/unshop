<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

//字串-隨機產生亂碼
use Illuminate\Support\Str;
//例外處理
use Illuminate\Database\QueryException;
//上傳檔案
use Illuminate\Support\Facades\Storage;
//DB
use Illuminate\Support\Facades\DB;
//Model
use App\Models\UnshopCode;
use App\Models\UnshopFile;
use App\Models\UnshopFileData;
use App\Models\UnshopProduct;
use App\Models\UnshopCart;
use App\Models\UnshopOrder;
use App\Models\UnshopOrderItem;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 印出資料
     * @param  data：資料
     * @param  page：是否轉換
     * @return echo
     */
    public function pr($data,$ret=false)
    {
        echo "<pre>";print_r($data,$ret);echo "</pre>";
    }

    /**
     * 取得資料(unshop_code、unshop_file)
     * @param  type：型態-code、file
     * @param  cond：搜尋條件
     * @param  return_col：回傳資料的欄位
     * @return array
     */
    public function getData($type="",$cond=array(),$return_col="")
    {
        $data = $get_datas = array();

        if($type == "code") { //代碼
            $get_datas = UnshopCode::where($cond)->get()->toArray();
        } else if($type == "file") { //檔案
            $get_datas = UnshopFile::where($cond)->get()->toArray();
        }

        if(!empty($get_datas)) {
            foreach($get_datas as $get_data) {
                if($type == "code") { //代碼
                    //code
                    $id = isset($get_data["code"])?$get_data["code"]:"";
                } else {
                    //ID
                    $id = isset($get_data["id"])?$get_data["id"]:"";
                }
                
                
                if($id != "") {
                    if($return_col != "") {
                        $data[$id] = isset($get_data[$return_col])?$get_data[$return_col]:"";
                    } else {
                        $data[$id] = $get_data;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * 取得代碼資料名稱(unshop_code)
     * @param  type：型態
     * @return array
     */
    public function getCodeNames($type="")
    {
        $cond = array();
        $cond["types"] = $type;
        $data = $this->getData("code",$cond,"cname");

        return $data;
    }

    /**
     * 取得數字和字母隨機位數
     * @param  num：隨機產生的位數
     * @return string
     */
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
    
    /**
     * 分頁
     * @param  page_link：頁面連結
     * @param  page：目前頁數
     * @param  datas：需轉換分頁的資料
     * @return array
     */
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

    /**
     * 選項項目
     * @param  type：選項類別
     * @param  code_type：從unshop_code資料表而來-代碼類別
     * @param  is_all：代碼類別選項是否加上全部
     * @return array
     */
    public function getOptions($type="",$code_type="",$is_all=false)
    {
        $data = array();
        switch($type) {
            case "code": //代碼
                $conds = array();
                $conds["types"] = $code_type;
                $conds["is_display"] = 1;
                $conds["is_delete"] = 0;
                $code_datas = $this->getData("code",$conds,"cname");

                if($is_all) {
                    $data[""] = "全部";
                }

                if(!empty($code_datas)) {
                    foreach($code_datas as $key => $val) {
                        $data[$key] = $val;
                    }
                }
                break;
            case "product_is_display": //是否顯示
                $data[""] = "全部";
                $data[1] = "是";
                $data[0] = "否";
                break;
            case "product_orderby": //商品排序
                $data["asc_serial"] = "編號 小 ~ 大";
                $data["desc_serial"] = "編號 大 ~ 小";
                $data["asc_sales"] = "售價 小 ~ 大";
                $data["desc_sales"] = "售價 大 ~ 小";
                break;
            case "order_status": //訂單狀態
                $data[""] = "全部";
                $data[0] = "處理中";
                $data[1] = "已付款";
                $data[2] = "已寄送";
                $data[3] = "已取消";
                break;
            case "order_orderby": //訂單排序
                $data["asc_serial"] = "編號 小 ~ 大";
                $data["desc_serial"] = "編號 大 ~ 小";
                $data["asc_create_time"] = "日期 小 ~ 大";
                $data["desc_create_time"] = "日期 大 ~ 小";
                break;
        }

        return $data;
    }

    /**
     * 取得商品資料(unshop_product)
     * @param  cond：搜尋條件
     * @param  orderby：排序欄位
     * @param  sort：排序-遞增、遞減
     * @param  is_page：是否分頁
     * @param  page_cond：分頁條件
     * @param  is_one：圖片是否只取一張
     * @return array
     */
    public function getProductData($cond=array(),$orderby="serial",$sort="asc",$is_page=false,$page_cond=array(),$is_one=false)
    {
        $datas = $all_datas = array();

        //取得商品資料
        $all_datas = UnshopProduct::getProduct($cond,$orderby,$sort);

        //取得分頁
        if($is_page) {
            $search_link = isset($page_cond["search_link"])?$page_cond["search_link"]:"";
            $page = isset($page_cond["page"])?$page_cond["page"]:1;
            //分頁資料
            $page_data = $this->getPage($search_link,$page,$all_datas);
            $datas["page_data"] = $page_data;
            $list_data = isset($page_data["list_data"])?$page_data["list_data"]:array();
        } else {
            $list_data = $all_datas->get()->toArray();
        }
        //$this->pr($list_data);exit;
        
        if(!empty($list_data)) {
            //選項
            $option_datas = array();
            //是否顯示
            $option_datas["is_display"] = $this->getOptions("product_is_display");
            //代碼-類別
            $option_datas["types"] = $this->getCodeNames("product_category");

            foreach($list_data as $key => $val) {
                $data = array();
                $data = $val;
                $id = isset($val["id"])?$val["id"]:0;
                //轉換名稱-類別
                $data["types_name"] = isset($option_datas["types"][$data["types"]])?$option_datas["types"][$data["types"]]:"";
                //轉換名稱-是否顯示
                $data["is_display_name"] = isset($option_datas["is_display"][$data["is_display"]])?$option_datas["is_display"][$data["is_display"]]:"";

                //取得檔案
                $conds_file = array();
                $conds_file["data_id"] = $id;
                $conds_file["data_type"] = "product";
                $file_datas = UnshopFileData::getFileData($conds_file,true);
                //$this->pr($file_datas);
                
                //列表-只取一張圖片
                $data["file_path"] = "";
                if($is_one && !empty($file_datas)) {
                    foreach($file_datas as $file_data) {
                        if($data["file_path"] == "" && isset($file_data["path"]) && $file_data["path"] != "") {
                            $data["file_path"] = $file_data["path"];
                            $data["file_url"] = asset(Storage::url($file_data["path"]));
                        }
                    }
                }

                $datas["list_data"][$id] = $data;
                $datas["list_data"][$id]["file_datas"] = $file_datas;
            }
        }
        //$this->pr($datas);

        return $datas;
    }

    /**
     * 商品資料列表(unshop_product)
     * @param  request
     * @param  type：product-後台商品列表、index-前台首頁、my_page-前台我的頁面
     * @param  cond：搜尋條件
     * @return array
     */
    public function showProductList($request,$type="index",$cond=array())
    {
        $assign_data = $conds = array();
        //選項
        $option_datas = array();
        //排序
        $option_datas["orderby"] = $this->getOptions("product_orderby");

        $search_link = $search_get_url = "";
        $short_link = isset($cond["short_link"])?$cond["short_link"]:"";
        $user_id = isset($cond["user_id"])?$cond["user_id"]:0;

        if($type == "index") { //前台首頁
            $assign_data["title_txt"] = "首頁";
            $assign_data["source"] = "index";
            $search_link = "/fronts";
            $search_datas = array("page","keywords","orderby");
            
            //搜尋條件
            $conds["is_display"] = 1;
        } else if($type == "my_page") { //前台我的頁面
            $assign_data["title_txt"] = "我的頁面";
            $assign_data["source"] = "my_page";
            $search_link = "/fronts/my_page/".$short_link;
            $search_datas = array("page","keywords","orderby");

            //搜尋條件
            $conds["user_id"] = $user_id;
            $conds["is_delete"] = 0;
            $conds["is_display"] = 1;
        } else if($type == "product") { //後台商品列表
            $search_link = "/products";
            $search_datas = array("page","keywords","types","is_display","orderby");

            //選項
            //是否顯示
            $option_datas["is_display"] = $this->getOptions("product_is_display");
            //代碼-類別
            $option_datas["types"] = $this->getOptions("code","product_category",true);

            //搜尋條件
            $conds["user_id"] = $user_id;
            $conds["is_delete"] = 0;
        }

        //取得目前頁數及搜尋條件
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
        //$this->pr($conds);

        //顯示資料
        $assign_data["short_link"] = $short_link;
        $assign_data["search_get_url"] = $search_get_url;

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
        //取得商品資料
        $all_datas = $this->getProductData($conds,$orderby_col,$orderby_sort,true,$page_conds,true);
        //分頁資料
        $page_data = isset($all_datas["page_data"])?$all_datas["page_data"]:array();
        //列表資料
        $datas = isset($all_datas["list_data"])?$all_datas["list_data"]:array();

        $return_datas = array();
        $return_datas["assign_data"] = $assign_data;
        $return_datas["option_datas"] = $option_datas;
        $return_datas["datas"] = $datas;
        $return_datas["page_data"] = $page_data;

        return $return_datas;
    }

    /**
     * 取得購物車資料(unshop_cart)
     * @param  cond：搜尋條件
     * @param  is_total：是否計算合計
     * @return array
     */
    public function getCartData($cond=array(),$is_total=false)
    {
        $datas = $all_datas = array();

        //取得購物車資料
        $all_datas = UnshopCart::getCart($cond);
        //print_r($all_datas->toSql());

        //取得商品ID
        $product_ids = $all_datas->pluck("product_id")->toArray();
        //$this->pr($product_ids);exit;
        //取得購物車資料
        $cart_datas = $all_datas->get()->toArray();
            
        //取得商品資料
        $conds = array();
        $conds["id"] = $product_ids;
        $product_datas = $this->getProductData($conds,"serial","asc",false,array(),true);
        //$this->pr($product_datas);

        //合計
        $total = 0;
        if(!empty($cart_datas)) {
            foreach($cart_datas as $cart_data) {
                //商品ID
                $product_id = isset($cart_data["product_id"])?$cart_data["product_id"]:0;
                //商品資料
                $product_data = isset($product_datas["list_data"][$product_id])?$product_datas["list_data"][$product_id]:array();
                //商品明細連結
                $product_data["product_link"] = "/fronts/product_view?source=cart&uuid=".$product_data["uuid"];
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

        //合計
        if($is_total) {
            $datas["total"] = $total;
        }

        //$this->pr($datas);
        return $datas;
    }

    /**
     * 取得訂單資料(unshop_order)
     * @param  cond：搜尋條件
     * @param  orderby：排序欄位
     * @param  sort：排序-遞增、遞減
     * @param  is_page：是否分頁
     * @param  page_cond：分頁條件
     * @param  is_detail：是否取得訂單詳細資料
     * @return array
     */
    public function getOrderData($cond=array(),$orderby="serial",$sort="asc",$is_page=false,$page_cond=array(),$is_detail=false)
    {
        $datas = $all_datas = array();
        
        //取得訂單資料
        $all_datas = UnshopOrder::getOrder($cond,$orderby,$sort);

        //取得分頁
        if($is_page) {
            $search_link = isset($page_cond["search_link"])?$page_cond["search_link"]:"";
            $page = isset($page_cond["page"])?$page_cond["page"]:1;
            //分頁資料
            $page_data = $this->getPage($search_link,$page,$all_datas);
            $datas["page_data"] = $page_data;
            $list_data = isset($page_data["list_data"])?$page_data["list_data"]:array();
        } else {
            $list_data = $all_datas->get()->toArray();
        }
        //$this->pr($list_data);exit;
        
        if(!empty($list_data)) {
            //選項
            $option_datas = array();
            //選項-狀態
            $option_datas["status"] = $this->getOptions("order_status");
            //代碼-配送方式
            $option_datas["send"] = $this->getCodeNames("order_send");
            //代碼-付款方式
            $option_datas["payment"] = $this->getCodeNames("order_pay");

            foreach($list_data as $key => $val) {
                $data = array();
                $data = $val;
                $id = isset($val["id"])?$val["id"]:0;
                $uuid = isset($val["uuid"])?$val["uuid"]:"";
                //轉換名稱-狀態
                $data["status_name"] = isset($option_datas["status"][$data["status"]])?$option_datas["status"][$data["status"]]:"";
                //轉換名稱-配送方式
                $data["send_name"] = isset($option_datas["send"][$data["send"]])?$option_datas["send"][$data["send"]]:"尚未選擇";
                //轉換名稱-付款方式
                $data["payment_name"] = isset($option_datas["payment"][$data["payment"]])?$option_datas["payment"][$data["payment"]]:"尚未付款";

                //取得訂單詳細資料
                $item_datas = array();
                if($is_detail) {
                    $conds_item = array();
                    $conds_item["order_id"] = $id;
                    $order_item = UnshopOrderItem::where($conds_item);

                    //取得商品ID
                    $product_ids = $order_item->pluck("product_id")->toArray();
                    //$this->pr($product_ids);exit;
                    //取得訂單詳細資料
                    $order_item_datas = $order_item->get()->toArray();
                    //$this->pr($order_item_datas);exit;
                        
                    //取得商品資料
                    $conds_product = array();
                    $conds_product["id"] = $product_ids;
                    $product_datas = $this->getProductData($conds_product,"serial","asc",false,array(),true);
                    //$this->pr($product_datas);


                    if(!empty($order_item_datas)) {
                        foreach($order_item_datas as $order_item_data) {
                            $item_data = array();
                            $item_data = $order_item_data;
                            $product_id = isset($order_item_data["product_id"])?$order_item_data["product_id"]:0;
                            //商品資料
                            $product_data = isset($product_datas["list_data"][$product_id])?$product_datas["list_data"][$product_id]:array();
                            //商品UUID
                            $item_data["uuid"] = isset($product_data["uuid"])?$product_data["uuid"]:"";
                            //商品明細連結
                            $item_data["product_link"] = "/fronts/product_view?source=order&order_uuid=".$uuid."&uuid=".$product_data["uuid"];
                            //商品編號
                            $item_data["serial"] = isset($product_data["serial"])?$product_data["serial"]:"";
                            //商品名稱
                            $item_data["name"] = isset($product_data["name"])?$product_data["name"]:"";
                            //商品圖片
                            $item_data["file_url"] = isset($product_data["file_url"])?$product_data["file_url"]:"";
                            //小計
                            $item_data["subtotal"] = isset($order_item_data["total"])?$order_item_data["total"]:"";

                            $item_datas[] =  $item_data;
                        }
                    }
                }

                $datas["list_data"][$id] = $data;
                $datas["list_data"][$id]["item_datas"] = $item_datas;
            }
        }
        //$this->pr($datas);

        return $datas;
    }
}
