<?php

namespace App\Libraries;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

use App\Models\User;
use App\Models\UnshopUser;


class UserAuth {
    //首頁
    public const HOME = "/"; 
    private static $userdata = null;

    //取得使用者資料
    public static function userdata() {
        if(empty(self::$userdata) && session()->exists("userUuid")) {
            $unshop_user = UnshopUser::where(["uuid" => session("userUuid")])->first();
            if(isset($unshop_user->uuid) && $unshop_user->uuid != "") {
                self::$userdata = $unshop_user;
            }
        }
        return self::$userdata;
    }

    //新增使用者
    public static function createUser($post_username,$post_password) {
        try {
            $data = array();
            $data["name"] = $post_username;
            $data["email"] = $post_username;
            $data["password"] = Hash::make($post_password);
            $user = User::create($data);
            $user_id = (int)$user->id;
        } catch(QueryException $e) {
            $user_id = 0;
        }
        
        return $user_id;
    }

    //判斷是否登入
    public static function isLoggedIn() {
        return !empty(self::userdata());
    }

    //自動登入
    public static function userLogIn($user_id=0) {
        if($user_id > 0) {
            $user = User::where(["id" => $user_id])->first();
            $user_email = $user->email;
            $user_pass = $user->password;
            UserAuth::logIn($user_email,$user_pass,false);
        }
    }

    //登入
    public static function logIn($post_username,$post_password,$is_hash=true) {
        $isSuccess = false;
        //取得登入者
        $user = User::where(["email" => $post_username])->first();
        //檢查密碼是否符合
        if(!empty($user)) {
            $is_match = false;
            if($is_hash) {
                $is_match = Hash::check($post_password,$user->password);
            } else {
                if($post_password == $user->password) {
                    $is_match = true;
                }
            }

            if($is_match) {
                $unshop_user = UnshopUser::where(["user_id" => $user->id])->first();
                //設定session
                if(isset($unshop_user->uuid) && $unshop_user->uuid != "") {
                    $isSuccess = true;
                    self::$userdata = $unshop_user;
                    session(["userUuid" => $unshop_user->uuid]);
                }
            }
        }

        return $isSuccess;
    }

    //登出
    public static function logOut() {
       //刪除session
       session()->forget("userUuid");
       self::$userdata = null;
    }
}