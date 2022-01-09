@extends('layouts.front_base')
@section('title') {{ @$assign_data["title_txt"] }} @endsection
@section('content')
<form id="form_data" name="Newebpay" method="post" action="https://ccore.newebpay.com/MPG/mpg_gateway">
    <input type="hidden" name="MerchantID" value="{{ $assign_data["MerchantID"] }}"><br>
    <input type="hidden" name="TradeInfo" value="{{ $assign_data["tradeInfo"] }}"><br>
    <input type="hidden" name="TradeSha" value="{{ $assign_data["tradeSha"] }}"><br>
    <input type="hidden" name="Version" value="{{ $assign_data["Version"] }}"><br>
    <input type="submit" value="Submit" style="display:none;">
</form>
@endsection

@section('script')
<script>
    $(function () {
        document.getElementById("form_data").submit();
    });
</script>
@endsection