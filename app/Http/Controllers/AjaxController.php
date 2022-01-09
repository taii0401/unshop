<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//字串-UUID
use Illuminate\Support\Str;
//例外處理
use Illuminate\Database\QueryException;
//雜湊-密碼
use Illuminate\Support\Facades\Hash;
//上傳檔案
use Illuminate\Support\Facades\Storage;
//使用者權限
use App\Libraries\UserAuth;
//Model
use App\Models\User;
use App\Models\UnshopFile;
use App\Models\UnshopUser;
use App\Models\UnshopFileData;
use App\Models\UnshopProduct;
use App\Models\UnshopCart;
use App\Models\UnshopOrder;
use App\Models\UnshopOrderItem;

class AjaxController extends Controller
{
    private function resetResult() 
    {
        $this->error = true;
        $this->message = "請確認資料！";
    }

    private function returnResult()
    {
        $return_data = array(
            "error" => $this->error,
            "message" => $this->message
        );
        return $return_data;
    }

    //檔案-上傳檔案
    public function upload_file(Request $request)
    {
        $this->resetResult();
        
        $name = $file_id = "";
        //判斷是否登入
        if(UserAuth::isLoggedIn()) {
            $user_uuid = session("userUuid");
            $user_id = 0;
            if($user_uuid != "") {
                //使用者資料
                $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
                $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
            }

            //檔案名稱
            $name = $request->file("file")->getClientOriginalName();
            //檔案大小
            $size = $request->file("file")->getSize();
            //檔案型態
            $str = explode(".",$name);
            $types = isset($str[1])?$str[1]:"";
            //新檔案名稱
            $file_name = substr(Str::uuid()->toString(),0,8)."_".date("YmdHis").".".$types;

            //檔案存放路徑
            $diskName = "public";
            //將檔案存在./storage/public/files/$user_id/，並將檔名改為$file_name
            $path = $request->file("file")->storeAs(
                "files/".$user_id,
                $file_name,
                $diskName
            );
            //print_r($path);

            try {
                //新增檔案
                $data = array();
                $data["name"] = $name;
                $data["file_name"] = $file_name;
                $data["path"] = $diskName."/".$path;
                $data["size"] = $size;
                $data["types"] = $types;
                $file_data = UnshopFile::create($data);
                $file_id = (int)$file_data->id;

                if($file_id > 0) { //新增成功
                    $this->error = false;
                } else {
                    //刪除檔案存放路徑
                    $file_path = "public/files/".$user_id."/".$file_name;
                    if(Storage::exists($file_path)) {
                        Storage::delete($file_path);
                    }
                    $this->message = "新增檔案錯誤！";
                }
            } catch(QueryException $e) {
                $this->message = "新增檔案錯誤！";
            }
        } else {
            $this->message = "沒有權限上傳檔案！";
        }

        $return_data = $this->returnResult();
        $return_data["file_name"] = $name;
        $return_data["file_id"] = $file_id;
        //print_r($return_data);
        return response()->json($return_data);
    }
    
    //檔案-刪除檔案
    public function upload_file_delete(Request $request,$file_ids=array())
    {
        $this->resetResult();

        if(empty($file_ids)) {
            $file_ids = $request->has("file_id")?array($request->input("file_id")):array();
        }
        
        //刪除檔案
        $delete = UnshopFile::deleteFile($file_ids);
        if($delete) {
            $this->error = false;
        } else {
            $this->message = "刪除檔案錯誤！";
        }

        return response()->json($this->returnResult());
    }

    //使用者資料-檢查帳號是否存在
    public function user_exist(Request $request)
    {
        $this->resetResult();
        
        if($request->has("username") && trim($request->username) != "") {
            $username = trim($request->username);
            $count = User::where(["email" => $username])->count();
            if($count == 0) {
                $this->error = false;
                $this->message = "帳號可新增！";
            } else {
                $this->message = "帳號已存在！";
            }
        } else {
            $this->message = "請輸入帳號！";
        }
        
        return response()->json($this->returnResult());
    }

