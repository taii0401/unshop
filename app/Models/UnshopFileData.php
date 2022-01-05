<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//例外處理
use Illuminate\Database\QueryException;
//上傳檔案
use Illuminate\Support\Facades\Storage;

class UnshopFileData extends Model
{
    use HasFactory;

    protected $table = 'unshop_file_data'; //指定資料表名稱
    public $timestamps = false; 
    protected $primaryKey = 'id'; //主鍵，Model會另外自動加入id
    protected $fillable = [
        'data_id','data_type','file_id','create_by','create_time','modify_by','modify_time',
    ];

    /**
     * 取得檔案資料
     * @param  cond：搜尋條件
     * @param  is_detail：是否取得檔案詳細資料
     * @param  orderby：排序欄位
     * @param  sort：排序-遞增、遞減
     * @return array
     */
    public static function getFileData($cond=array(),$is_detail=false,$orderby="file_id",$sort="asc")
    {
        $data = array();
        //取得檔案資料
        $file_datas = UnshopFileData::where($cond)->orderBy($orderby,$sort)->get()->toArray();
        //$this->pr($file_datas);exit;
        if(!empty($file_datas)) {
            foreach($file_datas as $file_data) {
                $file_id = isset($file_data["file_id"])?$file_data["file_id"]:0;
                $data[$file_id] = $file_data;

                //取得檔案詳細資料
                if($file_id > 0 && $is_detail) {
                    $conds = array();
                    $conds["id"] = $file_id;
                    $file_details = UnshopFile::where($conds)->get()->toArray();
                    if(!empty($file_details)) {
                        foreach($file_details as $file_detail) {
                            foreach($file_detail as $key => $val) {
                                if($key != "id") {
                                    if($key == "path") {
                                        $url = asset(Storage::url($val));
                                        $data[$file_id]["url"] = $url; 
                                    }
                                    $data[$file_id][$key] = $val;  
                                }
                            }
                        }
                    }
                }
            }
        }
        //$this->pr($data);

        return $data;
    }

    /**
     * 更新檔案資料
     * @param  action_type：型態-add、edit、delete
     * @param  data：檔案資料
     * @return boolean
     */
    public static function updateFileData($action_type="add",$data=array())
    {
        $error = true;
        $message = "請確認資料！";
        
        //建立時間
        $now = date("Y-m-d H:i:s");
        
        if($action_type == "add" || $action_type == "edit") {
            $conds = array();
            if(isset($data["data_id"]) && $data["data_id"] != "") {
                $conds["data_id"] = $data["data_id"];
            }
            if(isset($data["data_type"]) && $data["data_type"] != "") {
                $conds["data_type"] = $data["data_type"];
            }
            //$this->pr($data["file_ids"]);
            
            if(!empty($conds) && isset($data["file_ids"]) && !empty($data["file_ids"])) {
                $exist_file_ids = $delete_file_ids = array();
                //取得資料內所有file_id
                $all_datas = UnshopFileData::where($conds)->get()->toArray();
                //$this->pr($all_datas);
                if(!empty($all_datas)) {
                    foreach($all_datas as $all_data) {
                        $file_id = isset($all_data["file_id"])?$all_data["file_id"]:0;
                        if($file_id > 0) {
                            if(!in_array($file_id,$data["file_ids"])) {
                                $delete_file_ids[] = $file_id; //取得需要刪除的file_id
                            } else {
                                $exist_file_ids[] = $file_id; //取得需要存在的file_id
                            }
                        }
                    }
                }
                //$this->pr($exist_file_ids);
                //$this->pr($delete_file_ids);//exit;

                
                $isSuccess = true;
                //刪除檔案
                if(!empty($delete_file_ids)) {
                    try {
                        //DB::enableQueryLog();
                        //刪除檔案資料
                        $delete_data = UnshopFileData::whereIn("file_id",$delete_file_ids)->where($conds)->delete();
                        //dd(DB::getQueryLog());
                        //刪除檔案
                        $delete = UnshopFile::deleteFile($delete_file_ids);

                        if(!$delete_data || !$delete) {
                            $isSuccess = false;
                            $message = "刪除檔案失敗！";
                        }
                    } catch(QueryException $e) {
                        $message = "刪除檔案錯誤！";
                    }
                }

                //新增檔案
                $insert_data = array();
                $insert_data["data_id"] = $data["data_id"];
                $insert_data["data_type"] = $data["data_type"];
                $insert_data["create_by"] = isset($data["user_id"])?$data["user_id"]:0;
                $insert_data["create_time"] = $now;
                $insert_data["modify_by"] = $insert_data["create_by"];
                $insert_data["modify_time"] = $insert_data["create_time"];

                foreach($data["file_ids"] as $file_id) {
                    if(!in_array($file_id,$exist_file_ids)) {
                        $insert_data["file_id"] = $file_id;
                        //DB::enableQueryLog();
                        $file_data = UnshopFileData::create($insert_data);
                        //dd(DB::getQueryLog());
                        $file_data_id = (int)$file_data->id;

                        if($file_data_id < 0) { 
                            $isSuccess = false;
                            $message = "新增失敗！";
                        }
                    }
                }
                if($isSuccess) {
                    $error = false;
                }
            }
        } else if($action_type == "delete") { //刪除
            $data_ids = array();
            if(isset($data["data_ids"]) && !empty($data["data_ids"])) { //多筆資料id
                $data_ids = $data["data_ids"];
            }

            try {
                //取得檔案ID(file_id)
                $file_datas = UnshopFileData::whereIn("data_id",$data_ids);
                $file_ids = $file_datas->pluck("file_id")->toArray();
                //刪除檔案資料
                $file_datas->delete();
                //刪除檔案
                $delete = UnshopFile::deleteFile($file_ids);
                if($delete) {
                    $error = false;
                } else {
                    $message = "刪除檔案錯誤！";
                }
            } catch(QueryException $e) {
                $message = "刪除錯誤！";
            }
        }

        $return_data = array("error" => $error,"message" => $message);
        //print_r($return_data);
        return $return_data;
    }
}
