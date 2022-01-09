<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//例外處理
use Illuminate\Database\QueryException;
//使用者權限
use App\Libraries\UserAuth;
//Model
use App\Models\User;
use App\Models\UnshopUser;
use App\Models\UnshopOrder;
use Hamcrest\Arrays\IsArray;

class OrderController extends Controller
{   
    //交易資料 AES 加密
    private function create_mpg_aes_encrypt($parameter="",$key="",$iv="") {
        $return_str = '';
        if(!empty($parameter)) {
            //將參數經過URL ENCODED QUERY STRING
            $return_str = http_build_query($parameter);
        }
        return trim(bin2hex(openssl_encrypt($this->addpadding($return_str),'aes-256-cbc',$key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,$iv)));
    }

    private function addpadding($string,$blocksize = 32) {
        $len = strlen($string);
        $pad = $blocksize-($len%$blocksize);
        $string .= str_repeat(chr($pad),$pad);
        return $string;
    }

    //交易資料 AES 解密
    private function create_aes_decrypt($parameter="",$key="",$iv="") {
        return $this->strippadding(openssl_decrypt(hex2bin($parameter),'AES-256-CBC',
        $key,OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,$iv));
    }

    private function strippadding($string) {
        $slast = ord(substr($string,-1));
        $slastc = chr($slast);
        $pcheck = substr($string,-$slast);
        if (preg_match("/$slastc{" . $slast . "}/",$string)) {
            $string = substr($string,0,strlen($string)-$slast);
            return $string;
        } else {
            return false;
        }
    }
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
        $assign_data["danger_none"] = $assign_data["success_none"] = "none"; //顯示訊息
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
        $assign_data["danger_none"] = $assign_data["success_none"] = "none"; //顯示訊息
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

    //購物車-收件人資料
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
        
        $assign_data["title_txt"] = "收件人資料";
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
            }
        }
        $assign_data["total"] = $total;
        
        return view("orders.pay",["assign_data" => $assign_data,"option_datas" => $option_datas]);
    }

    //購物車結帳
    public function pay_check(Request $request)
    {
        $assign_data = array();
        $assign_data["title_txt"] = "結帳";
        $uuid = $request->has("order_uuid")?$request->input("order_uuid"):"";

        $order_number = $user_email = "";
        $order_total = 0;
        //取得訂單資料
        if($uuid != "") {
            $conds = array();
            $conds["uuid"] = $uuid;
            $all_datas = $this->getOrderData($conds,"serial","asc",false,array(),true);
            //資料
            if(isset($all_datas["list_data"])) {
                foreach($all_datas["list_data"] as $list_data) {
                    foreach($list_data as $key => $val) {
                        $assign_data[$key] = $val;
                    }
                }
            }
            //訂單編號
            $order_number = isset($assign_data["serial"])?$assign_data["serial"]:"";
            //訂單金額
            $order_total = isset($assign_data["total"])?$assign_data["total"]:0;

            //取得使用者email
            if(isset($assign_data["user_id"]) && $assign_data["user_id"] > 0) {
                $user = User::where(["id" => $assign_data["user_id"]])->get()->toArray();
                $user_email = isset($user[0]["email"])?$user[0]["email"]:"";
            }
        }

        if($order_total > 0 && $order_number != "" && $user_email != "") {
            $assign_data["MerchantID"] = env("MPG_MerchantID",""); //商店代號
            $assign_data["Version"] = env("MPG_Version",""); //串接程式版本
            $assign_data["MerchantOrderNo"] = $order_number; //商店訂單編號
            $assign_data["Amt"] = $order_total; //訂單金額
            $assign_data["Email"] = $user_email; //付款人電子信箱

            $hashKey = env("MPG_HashKey","");
            $hashIV = env("MPG_HashIV","");
            $ExpireDate = env("MPG_ExpireDate","");
            $tradeInfoAry = [
                "MerchantID" => env("MPG_MerchantID",""), //商店代號
                "RespondType" => env("MPG_RespondType",""), //回傳格式
                "TimeStamp" => time(), //時間戳記
                "Version" => env("MPG_Version",""), //串接程式版本
                "LangType" => env("MPG_LangType",""), //語系
                "MerchantOrderNo" => $assign_data["MerchantOrderNo"], //商店訂單編號
                "Amt" => $assign_data["Amt"], //訂單金額
                "ItemDesc" => env("MPG_ItemDesc",""), //商品資訊
                "TradeLimit" => env("MPG_TradeLimit",""), //交易限制秒數
                "ExpireDate" => date("Ymd",strtotime(date("")."+$ExpireDate days")), //繳費有效期限
                "ReturnURL" => env("APP_URL").env("MPG_ReturnURL",""), //支付完成，返回商店網址
                "NotifyURL" => env("APP_URL").env("MPG_NotifyURL",""), //支付通知網址
                "CustomerURL" => env("APP_URL").env("MPG_CustomerURL",""), //商店取號網址
                "ClientBackURL" => env("APP_URL").env("MPG_ClientBackURL",""), //返回商店網址
                "Email" => $assign_data["Email"], //付款人電子信箱
                "EmailModify" => env("MPG_EmailModify",""), //付款人電子信箱，是否開放修改
                "LoginType" => env("MPG_LoginType",""), //藍新金流會員
                "OrderComment" => env("MPG_OrderComment",""), //商店備註
                "CREDIT" => env("MPG_CREDIT",""), //信用卡㇐次付清啟用
                "ANDROIDPAY" => env("MPG_ANDROIDPAY",""), //Google Pay啟用
                "SAMSUNGPAY" => env("MPG_SAMSUNGPAY",""), //Samsung Pay啟用
                "LINEPAY" => env("MPG_LINEPAY",""), //LINE Pay啟用
                "ImageUrl" => env("MPG_ImageUrl",""), //LINE PAY產品圖檔連結網址
                "InstFlag" => env("MPG_InstFlag",""), //信用卡分期付款啟用
                "CreditRed" => env("MPG_CreditRed",""), //信用卡紅利啟用
                "UNIONPAY" => env("MPG_UNIONPAY",""), //信用卡銀聯卡啟用
                "WEBATM" => env("MPG_WEBATM",""), //WEBATM 啟用
                "VACC" => env("MPG_VACC",""), //ATM 轉帳啟用
                "CVS" => env("MPG_CVS",""), //超商代碼繳費啟用
                "BARCODE" => env("MPG_BARCODE",""), //超商條碼繳費啟用
                "ESUNWALLET" => env("MPG_ESUNWALLET",""), //玉山Walle
                "TAIWANPAY" => env("MPG_TAIWANPAY",""), //台灣Pay
                "CVSCOM" => env("MPG_CVSCOM",""), //物流啟用
                "EZPAY" => env("MPG_EZPAY",""), //簡單付電子錢包
                "EZPWECHAT" => env("MPG_EZPWECHAT",""), //簡單付微信支付
                "EZPALIPAY" => env("MPG_EZPALIPAY",""), //簡單付支付寶
                "LgsType" => env("MPG_LgsType",""), //物流型態     
            ];

            //交易資料經AES 加密後取得tradeInfo
            $tradeInfo = $this->create_mpg_aes_encrypt($tradeInfoAry,$hashKey,$hashIV);
            $tradeSha = strtoupper(hash("sha256","HashKey={$hashKey}&{$tradeInfo}&HashIV={$hashIV}"));
            $assign_data["tradeInfo"] = $tradeInfo;
            $assign_data["tradeSha"] = $tradeSha;
        } else { //資料有誤，無法串接
            //訂單列表
            return redirect("orders/");
        }        

        return view("orders.pay_check",["assign_data" => $assign_data]);
    }

    //檢查串接金流回傳結果
    private function mpgCallbackValues($request)
    {
        $hashKey = env("MPG_HashKey","");
        $hashIV = env("MPG_HashIV","");

        $status = $request->has("Status")?$request->input("Status"):"";
        $merchantID = $request->has("MerchantID")?$request->input("MerchantID"):"";
        $version = $request->has("Version")?$request->input("Version"):"";
        $tradeInfo = $request->has("TradeInfo")?$request->input("TradeInfo"):"";
        $tradeSha = $request->has("TradeSha")?$request->input("TradeSha"):"";
        $tradeShaForTest = strtoupper(hash("sha256","HashKey={$hashKey}&{$tradeInfo}&HashIV={$hashIV}"));
        //$this->pr($status);

        if($status == "SUCCESS" && $merchantID == env("MPG_MerchantID") && $version == env("MPG_Version") && $tradeSha == $tradeShaForTest) {
            //交易資料 AES 解密
            $tradeInfoJSONString = $this->create_aes_decrypt($tradeInfo,$hashKey,$hashIV);
            $tradeInfoAry = json_decode($tradeInfoJSONString,true);
            $this->pr($tradeInfoAry);//exit;

            $result = isset($tradeInfoAry["Result"])?$tradeInfoAry["Result"]:array();
            $result["json_data"] = $tradeInfoJSONString;

            return $result;
        }

        return "MPG 錯誤";
    }

    //購物車結帳-串接金流-回傳是否成功
    public function mpg_return(Request $request)
    {
        //成功訊息
        $isSuccess = false;
        $data = array();

        $result = $this->mpgCallbackValues($request);
        //回傳訊息
        if(is_array($result)) {
            //訂單編號
            $MerchantOrderNo = isset($result["MerchantOrderNo"])?$result["MerchantOrderNo"]:"";
            //付款方式
            $PaymentType = isset($result["PaymentType"])?$result["PaymentType"]:"";

            //自動登入
            $user_id = UnshopOrder::where(["serial" => $MerchantOrderNo])->first()->user_id;
            UserAuth::userLogIn($user_id);

            //取得訂單資料
            $conds = array();
            $conds["serial"] = $MerchantOrderNo;
            $all_datas = $this->getOrderData($conds,"serial","asc",false,array(),true);
            //資料
            if(isset($all_datas["list_data"])) {
                foreach($all_datas["list_data"] as $list_data) {
                    foreach($list_data as $key => $val) {
                        $assign_data[$key] = $val;
                    }
                }
            }

            //更新狀態及付款方式
            $data = array();
            if(!empty($assign_data) && in_array($PaymentType,array("CREDIT","WEBATM")) && isset($result["PayTime"])) {
                $data["send"] = "home"; //配送方式-宅配到府
                $data["payment"] = $PaymentType; //付款方式

                if($PaymentType == "CREDIT") {
                    $data["status"] = 1; //狀態-已付款
                } else if($PaymentType == "WEBATM") {
    
                }

                try {
                    UnshopOrder::where(["serial" => $MerchantOrderNo])->update($data);
                    $isSuccess = true;
                } catch(QueryException $e) {
                    
                }
            }
        }

        return redirect("orders/pay_result?order_serial=$MerchantOrderNo&status=$isSuccess&payment=$PaymentType");
    }

    //購物車結帳-串接金流-按鈕觸發是否付款
    public function notify(Request $request)
    {
        $assign_data = array();

        $result = $this->mpgCallbackValues($request);
        //回傳訊息
        if(is_array($result)) {
            //Log::debug("notify: ".json_encode($result));

            //訂單編號
            $MerchantOrderNo = isset($result["MerchantOrderNo"])?$result["MerchantOrderNo"]:"";
            //付款方式
            $PaymentType = isset($result["PaymentType"])?$result["PaymentType"]:"";

            //自動登入
            $user_id = UnshopOrder::where(["serial" => $MerchantOrderNo])->first()->user_id;
            UserAuth::userLogIn($user_id);

            //取得訂單資料
            $conds = array();
            $conds["serial"] = $MerchantOrderNo;
            $all_datas = $this->getOrderData($conds,"serial","asc",false,array(),true);
            //資料
            if(isset($all_datas["list_data"])) {
                foreach($all_datas["list_data"] as $list_data) {
                    foreach($list_data as $key => $val) {
                        $assign_data[$key] = $val;
                    }
                }
            }

            $data = array();
            if(!empty($assign_data) && in_array($PaymentType,array("VACC","CVS","BARCODE")) && isset($result["PayTime"])) {
                $data["send"] = "home"; //配送方式-宅配到府
                $data["payment"] = $PaymentType; //付款方式
                $data["status"] = 1; //狀態-已付款

                try {
                    UnshopOrder::where(["serial" => $MerchantOrderNo])->update($data);
                } catch(QueryException $e) {
                    return;
                }
            }
        }
        //Log::debug("notify: ".$result);
        return;
    }

    //購物車結帳-串接金流-待客戶付款
    public function customer(Request $request)
    {
        $assign_data = array();

        $result = $this->mpgCallbackValues($request);
        //$this->pr($result);exit;
        //回傳訊息
        if(is_array($result)) {
            //訂單編號
            $MerchantOrderNo = isset($result["MerchantOrderNo"])?$result["MerchantOrderNo"]:"";
            //付款方式
            $PaymentType = isset($result["PaymentType"])?$result["PaymentType"]:"";
            
            //自動登入
            $user_id = UnshopOrder::where(["serial" => $MerchantOrderNo])->first()->user_id;
            UserAuth::userLogIn($user_id);

            //取得訂單資料
            $conds = array();
            $conds["serial"] = $MerchantOrderNo;
            $all_datas = $this->getOrderData($conds,"serial","asc",false,array(),true);
            //資料
            if(isset($all_datas["list_data"])) {
                foreach($all_datas["list_data"] as $list_data) {
                    foreach($list_data as $key => $val) {
                        $assign_data[$key] = $val;
                    }
                }
            }

            $data = array();
            if(!empty($assign_data) && in_array($PaymentType,array("VACC","CVS","BARCODE"))) {
                $data["send"] = "home"; //配送方式-宅配到府
                $data["payment"] = $PaymentType; //付款方式

                try {
                    UnshopOrder::where(["serial" => $MerchantOrderNo])->update($data);
                } catch(QueryException $e) {

                }
            }
        }
        
        return redirect("orders/pay_result?order_serial=$MerchantOrderNo&status=0&payment=$PaymentType");
    }

    //購物車結帳-結果
    public function pay_result(Request $request)
    {
        $assign_data = $datas = array();
        $assign_data["title_txt"] = "付款結果";
        //顯示結果
        $assign_data["danger_none"] = $assign_data["success_none"] = "none"; //顯示訊息

        $order_serial = $request->has("order_serial")?$request->input("order_serial"):"";
        $status = $request->has("status")?$request->input("status"):"";
        $payment = $request->has("payment")?$request->input("payment"):"";

        //取得訂單資料
        $conds = array();
        $conds["serial"] = $order_serial;
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

        if($payment == "CREDIT") {
            if($status) {
                $assign_data["success_none"] = "";
            } else {
                $assign_data["danger_none"] = "";
            }
        }

        return view("orders.pay_result",["assign_data" => $assign_data,"datas" => $datas]);
    }

    
    
}