    //使用者資料-檢查商品頁面網址是否存在
    public function user_link_exist(Request $request)
    {
        $this->resetResult();

        if($request->has("short_link") && trim($request->short_link) != "") {
            $short_link = trim($request->short_link);
            if(session()->exists("userUuid")) {
                $count = UnshopUser::where([["short_link","=",$short_link],["uuid","<>",session("userUuid")]])->count();
            } else {
                $count = UnshopUser::where(["short_link" => $short_link])->count();
            }

            if($count == 0) {
                $this->error = false;
                $this->message = "商品頁面網址可新增！";
            } else {
                $this->message = "商品頁面網址已存在！";
            }
        } else {
            $this->message = "請輸入商品頁面網址！";
        }

        return response()->json($this->returnResult());
    }

    //使用者資料-忘記密碼
    public function user_forget(Request $request)
    {
        $this->resetResult();

        if($request->has("username") && trim($request->username) != "") {
            try {
                $username = trim($request->username);
                $user = User::where(["email" => $username])->first()->toArray();
                if(!empty($user)) {
                    //隨機產生亂碼
                    $ran_str = $this->getRandom(6);
                    //更新密碼
                    $data = array();
                    $data["password"] = Hash::make($ran_str);
                    User::where(["email" => $username])->update($data);

                    $this->error = false;
                    $this->message = "密碼：".$ran_str."  請重新登入後，至修改密碼更新！";
                }
            } catch(QueryException $e) {
                $this->message = "請確認帳號！";
            }
        } else {
            $this->message = "請輸入帳號！";
        }

        return response()->json($this->returnResult());
    }

    //使用者資料-新增、編輯、刪除
    public function user_data(Request $request)
    {
        $this->resetResult();

        //建立時間
        $now = date("Y-m-d H:i:s");
        //表單動作類型(新增、編輯、刪除)
        $action_type = $request->has("action_type")?$request->input("action_type"):"";
        //print_r($action_type);
        
        if($action_type == "add") { //新增使用者
            $post_username = $request->has("username")?trim($request->input("username")):"";
            $post_password = $request->has("password")?trim($request->input("password")):"";

            if($post_username == "" || $post_password == "") {
                $this->message = "請輸入帳號密碼！";
            } else {
                //新增使用者
                $user_id = UserAuth::createUser($post_username,$post_password);
                //print_r($user_id);exit;

                //新增使用者資料
                if($user_id > 0) {
                    $data = array();
                    $data["uuid"] = Str::uuid()->toString();
                    $data["user_id"] = $user_id;
                    $data["short_link"] = $request->has("short_link")?trim($request->input("short_link")):"";
                    $data["name"] = $request->has("name")?$request->input("name"):"";
                    $data["sex"] = $request->has("sex")?$request->input("sex"):"";
                    $data["birthday"] = $request->has("birthday")?$request->input("birthday"):"";
                    $data["phone"] = $request->has("phone")?$request->input("phone"):"";
                    $data["address"] = $request->has("address")?$request->input("address"):"";
                    $data["is_delete"] = 0;
                    $data["create_time"] = $now;
                    $data["modify_time"] = $now;
                    $user_data = UnshopUser::create($data);
                    //print_r($user_data->id);exit;

                    if((int)$user_data->id > 0) { //新增成功
                        $this->error = false;
                    } else {
                        //刪除使用者
                        User::destroy($user_id);
                        $this->message = "新增錯誤！";
                    }
                } else {
                    $this->message = "新增帳號密碼錯誤！";
                }
            }
        } else {
            $uuid = $request->has("uuid")?$request->input("uuid"):"";
            //取得使用者資料
            $unshop_user = UnshopUser::where(["uuid" => $uuid])->first()->toArray();
            $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;

            if($uuid != "") {
                if($action_type == "edit") { //編輯使用者
                    try {
                        $data = array();
                        $data["short_link"] = $request->has("short_link")?trim($request->input("short_link")):"";
                        $data["name"] = $request->has("name")?$request->input("name"):"";
                        $data["sex"] = $request->has("sex")?$request->input("sex"):"";
                        $data["birthday"] = $request->has("birthday")?$request->input("birthday"):"";
                        $data["phone"] = $request->has("phone")?$request->input("phone"):"";
                        $data["address"] = $request->has("address")?$request->input("address"):"";
                        $data["modify_time"] = $now;
                        UnshopUser::where(["uuid" => $uuid])->update($data);
                        $this->error = false;
                    } catch(QueryException $e) {
                        $this->message = "更新錯誤！";
                    }
                } else if($action_type == "edit_password") { //編輯使用者密碼
                    //取得登入者
                    $user = User::where(["id" => $user_id])->first();
                    //檢查密碼是否符合
                    if(!empty($user) && $request->has("password") && $request->has("confirm_password")) {
                        if(trim($request->input("password")) == $user->password) {
                            $this->message = "密碼尚未修改！";
                        } else {
                            try {
                                if(trim($request->input("password")) == trim($request->input("confirm_password"))) {
                                    $data = array();
                                    $data["password"] = Hash::make(trim($request->input("password")));
                                    User::where(["id" => $user_id])->update($data);
                                    $this->error = false;
                                } else {
                                    $this->message = "密碼與確認密碼不符合！";
                                }
                            } catch(QueryException $e) {
                                $this->message = "更新密碼錯誤！";
                            }
                        }
                    }
                } else if($action_type == "delete") { //刪除使用者
                    try {
                        $data = array();
                        $data["is_delete"] = 1;
                        $data["modify_time"] = $now;
                        UnshopUser::where(["uuid" => $uuid])->update($data);
                        User::destroy($user_id);
                        $this->error = false;
                    } catch(QueryException $e) {
                        $this->message = "刪除錯誤！";
                    }
                }
            }
        }

        return response()->json($this->returnResult());
    }

