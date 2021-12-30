@extends('layouts.front_base')
@section('title') {{ @$title_txt }} @endsection
@section('css')
<style>
    /*圖片輪播 START*/
    * {
        padding:0px;
        margin:0px;
        border:0px;
    }
    li {
        list-style-type:none;
    }
    a {
        text-decoration:none;
    }
    #wrapper {
        margin:20px auto;
    }
    #show-area {
        width:100%;
        height:450px;
        position:relative;
        margin:0px auto;
        overflow:hidden;
    }
    #show-area ul {
        position:relative;
        width:5300px;
        height:450px;
        right:0;
    }
    #show-area ul li {
        float:left;
        width:590px;
    }
    #indicator {
        width:120px;
        text-align:center;
        position:absolute;
        top:400px;
        left:0;
        right: 0;
        margin: auto;
        z-index:1;
    }
    #indicator div {
        height:12px;
        width:12px;
        border-radius:100%;
        background-color:#ccc;
        float:left;
        margin-top:10px;
        margin-left:5px;
        opacity:0.9;
        filter:Alpha(opacity=90);
    }
    #button-left,#button-right {
        position: absolute;
        width: 50px;
        height: 110px;
        z-index: 2;
        background-color: #333;
        font-size: 40px;
        color: #FFFFFF;
        text-align: center;
        line-height: 110px;
        opacity:0;
        filter:Alpha(opacity=50);
        cursor: default;
    }
    #button-left {
        top: 140px;
        left: 0px;
    }
    #button-right {
        top: 140px;
        left: 87%;
    }
    .onclick {
        background-color:#3aabd0 !important;
    }
    /*圖片輪播 END*/
</style>
@endsection
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
                    <div class="row">
                        <div class="col-4">
                            <div class="wrapper"> 
                                <div id="show-area"> 
                                    <ul>
                                        @if(isset($assign_data["file_datas"]))
                                            @foreach($assign_data["file_datas"] as $key => $val)
                                            <li><img src="{{ @$val["url"] }}" width="450" height="400"></li>
                                            {{ @$val["url"] }}
                                            @endforeach
                                        @endif
                                        <li><img src="/storage/app/public/files/1/b6baddd1_20211230203852.jpg" width="450" height="400"></li>
                                    </ul>
                                    <div id="button-left" title="上一張"><</div>
                                    <div id="button-right" title="下一張">></div>
                                    <div id="indicator"></div>
                                </div>   
                            </div>
                        </div>
                        <div class="col-8">
                            <table class="table-detail">
                                <thead>
                                    <tr>
                                        <td class="detail-title" style="width:150px;">
                                        <img src="url('storage/files/1/b6baddd1_20211230203852.jpg')" width="450" height="400">
                                        產品編號：</td>
                                        <td class="detail-text">{{ @$assign_data["serial"] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="detail-title">名稱：</td>
                                        <td class="detail-text">{{ @$assign_data["name"] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="detail-title">作者：</td>
                                        <td class="detail-text">{{ @$assign_data["author"] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="detail-title">出版社：</td>
                                        <td class="detail-text">{{ @$assign_data["office"] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="detail-title">出版日期：</td>
                                        <td class="detail-text">{{ @$assign_data["publish"] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="detail-title">原價：</td>
                                        <td class="detail-text">{{ @$assign_data["price"] }}元</td>
                                    </tr>
                                    <tr>
                                        <td class="detail-title">售價：</td>
                                        <td class="detail-text"><span style="color: red;">{{ @$assign_data["sales"] }}元</span></td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="col-12">
                            <span class="detail-title">內容簡介：</span>
                            <div class="col-12 detail-content-div">
                                {!! @$assign_data["content"] !!}
                            </div>
                        </div>
                        <div class="col-12" style="margin-top:25px;margin-bottom:25px">
                            <span class="detail-title">目錄：</span>
                            <div class="col-12 detail-content-div">
                                {!! @$assign_data["category"] !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <button type="button" class="btn btn-primary" onclick="changeForm('/fronts/my_page/{{ @$assign_data["short_link"] }}')">返回</button>
                        </div>
                        <div class="col-12 col-sm-6 tm-btn-right"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function() {
        //移至圖片時，顯示左右兩邊的按鈕，移開時，則隱藏按鈕
        $("#show-area").mouseenter(function () {
            $("#button-right,#button-left").css({opacity:0.5});
        });
        $("#show-area").mouseleave(function () {
            $("#button-right,#button-left").css({opacity:0});
        });
 
        var i = 0;
        var imgWidth = $("#show-area ul li").width();
        var clone = $("#show-area ul li").first().clone(true);
        $("#show-area ul").append(clone);

        //左邊按鈕
        $("#button-left").click(function () {
            toLeft();
        });
        //右邊按鈕
        $("#button-right").click(function () {
            toRight();
        });
 
        //圖片數量
        var size = $("#show-area ul li").length;
        //添加圖片圓點按鈕
        for(var j = 0;j < (size-1);j++) {
            $("#indicator").append("<div></div>");
        }
        //圖片圓點
        $("#indicator div").eq(i).addClass("onclick");
        //圖片圓點-滑鼠滑過則切換
        $("#indicator div").hover(function () {
            i = $(this).index();
            //clearInterval(timer);
            $("#show-area ul").stop().animate({left:-i*imgWidth});
            $(this).addClass("onclick").siblings().removeClass("onclick");
        },function () {
            /*timer = setInterval(function () {
                toRight();
            },2000)*/
        });
 
        //自動輪播
        /*var timer = setInterval(function () {
            toRight();
        },2000);*/
 
        //左邊按鈕
        function toLeft() {
            i--;
            if(i == -1) {
                $("#show-area ul").css({left:-(size-1)*imgWidth});
                i = size-2;
            }
            $("#show-area ul").animate({left:-i*imgWidth},1000);
            $("#indicator div").eq(i).addClass("onclick").siblings().removeClass("onclick");
        }
        //右邊按鈕
        function toRight() {
            i++;
            if(i == size) {
                $("#show-area ul").css({left:0});
                i = 1;
            }
            $("#show-area ul").stop().animate({left:-i*imgWidth},1000);
            //设置下面指示器的颜色索引
            if(i == size-1) {
                $("#indicator div").eq(0).addClass("onclick").siblings().removeClass("onclick");
            } else {
                $("#indicator div").eq(i).addClass("onclick").siblings().removeClass("onclick");
            }
        }
    });
    
</script>
@endsection
