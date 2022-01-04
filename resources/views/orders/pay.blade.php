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
                                <label>選擇配送方式</label>
                                <div class="col-12">
                                    @if(isset($option_datas["send"]))    
                                        @foreach($option_datas["send"] as $key => $val) 
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="send" id="send_{{ @$key }}" value="{{ @$key }}" @if($assign_data["send"] == $key) checked @endif >
                                            <label class="form-check-label" for="inlineRadio1">{{ @$val }}</label>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>              
                            </div> 
                            <div class="col-6">
                                <label>選擇付款方式</label>
                                <div class="col-12">
                                    @if(isset($option_datas["payment"]))    
                                        @foreach($option_datas["payment"] as $key => $val) 
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment" id="payment_{{ @$key }}" value="{{ @$key }}" @if($assign_data["payment"] == $key) checked @endif >
                                            <label class="form-check-label" for="inlineRadio1">{{ @$val }}</label>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>              
                            </div>  
                        </div>
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
                            <div class="col-12 col-sm-6">
                                <button type="button" class="btn btn-primary" onclick="changeForm('/orders/cart');">上一步</button>
                                <button type="button" class="btn btn-danger" onclick="orderSubmit('add')">下一步</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection