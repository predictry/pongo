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
<h1>Billing</h1>

<div class="row">
  
  <div class="col-md-12">
    <h2>Current Payment and Charges</h2>

    <table class="table">
      <tbody>
        <tr>
          <td>Latest Payment Received (9/10/2015)</td>
          <td>200 USD</td>
        </tr>
        <tr>
          <td>Total Amount Due (Payment Due 10/11/2015)</td>
          <td>500 USD</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="col-md-12">

    <h2>Billing History</h2>

    <table class="table">
      <thead>
        <tr>
          <td>Invoice Number</td>
          <td>Date</td>
          <td>Amount</td>
          <td>Status</td>
        </tr>
      </thead>
      <tbody>
        @foreach($invoices as $invoice)
        <tr>
          <td>{{ $invoice['invoice_number']}}</td>
          <td>{{ date('d-m-Y', $invoice['created_at']) }}</td>
          <td>{{ $invoice['amount'] }} USD</td>
          <td>
            @if ($invoice['status'])
              <span class="paid">PAID</span>
            @else
              <span class="unpaid">UNPAID</span>
              <a href="/invoice/{{ $invoice['invoice_number'] }}">PAY</a>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@stop
