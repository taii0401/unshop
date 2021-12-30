<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnshopFileData extends Model
{
    use HasFactory;

    protected $table = 'unshop_file_data'; //指定資料表名稱
    public $timestamps = false; 
    protected $primaryKey = 'id'; //主鍵，Model會另外自動加入id
    protected $fillable = [
        'data_id','data_type','file_id','create_by','create_time','modify_by','modify_time',
    ];
}
