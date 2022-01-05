@extends('layouts.front_base')
@section('title') {{ @$assign_data["title_txt"] }} @endsection
@section('content')
<form id="form_data" class="tm-signup-form" method="post">
    @csrf
    <input type="hidden" id="action_type" name="action_type" value="delete">
    <input type="hidden" id="user_id" name="user_id" value="{{ @$assign_data["user_id"] }}">
    <input type="hidden" id="product_id" name="product_id" value="">
    <input type="hidden" id="amount" name="amount" value="">
</form>
<div class="row tm-content-row tm-mt-big">
    <div class="col-xl-12 col-lg-12 tm-md-12 tm-sm-12 tm-col">
        <div class="bg-white tm-block h-100">
            <div class="row">
                <div class="col-12">
                    <h2 class="tm-block-title">{{ @$assign_data["title_txt"] }}</h2>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped tm-table-striped-even mt-3"  style="vertical-align: middle;">
                    <thead>
                        <tr class="tm-bg-gray">
                            <th scope="col" class="text-center" style="width:12%;">商品編號</th>
                            <th scope="col" class="text-center" style="width:10%;">圖片</th>
                            <th scope="col" class="text-center">商品名稱</th>
                            <th scope="col" class="text-center" style="width:8%;">數量</th>
                            <th scope="col" class="text-center" style="width:8%;">售價</th>
                            <th scope="col" class="text-center" style="width:10%;">小計</th>
                            <th scope="col" class="text-center" style="width:8%; display:{{ @$assign_data["order_none"] }};">刪除</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data) 
                        <tr>
                            <td class="text-center">
                                <a href="#" target="_blank" class="tm-bg-blue tm-text-white tm-buy" onclick="changeForm('{{ @$data["product_link"] }}');">{{ @$data["serial"] }}</a>
                            </td>
                            <td class="text-center"><img src="{{ @$data["file_url"] }}" width="auto" height="80px"></td>
                            <td class="tm-product-name">{{ @$data["name"] }}</td>
                            <td class="text-center" style="display:{{ @$assign_data["order_none"] }};">
                                <input type="number" min="0" id="amount_{{ @$data["id"] }}" name="amount[]" value="{{ @$data["amount"] }}" style="width: 50px;" onchange="cartChangeTotal('{{ @$data["id"] }}')">
                            </td>
                            <td class="text-center" style="display:{{ @$assign_data["cart_none"] }};">{{ @$data["amount"] }}</td>
                            <td class="text-center">
                                <input type="hidden" id="price_{{ @$data["id"] }}" value="{{ @$data["price"] }}">    
                                {{ @$data["price"] }}
                            </td>
                            <td class="text-center">
                                <input type="hidden" id="subtotal_col_{{ @$data["id"] }}" name="subtotal[]" value="{{ @$data["subtotal"] }}">
                                <span id="subtotal_{{ @$data["id"] }}">{{ @$data["subtotal"] }}</span>元
                            </td>
                            <td class="text-center" style="display:{{ @$assign_data["order_none"] }};">
                                <div class="col">
                                    <div class="btn-action">
                                        <i class="fas fa-trash-alt tm-trash-icon" onclick="$('#product_id').val('{{ @$data["id"] }}');cartSubmit('delete');"></i>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table table-hover table-striped tm-table-striped-even mt-3"  style="vertical-align: middle;">
                    <thead>
                        <tr class="tm-bg-gray">
                            <th scope="col">合計：<span id="total">{{ @$assign_data["total"] }}</span>元</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="row">
                <div class="col-12 col-sm-6"></div>
                <div class="col-12 col-sm-6 tm-btn-right" style="display:{{ @$assign_data["order_none"] }};">
                    <button type="button" class="btn btn-primary" onclick="changeForm('/')">繼續購買</button>
                    <button type="button" class="btn btn-danger" style="display:{{ @$assign_data["btn_none"] }};" onclick="changeForm('/orders/pay')">結帳</button>
                </div>
                <div class="col-12 col-sm-6 tm-btn-right" style="display:{{ @$assign_data["cart_none"] }};">
                    <button type="button" class="btn btn-primary" onclick="changeForm('/orders')">返回</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection