@extends('layouts.base')
@section('title') 登入 @endsection
@section('content')
<div class="row tm-mt-big">
    <div class="col-12 mx-auto tm-login-col">
        <div class="bg-white tm-block">
            <div class="row">
                <div class="col-12 text-center">
                    <i class="fas fa-3x fa-tachometer-alt tm-site-icon text-center"></i>
                    <h2 class="tm-block-title mt-3">登入</h2>
                </div>
            </div>
            <div class="row mt-2">
                @if($errors->any())
                    @foreach($errors->all() as $message)
                        <div id="msg_error" class="col-12 alert alert-danger" role="alert">{{ $message }}</div>
                    @endforeach
                @endif
                <div class="col-12">
                    <form id="form_data" method="post" class="tm-login-form" action="{{ route('users.login') }}">
                        @csrf
                        <div class="input-group">
                            <label for="username" class="col-xl-2 col-lg-2 col-md-2 col-sm-5 col-form-label">帳號</label>
                            <input type="email" id="username" name="username" class="form-control require" placeholder="電子郵件">
                        </div>
                        <div class="input-group mt-3">
                            <label for="password" class="col-xl-2 col-lg-2 col-md-2 col-sm-5 col-form-label">密碼</label>
                            <input type="password" id="password" name="password" class="form-control require">
                        </div>
                        <div class="input-group mt-3">
                            <div class="col-2"></div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-primary d-inline-block mx-auto">登入</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-primary d-inline-block mx-auto" onclick="changeForm('/users/forget')">忘記密碼</button>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-primary d-inline-block mx-auto" onclick="changeForm('/users/create')">註冊</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection