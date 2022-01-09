@extends('layouts.front_base')
@section('title') {{ @$assign_data["title_txt"] }} @endsection
@section('content')
<div class="row tm-mt-big">
    <div class="col-12 mx-auto tm-login">
        <div class="bg-white tm-block">
            <div class="row">
                <div class="col-12">
                    <h2 class="tm-block-title">{{ @$assign_data["title_txt"] }}</h2>
                </div>
            </div>
            <div class="row">
                <div id="msg_error" class="col-12 alert alert-danger" role="alert" style="display:none;"></div>
                <div id="msg_success" class="col-12 alert alert-success" role="alert" style="display:none;"></div>
                <div class="col-12">
                    <form id="form_data" class="tm-signup-form" method="post">
                        @csrf
                        <input type="hidden" id="action_type" name="action_type" value="add">
                        <input type="hidden" id="total" name="total" value="{{ @$assign_data["total"] }}">
                        <div class="row">
                            <div class="col-6">
                                <label>收件人-姓名</label>
                                <input type="text" id="name" name="name" class="form-control require" value="{{ @$assign_data["name"] }}">
                            </div>
                            <div class="col-6">
                                <label>收件人-手機號碼</label>
                                <input type="text" id="phone" name="phone" class="form-control require" value="{{ @$assign_data["phone"] }}">                  
                            </div>
                            <div class="col-6">
                                <label>收件人-地址</label>
                                <input type="text" id="address" name="address" class="form-control require" value="{{ @$assign_data["address"] }}">
                            </div>
                        </div> 
                        
                        <div class="row">
                            <div class="col-12 col-sm-6"></div>
                            <div class="col-12 col-sm-6 tm-btn-right">
                                <button type="button" class="btn btn-primary" onclick="changeForm('/orders/cart');">上一步</button>
                                <button type="button" class="btn btn-danger" onclick="orderSubmit('add')">結帳</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection