<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//上傳檔案
use Illuminate\Support\Facades\Storage;

class UnshopFile extends Model
{
    use HasFactory;

    protected $table = 'unshop_file'; //指定資料表名稱
    public $timestamps = false; 
    protected $primaryKey = 'id'; //主鍵，Model會另外自動加入id
    protected $fillable = [
        'name','file_name','path','size','types',
    ];

    /**
     * 刪除檔案及實際路徑
     * @param  file_ids：檔案ID
     * @return boolean
     */
    public static function deleteFile($file_ids=array())
    {
        $isSuccess = true;
        if(!empty($file_ids)) {
            $file_datas = self::whereIn("id",$file_ids)->get()->toArray();
            if(!empty($file_datas)) {
                foreach($file_datas as $file_data) {
                    $file_id = isset($file_data["id"])?$file_data["id"]:"";
                    //刪除檔案存放路徑
                    $file_path = isset($file_data["path"])?$file_data["path"]:"";
                    if(Storage::exists($file_path)) {
                        Storage::delete($file_path);
                    }
                    //刪除檔案
                    $destroy = self::destroy($file_id);
                    if(!$destroy) {
                        $isSuccess = false;
                    }
                }
            }
        }

        return $isSuccess;
    }
}
