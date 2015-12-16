@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', 
['scripts' => array(
  HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), 
  HTML::script('assets/js/highcharts.js'), 
  HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
  HTML::script('assets/js/script.helper.js'), 
  HTML::script('assets/js/visual.js'))
])
@section('content')

<div class="row">
  <div class="col-lg-offset-2 col-lg-8">
    
    <h1>Payment for Invoice Number</h1>
    <p>({{ $invoice_number }})</p>

    <form id="checkout" method="post" action="/checkout/{{ $invoice_number }}">
      <div id="payment-form"></div>
      <input class="btn btn-primary" type="submit" value="Pay $10">
    </form>
  </div>
</div>
<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
<script>
  var clientToken = "{{ $bt_token }}"; 
  braintree.setup(clientToken, "dropin", {
    container: "payment-form"
  });
</script>

@stop