    //商品資料-新增、編輯、刪除
    public function product_data(Request $request)
    {
        $this->resetResult();
        
        //使用者ID
        $user_id = $request->has("user_id")?$request->user_id:0;
        //判斷是否登入
        if(UserAuth::isLoggedIn() && $user_id > 0) {
            //建立時間
            $now = date("Y-m-d H:i:s");
            //表單動作類型(新增、編輯、刪除)
            $action_type = $request->has("action_type")?$request->input("action_type"):"";
            //print_r($action_type);

            if($action_type == "add" || $action_type == "edit") { //新增、編輯
                //表單資料
                $data = array();
                $data["name"] = $request->has("name")?$request->input("name"):""; //名稱
                $data["author"] = $request->has("author")?$request->input("author"):""; //作者
                $data["office"] = $request->has("office")?$request->input("office"):""; //出版社
                $data["publish"] = $request->has("publish")?$request->input("publish"):""; //出版日期
                $data["price"] = $request->has("price")?(int)$request->input("price"):0; //原價
                $data["sales"] = $request->has("sales")?(int)$request->input("sales"):$data["price"]; //售價
                $data["content"] = $request->has("content")?$request->input("content"):""; //內容簡介
                $data["category"] = $request->has("category")?$request->input("category"):""; //目錄
                //是否顯示
                $data["is_display"] = 1; //是
                if(!$request->has("is_display")) {
                    $data["is_display"] = 0; //否
                }
                $data["is_delete"] = 0; //是否刪除
                $data["modify_time"] = $now; //修改時間

                $uuid = "";
                if($action_type == "add") { //新增
                    //UUID
                    $uuid = Str::uuid()->toString();
                    //取得代碼名稱
                    $code_datas = $this->getData("code",array(),"code");
                    //商品類型
                    $types = $request->has("types")?$request->input("types"):1;
                    //商品類型代碼
                    $serial_code = isset($code_datas[$types])?$code_datas[$types]:"";
                    //取得新編號
                    $serial_num = UnshopProduct::getSerial($types);

                    $data["uuid"] = $uuid;
                    $data["user_id"] = $user_id;
                    $data["types"] = $types; 
                    $data["serial_code"] = $serial_code;
                    $data["serial_num"] = $serial_num;
                    $data["serial"] = $serial_code.str_pad($serial_num,4,0,STR_PAD_LEFT); //商品編號
                    $data["create_time"] = $now; //建立時間

                    try {
                        $product_data = UnshopProduct::create($data);
                    } catch(QueryException $e) {
                        $this->message = "新增錯誤！";
                    }
                } else if($action_type == "edit") { //編輯
                    $uuid = $request->has("uuid")?$request->input("uuid"):"";
                    try {
                        UnshopProduct::where(["uuid" => $uuid,"user_id" => $user_id])->update($data);
                        $product_data = UnshopProduct::where(["uuid" => $uuid,"user_id" => $user_id])->first("id");
                    } catch(QueryException $e) {
                        $this->message = "更新錯誤！";
                    }
                }
                //$this->pr($product_data->id);

                //新增或編輯成功
                if($product_data->exists("id")) {
                    $product_id = $product_data->id;
                    //上傳檔案
                    if($request->has("file_id") && !empty($request->input("file_id"))) {
                        $file_data = array();
                        $file_data["data_id"] = $product_id;
                        $file_data["data_type"] = "product";
                        $file_data["file_ids"] = $request->input("file_id");
                        $file_data["user_id"] = $user_id;
                        $result = UnshopFileData::updateFileData($action_type,$file_data);
                        //$this->pr($result);exit;
                        if(isset($result["error"]) && !($result["error"])) {
                            $this->error = false;
                            $this->message = $uuid;
                        } else {
                            $this->message = isset($result["message"])?$result["message"]:"檔案儲存錯誤！";
                        }
                    } else {
                        $this->error = false;
                        $this->message = $uuid;
                    }
                } else {
                    $this->message = "新增或更新錯誤！";
                }
            } else if($action_type == "delete" || $action_type == "delete_list") { //刪除
                $uuids = array();
                if($action_type == "delete") {
                    $uuids = $request->has("uuid")?array($request->input("uuid")):array();
                } else { //刪除-列表勾選多筆
                    $check_list = $request->has("check_list")?$request->input("check_list"):"";
                    $uuids = explode(",",$check_list);
                }
                
                try {
                    $product_data = UnshopProduct::whereIn("uuid",$uuids)->where(["user_id" => $user_id]);
                    //取得ID
                    $product_ids = $product_data->pluck("id")->toArray();

                    //更新是否刪除
                    $data = array();
                    $data["is_delete"] = 1;
                    $data["modify_time"] = $now;
                    $product_data->update($data);

                    //刪除上傳檔案
                    $file_data = array();
                    $file_data["data_ids"] = $product_ids;
                    $result = UnshopFileData::updateFileData($action_type,$file_data);
                    if(isset($result["error"]) && !($result["error"])) {
                        $this->error = false;
                    } else {
                        $this->message = isset($result["message"])?$result["message"]:"檔案刪除錯誤！";
                    }
                } catch(QueryException $e) {
                    $this->message = "刪除錯誤！";
                }
            }
        } else {
            $this->message = "沒有權限！";
        }

        return response()->json($this->returnResult());
    }

