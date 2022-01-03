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
        'uuid','user_id','name','phone','address','payment','status','create_time','modify_time',
    ];
}
