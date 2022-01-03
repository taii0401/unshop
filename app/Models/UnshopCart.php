<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnshopCart extends Model
{
    use HasFactory;

    protected $table = 'unshop_cart'; //指定資料表名稱
    public $timestamps = false; 
    protected $primaryKey = 'id'; //主鍵，Model會另外自動加入id
    protected $fillable = [
        'user_id','product_id','amount','create_time','modify_time',
    ];
}
