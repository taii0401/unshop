@extends('layouts.front_base')
@section('title') {{ @$assign_data["title_txt"] }} @endsection
@section('content')
<div class="row tm-content-row tm-mt-big">
    <div class="col-xl-12 col-lg-12 tm-md-12 tm-sm-12 tm-col">
        <div class="bg-white tm-block h-100">
            <div class="row">
                <div class="col-12">
                    <h2 class="tm-block-title">{{ @$assign_data["title_txt"] }}</h2>
                </div>
            </div>
            <div class="table-responsive">
                <div id="msg_error" class="col-12 alert alert-danger" role="alert" style="display:{{ @$assign_data["danger_none"] }};">交易失敗</div>
                <div id="msg_success" class="col-12 alert alert-success" role="alert" style="display:{{ @$assign_data["success_none"] }};">交易成功</div>
                <table class="table table-hover table-striped tm-table-striped-even mt-3"  style="vertical-align: middle;">
                    <thead>
                        <tr>
                            <th class="text-center tm-bg-gray">訂單編號：</th>
                            <th>{{ @$assign_data["serial"] }}</th>
                        </tr>
                        <tr>
                            <th class="text-center tm-bg-gray">訂購日期：</th>
                            <th>{{ @$assign_data["create_time"] }}</th>
                        </tr>
                        <tr>
                            <th class="text-center tm-bg-gray">訂單狀態：</th>
                            <th>{{ @$assign_data["status_name"] }}</th>
                        </tr>
                        <tr>
                            <th class="text-center tm-bg-gray">配送方式：</th>
                            <th>{{ @$assign_data["send_name"] }}</th>
                        </tr>
                        <tr>
                            <th class="text-center tm-bg-gray">付款方式：</th>
                            <th>{{ @$assign_data["payment_name"] }}</th>
                        </tr>
                        <tr>
                            <th class="text-center tm-bg-gray">訂購金額：</th>
                            <th>{{ @$assign_data["total"] }}元</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="row">
                <div class="col-12 col-sm-6"></div>
                <div class="col-12 col-sm-6 tm-btn-right"></div>
            </div>
        </div>
    </div>
</div>
@endsection