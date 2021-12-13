<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

use App\Libraries\UserAuth;

use App\Models\User;
use App\Models\UnshopUser;

class AjaxController extends Controller
{
    //檔案-上傳檔案
    public function upload_file(Request $request)
    {

    }
    //檔案-刪除檔案實際路徑
    public function upload_file_delete(Request $request)
    {

    }

    //使用者資料-檢查帳號是否存在
    public function user_exist(Request $request)
    {
        $error = true;
        $message = "請確認資料！";
        
        if($request->has("username") && trim($request->username) != "") {
            $username = trim($request->username);
            $count = User::where(["email" => $username])->count();
            if($count == 0) {
                $error = false;
                $message = "帳號可新增！";
            } else {
                $message = "帳號已存在！";
            }
        } else {
            $message = "請輸入帳號！";
        }
        
        $return_data = array("error" => $error,"message" => $message);
        //print_r($return_data);
        return response()->json($return_data);
    }

    //使用者資料-檢查商品頁面網址是否存在
    public function user_link_exist(Request $request)
    {
        $error = true;
        $message = "請確認資料！";

        if($request->has("short_link") && trim($request->short_link) != "") {
            $short_link = trim($request->short_link);
            if(session()->exists("userUuid")) {
                $count = UnshopUser::where([["short_link","=",$short_link],["uuid","<>",session("userUuid")]])->count();
            } else {
                $count = UnshopUser::where(["short_link" => $short_link])->count();
            }

            if($count == 0) {
                $error = false;
                $message = "商品頁面網址可新增！";
            } else {
                $message = "商品頁面網址已存在！";
            }
        } else {
            $message = "請輸入商品頁面網址！";
        }

        $return_data = array("error" => $error,"message" => $message);
        //print_r($return_data);
        return response()->json($return_data);
    }

    //使用者資料-忘記密碼
    public function user_forget(Request $request)
    {
        $error = true;
        $message = "請確認資料！";

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

                    $error = false;
                    $message = "密碼：".$ran_str."  請重新登入後，至修改密碼更新！";
                }
            } catch(QueryException $e) {
                $message = "請確認帳號！";
            }
        } else {
            $message = "請輸入帳號！";
        }

        $return_data = array("error" => $error,"message" => $message);
        //print_r($return_data);
        return response()->json($return_data);
    }

    //使用者資料-新增、編輯、刪除
    public function user_data(Request $request)
    {
        $error = true;
        $message = "請確認資料！";

        //建立時間
        $now = date("Y-m-d H:i:s");
        //表單動作類型(新增、編輯、刪除)
        $action_type = $request->has("action_type")?$request->action_type:"";
        //print_r($action_type);
        
        if($action_type == "add") { //新增使用者
            $post_username = $request->has("username")?trim($request->input("username")):"";
            $post_password = $request->has("password")?trim($request->input("password")):"";

            if($post_username == "" || $post_password == "") {
                $message = "請輸入帳號密碼！";
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
                        $error = false;
                    } else {
                        //刪除使用者
                        User::destroy($user_id);
                        $message = "新增錯誤！";
                    }
                } else {
                    $message = "新增帳號密碼錯誤！";
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
                        $error = false;
                    } catch(QueryException $e) {
                        $message = "更新錯誤！";
                    }
                } else if($action_type == "edit_password") { //編輯使用者密碼
                    //取得登入者
                    $user = User::where(["id" => $user_id])->first();
                    //檢查密碼是否符合
                    if(!empty($user) && $request->has("password") && $request->has("confirm_password")) {
                        if(trim($request->password) == $user->password) {
                            $message = "密碼尚未修改！";
                        } else {
                            try {
                                if(trim($request->password) == trim($request->confirm_password)) {
                                    $data = array();
                                    $data["password"] = Hash::make(trim($request->password));
                                    User::where(["id" => $user_id])->update($data);
                                    $error = false;
                                } else {
                                    $message = "密碼與確認密碼不符合！";
                                }
                            } catch(QueryException $e) {
                                $message = "更新密碼錯誤！";
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
                        $error = false;
                    } catch(QueryException $e) {
                        $message = "刪除錯誤！";
                    }
                }
            }
        }

        $return_data = array("error" => $error,"message" => $message);
        //print_r($return_data);
        return response()->json($return_data);
    }
}
