<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnshopProduct extends Model
{
    use HasFactory;

    protected $table = 'unshop_product'; //指定資料表名稱
    public $timestamps = false; 
    protected $primaryKey = 'id'; //主鍵，Model會另外自動加入id
    protected $fillable = [
        'uuid','user_id','types','serial_code','serial_num','serial','name','author','office','publish','price','sales','content','category','click','is_delete','is_display','create_time','modify_time',
    ];
}
