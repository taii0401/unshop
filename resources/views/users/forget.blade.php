@extends('layouts.base')
@section('title') 忘記密碼 @endsection
@section('content')
<div class="row tm-mt-big">
    <div class="col-12 mx-auto tm-login-col">
        <div class="bg-white tm-block">
            <div class="row">
                <div class="col-12 text-center">
                    <i class="fas fa-3x fa-tachometer-alt tm-site-icon text-center"></i>
                    <h2 class="tm-block-title mt-3">忘記密碼</h2>
                </div>
            </div>
            <div class="row mt-2">
                <div id="msg_error" class="col-12 alert alert-danger" role="alert" style="display:none;"></div>
                <div id="msg_success" class="col-12 alert alert-success" role="alert" style="display:none;"></div>
                <div class="col-12">
                    <form id="form_data" class="tm-signup-form" method="post">
                        @csrf
                        <div class="input-group mt-3">
                            <label for="username" class="col-xl-2 col-lg-2 col-md-2 col-sm-5 col-form-label">帳號</label>
                            <input type="email" id="username" name="username" class="form-control require" placeholder="電子郵件">
                        </div>
                        <div class="input-group mt-3">
                            <div class="col-3"></div>
                            <div class="col-3">
                                <button type="button" class="btn btn-primary d-inline-block mx-auto" onclick="userForget()">送出</button>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-danger" onclick="changeForm('/users')">取消</button>
                            </div>
                            <div class="col-3"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection