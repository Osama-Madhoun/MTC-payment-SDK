<form style="display: none;" name="paymentForm" id="paymentForm" action="https://e-commerce.bop.ps/EcomPayment/RedirectAuthLink" method="post">



    <input type="hidden" name="Version" value="{{$data['BOP_VERSION']}}">
    <input type="hidden" name="MerID" value="{{$data['BOP_MER_ID']}}">
    <input type="hidden" name="AcqID" value="{{$data['BOP_ACQ_ID']}}">
    <input type="hidden" name="MerRespURL" value="{{ route($model->verify_route_name, ["payment" => "bop"]) }}">
    <input type="hidden" name="PurchaseAmt" value="{{$data['formattedPurchaseAmt']}}">
    <input type="hidden" name="PurchaseCurrency" value="{{$data['BOP_CURRENCY']}}" />
    <input type="hidden" name="PurchaseCurrencyExponent" value="{{$data['CURRENCY_Exp']}}">
    <input type="hidden" name="OrderID" value="{{$data['payment_id']}}">
    <input type="hidden" name="CaptureFlag" value="{{$data['BOP_CAPTURE_FLAG']}}">
    <input type="hidden" name="Signature" value="{{$data['base64Sha1Signature']}}">
    <input type="hidden" name="SignatureMethod" value="{{$data['signatureMethod']}}">


</form>

<script type="text/javascript">
    document.forms["paymentForm"].submit();
</script>