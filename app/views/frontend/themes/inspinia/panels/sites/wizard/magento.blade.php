@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(
HTML::script('assets/js/script.helper.js'), 
HTML::script('assets/js/data_collection.js'),
HTML::script('assets/js/prism.js')),
HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), 
HTML::script('assets/js/moment.min.js'), 
HTML::script('assets/js/daterangepicker.js'), 
HTML::script('assets/js/highcharts.js'), 
HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
HTML::script('assets/js/bootstrap-datetimepicker.min.js'), 
HTML::script('assets/js/script.helper.js'), 
HTML::script('assets/js/script.panel.filters.js'),
HTML::script('assets/js/visual.js')])
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Sites' => 'v2/sites']])
<div class="sint_wrapper woo_docs row">
  <h1>Integrate Predictry in Magento</h1>
  <p>We provide a simple extension for you to integrate our script into your magento installation.</p>
  
  <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_1">
    <h2 class="step_head">STEP 1: Download The Plugin</h2>
    <p>
      Download the plugin from our server and go to System >> Magento Connect >> Magento Connect Manager.</p>

      <img src="/assets/img/files/srcm_one.png" class="img-responsive" style="max-width: 600px; width: 100%;" />
    </p>    
  </div>

  <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_2">
    <h2 class="step_head">STEP 2: Install The Plugin</h2>
    <p>
      Upload the zipped plugin you just downloaded through Magento Connect. Browser the zip file by pressing "Choose File" by "Direct File Upload".

      <img src="/assets/img/files/srcm_two.png" class="img-responsive" style="max-width: 600px; width: 100%;" />

      If the installation is successfull, a message would be shown under the console.

      <img src="/assets/img/files/srcm_three.png" class="img-responsive" style="max-width: 600px; width: 100%;" />
    </p>
  </div>

   <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_2">
    <h2 class="step_head">STEP 3: Fill in the credentials</h2>
    <p> Go to System >> Configuration >> Predictry Recommendation and fill in the credentials to start using our service.</p>
      <img src="/assets/img/files/srcm_four.png" class="img-responsive" style="max-width: 600px; width: 100%;" />
  </div>
</div>
@stop
