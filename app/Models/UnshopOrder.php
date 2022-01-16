<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnshopOrder extends Model
{
    use HasFactory;

    protected $table = 'unshop_order'; //指定資料表名稱
    public $timestamps = false; 
    protected $primaryKey = 'id'; //主鍵，Model會另外自動加入id
    protected $fillable = [
        'uuid','user_id','serial_code','serial_num','serial','name','phone','address','total','payment','send','status','create_time','modify_time',
    ];

    /**
     * 取得新編號
     * @return number
     */
    public static function getSerial()
    {
        $serial_num = 0;
        $data = UnshopOrder::orderBy("serial_num","desc")->first("serial_num");
        if(isset($data) && $data->exists("serial_num")) {
            $serial_num = $data->serial_num;
        }
        $serial_num += 1;
        return $serial_num;
    }

    /**
     * 取得訂單資料
     * @param  cond：搜尋條件
     * @param  orderby：排序欄位
     * @param  sort：排序-遞增、遞減
     * @return data
     */
    public static function getOrder($cond=array(),$orderby="serial",$sort="asc")
    {
        $all_datas = $conds = $conds_in = array();
        
        //條件欄位
		$cols = array("id","uuid","user_id","serial","payment","send","status");
		foreach($cols as $col) {
			if(isset($cond[$col])) {
				if(is_array($cond[$col])) {
					$conds_in[$col] = $cond[$col];
				} else if($cond[$col] != "") {
					if(is_numeric($cond[$col])) {
						$conds[$col] = (int)$cond[$col];
					} else {
						$conds[$col] = $cond[$col];
					}
				}
			}
		}
        $all_datas = self::where($conds);
        //搜尋條件
        if(!empty($conds_in)) {
            foreach($conds_in as $key => $val) {
                $all_datas = $all_datas->whereIn($key,$val);
            }
        }
        //關鍵字
        if(isset($cond["keywords"]) && $cond["keywords"] != "") {
            $keywords = $cond["keywords"];
            $conds_or = array("serial");
            $all_datas = $all_datas->where(function ($query) use($conds_or,$keywords) {
                foreach($conds_or as $value) {
                    $query->orWhere($value,"like","%".$keywords."%");
                }
            });
        }
        //排序
        $all_datas = $all_datas->orderBy($orderby,$sort);
        //print_r($all_datas->toSql());

        return $all_datas;
    }
}
