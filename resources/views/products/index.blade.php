@extends('layouts.base')
@section('title') 商品列表 @endsection
@section('content')
<form id="form_data" class="tm-signup-form" method="post">
    @csrf
    <input type="hidden" id="action_type" name="action_type" value="{{ @$assign_data["action_type"] }}">
    <input type="hidden" id="user_id" name="user_id" value="{{ @$assign_data["user_id"] }}">
    <input type="hidden" id="check_list" name="check_list" value="">
</form>
<div class="row tm-content-row tm-mt-big">
    <div class="col-xl-12 col-lg-12 tm-md-12 tm-sm-12 tm-col">
        <div class="bg-white tm-block h-100">
            <div class="row">
                <div class="form-group col-md-4 col-sm-12">
                    <div class="input-group">
                        <input type="text" id="keywords" name="keywords" class="form-control search_input_data" placeholder="編號、名稱" value="{{ @$assign_data["keywords"] }}">
                        <span class="input-group-btn">
                            <button class="btn btn-secondary" onclick="getSearchUrl('/products');"><i class="fas fa-search"></i></button>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="hidden" id="types" name="types" class="form-control search_input_data" value="{{ @$assign_data["types"] }}">
                    <input type="hidden" id="is_display" name="is_display" class="form-control search_input_data" value="{{ @$assign_data["is_display"] }}">
                    <input type="hidden" id="orderby" name="orderby" class="form-control search_input_data" value="{{ @$assign_data["orderby"] }}">
                    <div class="dropdown btn-group">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            類別
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if(isset($option_datas["types"]))    
                                @foreach($option_datas["types"] as $key => $val) 
                                <a class="dropdown-item @if($assign_data["types"] == $key) active @endif" href="#" onclick="$('#types').val('{{ @$key }}');getSearchUrl('{{ @$assign_data["search_link"] }}');">{{ @$val }}</a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="dropdown btn-group">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            顯示
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if(isset($option_datas["is_display"]))
                                @foreach($option_datas["is_display"] as $key => $val) 
                                <a class="dropdown-item @if($assign_data["is_display"] == $key) active @endif" href="#" onclick="$('#is_display').val('{{ @$key }}');getSearchUrl('{{ @$assign_data["search_link"] }}');">{{ @$val }}</a>
                                @endforeach
                            @endif
                        </div>
                    </div>
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
                <div class="col-md-4 col-sm-12 text-right">
                    <button type="button" class="btn btn-primary" onclick="changeForm('/products/create');">新增</button>
                    <button type="button" class="btn btn-danger check_btn" style="display:none" onclick="productSubmit('delete_list');">刪除</button>
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
                            <th scope="col" class="text-center" style="width:1%;">
                                <div class="custom-control custom-checkbox">
                                    <input id="check_all" type="checkbox" value="all" onclick="checkAll()">
                                    <label for="check_all"></label>
                                </div>
                            </th>
                            <th scope="col" class="text-center" style="width:8%;">編號</th>
                            <th scope="col" class="text-center" style="width:10%;">圖片</th>
                            <th scope="col" class="text-center">名稱</th>
                            <th scope="col" class="text-center" style="width:8%;">類別</th>
                            <th scope="col" class="text-center" style="width:8%;">價錢</th>
                            <th scope="col" class="text-center" style="width:8%;">售價</th>
                            <th scope="col" class="text-center" style="width:8%;">顯示</th>
                            <th scope="col" class="text-center" style="width:12%;">動作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data) 
                        <tr>
                            <td scope="row">
                                <div class="custom-control custom-checkbox">
                                    <input id="checkbox_{{ @$data["uuid"] }}" type="checkbox" value="{{ @$data["uuid"] }}" name="check_list[]" onclick="checkId('{{ @$data["uuid"] }}')" class="check_list">
                                    <label for="checkbox_{{ @$data["uuid"] }}"></label>
                                </div>
                            </td>
                            <td class="text-center">{{ @$data["serial"] }}</td>
                            <td class="text-center"><img src="{{ @$data["file_path"] }}" width="auto" height="80px"></td>
                            <td class="tm-product-name">{{ @$data["name"] }}</td>
                            <td class="text-center">{{ @$data["types_name"] }}</td>
                            <td class="text-center">{{ @$data["price"] }}</td>
                            <td class="text-center">{{ @$data["sales"] }}</td>
                            <td class="text-center">{{ @$data["is_display_name"] }}</td>
                            <td class="text-center">
                                <div class="col">
                                    <div class="btn-action">
                                        <i class="fas fa-edit tm-edit-icon" onclick="changeForm('/products/edit?uuid={{ @$data["uuid"] }}');"></i>
                                    </div>
                                    <div class="btn-action">
                                        <i class="fas fa-trash-alt tm-trash-icon" onclick="$('#check_list').val('{{ @$data["uuid"] }}');productSubmit('delete_list');"></i>
                                    </div>
                                </div>
                            </td>
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