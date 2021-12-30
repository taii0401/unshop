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
                //ID
                $id = isset($get_data["id"])?$get_data["id"]:0;
                if($id > 0) {
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
            case "product_is_display": //是否顯示
                $data[""] = "全部";
                $data[1] = "是";
                $data[0] = "否";
                break;
            case "product_orderby": //排序
                $data["asc_serial"] = "編號 小 ~ 大";
                $data["desc_serial"] = "編號 大 ~ 小";
                $data["asc_sales"] = "售價 小 ~ 大";
                $data["desc_sales"] = "售價 大 ~ 小";
                break;
            case "code": //代碼
                $conds = array();
                $conds["types"] = $code_type;
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
        }

        return $data;
    }
    
    /**
     * 取得新編號(unshop_product)
     * @param  cond：搜尋條件
     * @return number
     */
    public function getSerial($cond=array())
    {
        $serial_num = 0;
        $data = UnshopProduct::where($cond)->orderBy("serial_num","desc")->first("serial_num");
        if($data->exists("serial_num")) {
            $serial_num = $data->serial_num;
        }
        $serial_num += 1;
        return $serial_num;
    }

    /**
     * 刪除檔案及實際路徑
     * @param  file_ids：檔案ID
     * @return boolean
     */
    public function deleteFile($file_ids=array())
    {
        $isSuccess = true;
        if(!empty($file_ids)) {
            $file_datas = UnshopFile::whereIn("id",$file_ids)->get()->toArray();
            if(!empty($file_datas)) {
                foreach($file_datas as $file_data) {
                    $file_id = isset($file_data["id"])?$file_data["id"]:"";
                    //刪除檔案存放路徑
                    $file_path = isset($file_data["path"])?$file_data["path"]:"";
                    if(Storage::exists($file_path)) {
                        Storage::delete($file_path);
                    }
                    //刪除檔案
                    $destroy = UnshopFile::destroy($file_id);
                    if(!$destroy) {
                        $isSuccess = false;
                    }
                }
            }
        }

        return $isSuccess;
    }
    
    /**
     * 取得檔案資料(unshop_file_data)
     * @param  cond：搜尋條件
     * @param  is_detail：是否取得檔案詳細資料
     * @param  orderby：排序欄位
     * @param  sort：排序-遞增、遞減
     * @return array
     */
    public function getFileData($cond=array(),$is_detail=false,$orderby="file_id",$sort="asc")
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
                    $file_details = $this->getData("file",$conds);
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
    public function updateFileData($action_type="add",$data=array())
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
                        $delete = $this->deleteFile($delete_file_ids);

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
                $delete = $this->deleteFile($file_ids);
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

    /**
     * 取得商品資料(unshop_product)
     * @param  cond：搜尋條件
     * @param  orderby：排序欄位
     * @param  sort：排序-遞增、遞減
     * @param  is_page：是否分頁
     * @param  page_cond：分頁條件
     * @return array
     */
    public function getProductData($cond=array(),$orderby="serial",$sort="asc",$is_page=false,$page_cond=array())
    {
        $datas = $all_datas = array();

        //條件欄位
		$cols = array("uuid","user_id","types","is_display","is_delete");
		foreach($cols as $col) {
			if(isset($cond[$col])) {
				if(is_array($cond[$col])) {
					$val = array();
					$val[implode(",",$cond[$col])] = "in";
					$conds[$col] = $val;
				} else if($cond[$col] != "") {
					if(is_numeric($cond[$col])) {
						$conds[$col] = (int)$cond[$col];
					} else {
						$conds[$col] = $cond[$col];
					}
				}
			}
		}
        $all_datas = UnshopProduct::where($conds);
        //關鍵字
        if(isset($cond["keywords"]) && $cond["keywords"] != "") {
            $keywords = $cond["keywords"];
            $conds_or = array("name","serial");
            $all_datas = $all_datas->where(function ($query) use($conds_or,$keywords) {
                foreach($conds_or as $value) {
                    $query->orWhere($value,"like","%".$keywords."%");
                }
            });
        }
        //排序
        $all_datas = $all_datas->orderBy($orderby,$sort);
        //print_r($all_datas->toSql());

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
            $option_datas["types"] = $this->getOptions("code","product_category",true);

            foreach($list_data as $key => $val) {
                $data = array();
                $data = $val;
                //轉換名稱-類別
                $data["types_name"] = isset($option_datas["types"][$data["types"]])?$option_datas["types"][$data["types"]]:"";
                //轉換名稱-是否顯示
                $data["is_display_name"] = isset($option_datas["is_display"][$data["is_display"]])?$option_datas["is_display"][$data["is_display"]]:"";

                //取得檔案
                $conds_file = array();
                $conds_file["data_id"] = isset($val["id"])?$val["id"]:0;
                $conds_file["data_type"] = "product";
                $file_datas = $this->getFileData($conds_file,true);
                //$this->pr($file_datas);
                
                //列表-只取一張
                $data["file_path"] = "";
                if($is_page && !empty($file_datas)) {
                    foreach($file_datas as $file_data) {
                        if(isset($file_data["path"]) && $file_data["path"] != "") {
                            $data["file_path"] = $file_data["path"];
                            continue;
                        }
                    }
                }

                $datas["list_data"][$key] = $data;
                $datas["list_data"][$key]["file_datas"] = $file_datas;
            }
        }
        //$this->pr($datas);

        return $datas;
    }
}
