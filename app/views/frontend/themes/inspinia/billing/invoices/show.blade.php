@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', 
['scripts' => array(
  HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), 
  HTML::script('assets/js/moment.min.js'), 
  HTML::script('assets/js/daterangepicker.js'), 
  HTML::script('assets/js/highcharts.js'), 
  HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
  HTML::script('assets/js/bootstrap-datetimepicker.min.js'), 
  HTML::script('assets/js/script.helper.js'), 
  HTML::script('assets/js/script.panel.filters.js'),
  HTML::script('assets/js/visual.js'))
])
@section('content')

<div class="row">

  <div class="col-lg-6">
    <h1>Invoice Number</h1>
    <p>{{ $invoice_number }}</p>
   
    
        <table class='table table-bordered invoice'>
        <thead>
          <tr>
            <th>No</td>
            <th>Description</td>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>
              {{ $invoice_description }} for {{ $current_site }}
            </td>
            <td>
              ${{ $invoice_amount }} USD
            </td>
          </tr>
      
          <tr>
            <td></td>
            <td class="total">Total</td>
            <td>
              ${{ $total }} USD
            </td>
          </tr>
        </tbody>
        </table>      
        
    
    <div class="ibox">
      <div class="ibox-content">
        <h1>Payment</h1>

        <form id="checkout" method="post" action="/checkout/{{ $invoice_number }}">
          <div id="payment-form"></div>
          <input class="btn btn-primary btn-lg" type="submit" value="Pay ${{ $invoice_amount }}">
        </form>
      </div>
    </div>
  </div>

<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
<script>
  var clientToken = "{{ $bt_token }}"; 
  braintree.setup(clientToken, "dropin", {
    container: "payment-form",
    paypal: {
      singleUse: true,
      amount: {{ $invoice_amount }},
      currency: 'USD'
    }
  });
</script>
</div>



@stop
