@extends('layouts.base')
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
                        <input type="hidden" id="action_type" name="action_type" value="{{ @$assign_data["action_type"] }}">
                        <input type="hidden" id="user_id" name="user_id" value="{{ @$assign_data["user_id"] }}">
                        <input type="hidden" id="uuid" name="uuid" value="{{ @$assign_data["uuid"] }}">
                        <input type="hidden" id="types" name="types" value="1">
                        <div class="row">
                            <div class="col-3">
                                商品編號：{{ @$assign_data["serial"] }}
                            </div>
                            <div class="col-3">
                                <label>是否顯示：</label>
                                <label class="form-switch">
                                    <input type="checkbox" id="is_display" name="is_display" class="form-control" onclick="changeSwitch('is_display');" {{ @$assign_data["is_display_checked"] }}>
                                    <i></i> <span id="input_switch_text_is_display"></span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label>名稱：</label>
                                <input type="text" id="name" name="name" class="form-control require" value="{{ @$assign_data["name"] }}">
                            </div>
                            <div class="col-4">
                                <label>作者：</label>
                                <input type="text" id="author" name="author" class="form-control require" value="{{ @$assign_data["author"] }}">
                            </div>
                            <div class="col-4">
                                <label>出版社：</label>
                                <input type="text" id="office" name="office" class="form-control require" value="{{ @$assign_data["office"] }}">
                            </div>
                            <div class="col-4">
                                <label>出版日期：</label>
                                <div class="input-group date" id="input_datetimepicker" data-target-input="nearest">
                                    <input type="text" id="publish" name="publish" class="form-control datetimepicker require" data-target="#input_datetimepicker"  value="{{ @$assign_data["publish"] }}" />
                                    <div class="input-group-append" data-target="#input_datetimepicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <label>原價：</label>
                                <input type="number" id="price" name="price" class="form-control require" value="{{ @$assign_data["price"] }}">
                            </div>
                            <div class="col-4">
                                <label>售價：</label>
                                <input type="number" id="sales" name="sales" class="form-control" value="{{ @$assign_data["sales"] }}">
                            </div>
                            <div class="col-12">
                                <label>內容簡介：</label>
                                <textarea id="content" name="content" class="form-control">{!! @$assign_data["content"] !!}</textarea>
                            </div>
                            <div class="col-12">
                                <label><br/>目錄：</label>
                                <textarea id="category" name="category" class="form-control">{!! @$assign_data["category"] !!}</textarea>
                            </div>
                        </div>
                        <div class="row" style="margin-top:25px;margin-bottom:25px">
                            <div class="col-12">
                                <label>上傳圖片：</label>
                            </div>
                            @include('layouts.upload')
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <button type="button" class="btn btn-primary" onclick="productSubmit('{{ @$assign_data["action_type"] }}');">送出</button>
                                <button type="button" class="btn btn-danger" onclick="changeForm('/products')">取消</button>
                            </div>
                            <div class="col-12 col-sm-6 tm-btn-right" style="display:{{ @$assign_data["btn_none"] }}">
                                <button type="button" class="btn btn-danger" onclick="productSubmit('delete');">刪除商品</button>
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
<!-- 編輯器 -->
<script src="{{ mix('packages/ckeditor/ckeditor-init.js') }}"></script>
<script src="{{ mix('packages/ckeditor/ckeditor/ckeditor.js') }}"></script>

<!-- 上傳檔案 -->
<script src="{{ mix('packages/upload/jquery.dm-uploader.min.js') }}"></script>
<script src="{{ mix('packages/upload/jquery.dm-uploader-ui.js') }}"></script>

<script>
    $(function () {
        //日期
        $('.datetimepicker').datepicker({
            language: 'zh-TW', //中文化
            format: 'yyyy-mm-dd', //格式
            autoclose: true, //選擇日期後就會自動關閉
            todayHighlight: true //今天會有一個底色
        });

        //是否顯示
        changeSwitch('is_display');

        //編輯器
        CKEDITOR.replace('content');
        CKEDITOR.replace('category');

        //上傳檔案
        uploadFile();
    });
</script>
@endsection