    //購物車-新增、編輯、刪除
    public function cart_data(Request $request)
    {
        $this->resetResult();

        //判斷是否登入
        if(UserAuth::isLoggedIn()) {
            $user_uuid = session("userUuid");
            $user_id = 0;
            if($user_uuid != "") {
                //使用者資料
                $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
                $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
            }
            
            if($user_id > 0) {
                //建立時間
                $now = date("Y-m-d H:i:s");
                //表單動作類型(新增、編輯、刪除)
                $action_type = $request->has("action_type")?$request->input("action_type"):"";
                //print_r($action_type);
                //商品ID
                $product_id = $request->has("product_id")?$request->input("product_id"):0;
                //print_r($product_id);

                $data = array();
                if($product_id > 0) {
                    if($action_type == "add") { //新增
                        //販賣商品的使用者
                        $product_user_id = $request->has("product_user_id")?$request->input("product_user_id"):0;
                        if($product_user_id == $user_id) {
                            $this->message = "無法購買自己的商品！";
                        } else {
                            //新增商品至購物車
                            $data["user_id"] = $user_id;
                            $data["product_id"] = $product_id;
                            $data["amount"] = 1;
                            $data["create_time"] = $now;
                            $data["modify_time"] = $now;
                            
                            try {
                                UnshopCart::create($data);
                                $this->error = false;
                            } catch(QueryException $e) {
                                $this->error = false;
                                $this->message = "商品已存在！";
                            }
                        }
                    } else if($action_type == "edit") { //編輯-更新數量
                        $data["amount"] = $request->has("amount")?$request->input("amount"):1;
                        try {
                            UnshopCart::where(["user_id" => $user_id,"product_id" => $product_id])->update($data);
                            $this->error = false;
                        } catch(QueryException $e) {
                            $this->message = "更新錯誤！";
                        }
                    } else if($action_type == "delete") { //刪除
                        try {
                            UnshopCart::where(["user_id" => $user_id,"product_id" => $product_id])->delete();
                            $this->error = false;
                        } catch(QueryException $e) {
                            $this->message = "刪除錯誤！";
                        }
                    }
                } else {
                    $this->message = "無此商品！";
                }
            }
        } else {
            $this->message = "請先登入！";
        }
        
        return response()->json($this->returnResult());
    }

