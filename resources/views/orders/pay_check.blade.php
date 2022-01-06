@extends('layouts.front_base')
@section('title') {{ @$assign_data["title_txt"] }} @endsection
@section('content')
<form id="form_data" name="Newebpay" method="post" action="https://core.newebpay.com/MPG/mpg_gateway">
    <input type="text" name="MerchantID" value="{{ @$assign_data["MerchantID"] }}"><br>
    <input type="text" name="TradeInfo" value="{{ @$assign_data["TradeInfo"] }}"><br>
    <input type="text" name="TradeSha" value="{{ @$assign_data["TradeSha"] }}"><br>
    <input type="text" name="Version" value="{{ @$assign_data["Version"] }}"><br>
    <input id="btn_submit" type="button" value="Submit">
</form>
@endsection

@section('script')
<script>
    $(function () {
        document.getElementById("form_data").submit();
    });
</script>
@endsection