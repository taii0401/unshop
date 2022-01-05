@extends('layouts.front_base')
@section('title') {{ @$title_txt }} @endsection
@section('content')
<div class="row tm-mt-big">
    <div class="col-12 mx-auto tm-login">
        <div class="bg-white tm-block">
            <div class="row">
                <div class="col-12">
                    <h2 class="tm-block-title">{{ @$title_txt }}</h2>
                </div>
            </div>
            <div class="row">
                <div id="msg_error" class="col-12 alert alert-danger" role="alert" style="display:none;"></div>
                <div id="msg_success" class="col-12 alert alert-success" role="alert" style="display:none;"></div>
                <div class="col-12">
                    <form id="form_data" class="tm-signup-form" method="post">
                        @csrf
                        <input type="hidden" id="action_type" name="action_type" value="{{ @$action_type }}">
                        <input type="hidden" id="uuid" name="uuid" value="{{ @$uuid }}">
                        <div class="row" style="display:{{ $edit_none }};">
                            <div class="col-12">
                                <label>登入帳號(電子郵件)</label>
                                <input type="email" id="username" name="username" class="form-control require" value="{{ @$username }}" {{ $disabled }}>
                            </div>
                        </div>
                        <div class="row" style="display:{{ $edit_pass_none }};">
                            <div class="col-6">
                                <label>登入密碼</label>
                                <input type="password" id="password" name="password" class="form-control require" value="{{ @$password }}" placeholder="不得超過30個英文字或數字">                  
                            </div>
                            <div class="col-6">
                                <label>確認密碼</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control require" value="{{ @$password }}" placeholder="請輸入相同的登入密碼">
                            </div>
                        </div>
                        <div class="row" style="display:{{ $edit_data_none }};">
                            <div class="col-6">
                                <label>商品頁面網址</label>
                                <input type="text" id="short_link" name="short_link" class="form-control require" value="{{ @$short_link }}">
                            </div>
                            <div class="col-6">
                                <label>姓名</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ @$name }}">
                            </div>
                            <div class="col-6">
                                <label>性別</label>
                                <div class="col-12">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="sex" id="sex_1" value="1" {{ @$checked_sex_1 }}>
                                      <label class="form-check-label" for="inlineRadio1">男</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="sex" id="sex_2" value="2" {{ @$checked_sex_2 }}>
                                      <label class="form-check-label" for="inlineRadio2">女</label>
                                    </div> 
                                </div>              
                            </div>                            
                            <div class="col-6">
                                <label>生日</label>
                                <div class="input-group date" id="input_datetimepicker" data-target-input="nearest">
                                    <input type="text" id="birthday" name="birthday" class="form-control datetimepicker" data-target="#input_datetimepicker"  value="{{ @$birthday }}" />
                                    <div class="input-group-append" data-target="#input_datetimepicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label>手機號碼</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{ @$phone }}">                  
                            </div>
                            <div class="col-6">
                                <label>地址</label>
                                <input type="text" id="address" name="address" class="form-control" value="{{ @$address }}">
                            </div>
                        </div> 
                        
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <button type="button" class="btn btn-danger" onclick="userSubmit('delete');" style="display:{{ $btn_none }}">刪除帳號</button>
                            </div>
                            <div class="col-12 col-sm-6 tm-btn-right">
                                <button type="button" class="btn btn-primary" onclick="userSubmit('{{ @$action_type }}');">送出</button>
                                <button type="button" class="btn btn-danger" onclick="changeForm('/users')">取消</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function () {
        $('.datetimepicker').datepicker({
            language: 'zh-TW', //中文化
            format: 'yyyy-mm-dd', //格式
            autoclose: true, //選擇日期後就會自動關閉
            todayHighlight: true //今天會有一個底色
        });
    });
</script>
@endsection
