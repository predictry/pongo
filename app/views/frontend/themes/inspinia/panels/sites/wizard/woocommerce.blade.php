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
  <h1>Integrate Predictry in WOOCOMMERCE</h1>

  <p>We provide an integration plugin for woocommerce platform with proper functionalities and you can follow these steps to install it on your site.</p>

  <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_1">
    <h2 class="step_head">STEP 1: Download The Plugin</h2>
    <p>
      First you need to download the plugin from our server into your local computer. The download url is linked <a href="/files/wordpress/plugin.zip">here</a>. Open your wordpress dashboard and go to
      plugin session under <strong>[ Plugins >> Add New ]</strong>.

      <img src="/assets/img/files/srcst_one.png" class="img-responsive " style="max-width: 600px; width: 100%;"/>

      Upload the zipped plugin you just downloaded.

      <img src="/assets/img/files/srcst_two.png" class="img-responsive " style="max-width: 600px; width: 100%;"/>

      Activate the plugin.

      <img src="/assets/img/files/srcst_three.png" class="img-responsive " style="max-width: 600px; width: 100%;"/>
      
    </p>    
  </div>

  <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_2">
    <h2 class="step_head">STEP 2: Fill in the credentials</h2>
    <p>
      Under the <strong>[ Plugins >> Installed Plugins ]</strong> and find "<strong>Predictry Engine</strong>" and press "<strong>Settings.</strong>"

      <img src="/assets/img/files/srcst_four.png" class="img-responsive " style="max-width: 600px; width: 100%;"/>
      
      Put your tenant ID and secrete keys accordingly and adjust the number of items you want to show on front. Then Press "Save".
      
      <img src="/assets/img/files/srcst_five.png" class="img-responsive " style="max-width: 600px; width: 100%;"/>

    </p>
  </div>

  <p>After the plugin is successfully installed, we will start tracking the site activities and product details. As a matter of time, you can see our recommended items displayed under the product detail.</p>
</div>
@stop
