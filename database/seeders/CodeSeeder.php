<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

//Model
use App\Models\UnshopCode;

class CodeSeeder extends Seeder
{
    /**
     * Seed the application"s database.
     *
     * @return void
     */
    public function run()
    {
        $keys = array("types","code","name","cname","is_delete","is_display");

        $datas = array();
        $datas[] = array("product_category","B","book","書籍",0,1);
        $datas[] = array("product_category","P","product","商品",0,1);
        $datas[] = array("order_send","store","store","7-11取貨",0,0);
        $datas[] = array("order_send","home","home","宅配到府",0,1);
        $datas[] = array("order_pay","CREDIT","CREDIT","信用卡",0,1);
        $datas[] = array("order_pay","WEBATM","WEBATM","WEBATM",0,1);
        $datas[] = array("order_pay","VACC","VACC","ATM 轉帳",0,1);
        $datas[] = array("order_pay","CVS","CVS","超商代碼繳費",0,1);
        $datas[] = array("order_pay","BARCODE","BARCODE","超商條碼繳費",0,1);
        $datas[] = array("order_pay","CVSCOM","CVSCOM","物流服務",0,1);
        $datas[] = array("order_pay","ANDROIDPAY","ANDROIDPAY","Google Pay",0,1);
        $datas[] = array("order_pay","SAMSUNGPAY","SAMSUNGPAY","Samsung Pay",0,1);
        $datas[] = array("order_pay","LINEPAY","LINEPAY","LINE Pay",0,1);
        $datas[] = array("order_pay","ESUNWALLET","ESUNWALLET","玉山Wallet",0,1);
        $datas[] = array("order_pay","TAIWANPAY","TAIWANPAY","台灣Pay",0,1);
        $datas[] = array("order_pay","EZPAY","EZPAY","簡單付電子錢包",0,1);
        $datas[] = array("order_pay","EZPWECHAT","EZPWECHAT","簡單付微信支付",0,1);
        $datas[] = array("order_pay","EZPALIPAY","EZPALIPAY","簡單付支付寶",0,1);

        if(!empty($datas)) {
            foreach($datas as $data_key => $data_val) {
                $data = array();
                $data[$keys[$data_key]] = $data_val;
                UnshopCode::create($data);
            }
        }
    }
}