    //訂單-新增、編輯、刪除
    public function order_data(Request $request)
    {
        $this->resetResult();
        
        //判斷是否登入
        if(UserAuth::isLoggedIn()) {
            $user_uuid = session("userUuid");
            $user_id = 0;
            if($user_uuid != "") {
                //使用者資料
                $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
                $user_id = isset($unshop_user["user_id"])?$unshop_user["user_id"]:0;
            }
            
            if($user_id > 0) {
                //建立時間
                $now = date("Y-m-d H:i:s");
                //表單動作類型(新增、編輯、刪除)
                $action_type = $request->has("action_type")?$request->input("action_type"):"";
                //print_r($action_type);

                $data = array();
                if($action_type == "add") { //新增
                    $isSuccess = false;
                    //UUID
                    $uuid = Str::uuid()->toString();
                    //取得新編號
                    $serial_num = UnshopOrder::getSerial();
                    
                    $data["uuid"] = $uuid;
                    $data["user_id"] = $user_id;
                    $data["serial_code"] = "UO";
                    $data["serial_num"] = $serial_num;
                    $data["serial"] = "UO".str_pad($serial_num,4,0,STR_PAD_LEFT); //訂單編號
                    $data["name"] = $request->has("name")?$request->input("name"):"";
                    $data["phone"] = $request->has("phone")?$request->input("phone"):"";
                    $data["address"] = $request->has("address")?$request->input("address"):"";
                    $data["payment"] = $request->has("payment")?$request->input("payment"):0;
                    $data["send"] = $request->has("send")?$request->input("send"):0;
                    $data["total"] = $request->has("total")?$request->input("total"):0;
                    $data["create_time"] = $now;
                    $data["modify_time"] = $now;
                    
                    try {
                        $order_data = UnshopOrder::create($data);
                        $isSuccess = true;
                    } catch(QueryException $e) {
                        $this->message = "新增錯誤！";
                    }

                    //新增成功
                    if($order_data->exists("id")) {
                        $order_id = $order_data->id;
                        if($order_id > 0) {
                            //取得購物車資料
                            $cart_datas = $this->getCartData(array("user_id" => $user_id),false);
                            //新增訂單項目
                            if(!empty($cart_datas)) {
                                foreach($cart_datas as $cart_data) {
                                    $item_data = array();
                                    $item_data["order_id"] = $order_id;
                                    $item_data["product_id"] = isset($cart_data["id"])?$cart_data["id"]:0;
                                    $item_data["amount"] = isset($cart_data["amount"])?$cart_data["amount"]:0;
                                    $item_data["price"] = isset($cart_data["price"])?$cart_data["price"]:0;
                                    $item_data["total"] = isset($cart_data["subtotal"])?$cart_data["subtotal"]:0;
                                    $item_data["create_time"] = $now;
                                    $item_data["modify_time"] = $now;
                                    try {
                                        UnshopOrderItem::create($item_data);
                                    } catch(QueryException $e) {
                                        $isSuccess = false;
                                    }
                                }
                            }
                        }
                    }

                    if($isSuccess) {
                        try {
                            UnshopCart::where(["user_id" => $user_id])->delete();
                            $this->error = false;
                            $this->message = $uuid;
                        } catch(QueryException $e) {
                            $this->message = "刪除購物車錯誤！";
                        }
                    }
                } else if($action_type == "edit") { //編輯-更新狀態
                    $uuid = $request->has("uuid")?$request->input("uuid"):"";
                    try {
                        UnshopCart::where(["uuid" => $uuid])->update($data);
                        $this->error = false;
                    } catch(QueryException $e) {
                        $this->message = "更新錯誤！";
                    }
                }
            }
        } else {
            $this->message = "請先登入！";
        }

        return response()->json($this->returnResult());
    }
}
