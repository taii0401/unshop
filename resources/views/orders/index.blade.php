@extends('layouts.front_base')
@section('title') {{ @$assign_data["title_txt"] }} @endsection
@section('css')
<style>
    .media-boxes {
        max-width: 95%;
        margin: 0 auto;
    }

    .media { margin-bottom: 35px; }
    .media-body { 
        display: flex; 
    }

    .tm-bg-gray { background-color: #F2F2F2; }
    .tm-bg-blue { background-color: #3aabd0; }
    
    .tm-text-white { color: #fff; }
    .tm-text-blue { color: #3aabd0; }

    .tm-description-box { 
        width: 100%;
        padding: 30px 35px; 
    }
    
    .tm-buy-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        width: 140px;
    }
    
    .tm-buy {
        font-weight: 400;
        width: 100%;
        padding: 20px 40px;
    }

    .tm-price-tag {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: 400;
    }
</style>
@endsection
@section('content')
<div class="row tm-content-row tm-mt-big">
    <div class="col-xl-12 col-lg-12 tm-md-12 tm-sm-12 tm-col">
        <div class="bg-white tm-block h-100">
            <div class="row">
                <div class="form-group col-md-4 col-sm-12">
                    <div class="input-group">
                        <input type="text" id="keywords" name="keywords" class="form-control search_input_data" placeholder="訂單編號" value="{{ @$assign_data["keywords"] }}">
                        <span class="input-group-btn">
                            <button class="btn btn-secondary" onclick="getSearchUrl('{{ @$assign_data["search_link"] }}');"><i class="fas fa-search"></i></button>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="hidden" id="orderby" name="orderby" class="form-control search_input_data" value="{{ @$assign_data["orderby"] }}">
                    <div class="dropdown btn-group">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            排序
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if(isset($option_datas["orderby"]))    
                                @foreach($option_datas["orderby"] as $key => $val) 
                                <a class="dropdown-item @if($assign_data["orderby"] == $key) active @endif" href="#" onclick="$('#orderby').val('{{ @$key }}');getSearchUrl('{{ @$assign_data["search_link"] }}');">{{ @$val }}</a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="tm-table-mt tm-table-actions-row">
                <div class="tm-table-actions-col-left">
                    
                </div>
                <div class="tm-table-actions-col-right">
                    @include('layouts.page')
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped tm-table-striped-even mt-3"  style="vertical-align: middle;">
                    <thead>
                        <tr class="tm-bg-gray">
                            <th scope="col" class="text-center" style="width:15%;">訂單編號</th>
                            <th scope="col" class="text-center">訂購日期</th>
                            <th scope="col" class="text-center" style="width:12%;">狀態</th>
                            <th scope="col" class="text-center" style="width:12%;">配送方式</th>
                            <th scope="col" class="text-center" style="width:12%;">付款方式</th>
                            <th scope="col" class="text-center" style="width:12%;">金額</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data) 
                        <tr>
                            <td class="text-center">
                                <a href="#" onclick="changeForm('/orders/detail?order_uuid={{ @$data["uuid"] }}');">{{ @$data["serial"] }}</a>
                            </td>
                            <td class="text-center">{{ @$data["create_time"] }}</td>
                            <td class="text-center">{{ @$data["status_name"] }}</td>
                            <td class="text-center">{{ @$data["send_name"] }}</td>
                            <td class="text-center">{{ @$data["payment_name"] }}</td>
                            <td class="text-center">{{ @$data["total"] }}元</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tm-table-mt tm-table-actions-row">
                <div class="tm-table-actions-col-left">
                    
                </div>
                <div class="tm-table-actions-col-right">
                    @include('layouts.page')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection