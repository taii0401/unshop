<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnshopCode extends Model
{
    use HasFactory;

    protected $table = 'unshop_code'; //指定資料表名稱
    public $timestamps = false; 
    protected $primaryKey = 'id'; //主鍵，Model會另外自動加入id
    protected $fillable = [
        'types','code','name','cname','is_delete','is_display',
    ];
}
