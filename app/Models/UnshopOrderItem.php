<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnshopOrderItem extends Model
{
    use HasFactory;

    protected $table = 'unshop_order_item'; //指定資料表名稱
    public $timestamps = false; 
    protected $primaryKey = 'id'; //主鍵，Model會另外自動加入id
    protected $fillable = [
        'order_id','product_id','amount','price','total','create_time','modify_time',
    ];
}
