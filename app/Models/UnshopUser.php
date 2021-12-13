<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnshopUser extends Model
{
    use HasFactory;

    protected $table = 'unshop_user'; //指定資料表名稱
    public $timestamps = false; 
    protected $primaryKey = 'id'; //主鍵，Model會另外自動加入id
    protected $fillable = [
        'uuid','user_id','short_link','name','sex','birthday','phone','address','file_id','is_delete','create_time','modify_time',
    ];
}
