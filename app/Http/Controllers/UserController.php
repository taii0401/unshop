<?php

namespace App\Http\Controllers;

use Crypt;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Libraries\UserAuth;

use App\Models\User;
use App\Models\UnshopUser;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //登入
    public function index()
    {
        //判斷是否登入
        if(UserAuth::isLoggedIn()) {
            //編輯使用者
            return redirect("users/edit");
        }
        return view("users.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //新增使用者
    public function create()
    {
        //判斷是否登入
        if(UserAuth::isLoggedIn()) {
            //編輯使用者
            return redirect("users/edit");
        }
        $data = $this->get_data("add");
        return view("users.data",$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id=0)
    {
        //判斷是否登入
        if(!UserAuth::isLoggedIn()) {
            //新增使用者
            return redirect("users/create");
        }
        $data = $this->get_data("edit",session("userUuid"));
        return view("users.data",$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //共用data樣板-新增使用者、編輯使用者、修改密碼
    public function get_data($action_type="add",$user_uuid="")
    {
        $data = array();
        $data["action_type"] = $action_type;
        //隱藏欄位-登入帳號、登入密碼、使用者資料
        $data["edit_none"] = $data["edit_pass_none"] = $data["edit_data_none"] = "none";
        //隱藏按鈕-刪除帳號
        $data["btn_none"] = "none";
        //鎖定欄位-登入帳號
        $data["disabled"] = "";
        
        if($action_type == "add") { //新增使用者
            $data["title_txt"] = "申請帳號";
            $data["edit_none"] = $data["edit_pass_none"] = $data["edit_data_none"] = $data["disabled"] = "";
            //性別-預設男
            $data["checked_sex_1"] = "checked";
        } else if($action_type == "edit" || $action_type == "edit_password") { //編輯使用者、修改密碼
            if($action_type == "edit") {
                $data["title_txt"] = "修改帳號";
                $data["edit_data_none"] = "";
            } else {
                $data["title_txt"] = "修改密碼";
                $data["edit_pass_none"] = $data["btn_none"] = "";
            }
            
            if($user_uuid != "") {
                //使用者資料
                $unshop_user = UnshopUser::where(["uuid" => $user_uuid])->first()->toArray();
                if(!empty($unshop_user)) {
                    foreach($unshop_user as $key => $val) {
                        $data[$key] = $val;
                    }
                }
                //帳號密碼
                if(isset($data["user_id"])) {
                    $user = User::where(["id" => $data["user_id"]])->first();
                    //密碼
                    $data["password"] = $user->password;
                    //帳號
                    $user_data = $user->toArray();
                    $data["username"] = isset($user_data["email"])?$user_data["email"]:"";
                }
                //性別
                if(isset($data["sex"])) {
                    $data["checked_sex_".$data["sex"]] = "checked";
                } else {
                    $data["checked_sex_1"] = "checked"; //預設男
                }
            }
        }

        return $data;
    }

    //登入-送出
    public function login(Request $request)
    {
        $message = $post_username = $post_password = "";
        $post_username = $request->input("username");
        $post_password = $request->input("password");

        if($post_username == "" || $post_password == "") {
            $message = "請輸入帳號密碼！";
        } else {
            $login = UserAuth::logIn($post_username,$post_password);
            if(!$login) {
                $message = "帳號密碼錯誤！";
            }
        }

        if($message != "") { //錯誤訊息
            return back()->withErrors($message);
        } else { 
            //編輯使用者
            return redirect("users/edit");
        }
    }

    //登出
    public function logout()
    {
        UserAuth::logOut();
        return redirect("users");
    }

    //忘記密碼
    public function forget()
    {
        return view("users.forget");
    }

    //修改密碼
    public function edit_password()
    {
        //判斷是否登入
        if(!UserAuth::isLoggedIn()) {
            //新增使用者
            return redirect("users");
        }
        $data = $this->get_data("edit_password",session("userUuid"));
        return view("users.data",$data);
    }
}
