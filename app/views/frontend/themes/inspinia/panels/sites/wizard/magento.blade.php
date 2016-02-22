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
      Download the plugin from our <a href="/files/magento/release.tgz">server</a> and go to System >> Magento Connect >> Magento Connect Manager.</p>

      <img src="/assets/img/files/srcm_one.png" class="img-responsive" style="max-width: 600px; width: 100%;" />
    </p>    
  </div>

  <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_2">
    <h2 class="step_head">STEP 2: Install The Plugin</h2>
    <p>
      Upload the zipped plugin you just downloaded to Magento Connect Manager. Browser the zip file by pressing "Choose File" by "Direct File Upload".

      <img src="/assets/img/files/srcm_two.png" class="img-responsive" style="max-width: 600px; width: 100%;" />

      If the installation is successfull, a message would be shown under the console.

      <img src="/assets/img/files/srcm_three.png" class="img-responsive" style="max-width: 600px; width: 100%;" />
    </p>
  </div>

  <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_2">
    <h2 class="step_head">STEP 3: Fill in the credentials</h2>
    <p> Go to System >> Configuration >> Predictry Recommendation and fill in the credentials to start using our service.Make sure plugin is enabled.</p>
      <img src="/assets/img/files/srcm_four.png" class="img-responsive" style="max-width: 600px; width: 100%;" />
    <p>supported magento versions: </p> 
  </div>
   
  <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_2">
    <h2 class="step_head">STEP 4: View the source of your site to check predictry script is present</h2>
    <img src="/assets/img/files/srcm_five.png" class="img-responsive" style="max-width: 600px; width: 100%;" /> 
  </div>

  <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_2">
    <h2 class="step_head">STEP 5:If everything is ok data will appear in 24 hours on your dashboard</h2>
    <p>Recommendations will be shown when enough data is collected. Email support@predicty.com if you need any help.</p>
  </div>

  <div class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12" id="woo_2">
    <h2 class="step_head">STEP 6:Add Predictry widget to display recommendation</h2>
    <p>
      Select <strong>CMS</strong>, <strong>Widgets</strong> menu from Magento Admin Panel and click on <strong>Add New Widget Instance</strong> button.
    </p>

    <p>In <em>Type</em> drop down, select <strong>Predictry Recommendation</strong>.</p>
    <p>In <em>Design Package/Theme</em> drop down, select <em>default/default</em> and click on <strong>Continue</strong> button.</p>

    <p>In <em>Frontend Properties</em>, <em>Layout Updates</em>, click on <strong>Add Layout Update</strong> button.</p>
    <p>In <em>Display On</em> drop down, select on what page recommendations is supposed to appear.</p>
    <p>In <strong>Block Reference</strong> drop down, select where recommendation should be located on a page (for example, <em>Main Content Area</em>, <em>Page Top</em>, etc).</p>

    <p>
      In <em>Widget Options</em>, <em>Recommendation Type</em>, you can select one of the following recommendation types:
      <ul>
        <li><strong>Recommended Items</strong> - This is the general recommendation based on view and purchase behavior.</li>
        <li><strong>Other Users Who Viewed This Also Viewed</strong> - Recommendation based on view behavior.</li>
        <li><strong>Other Users Who Bought This Also Bought</strong> - Recommendation based on purchase behavior.</li>
        <li><strong>Similar Items</strong> - Recommendation based on similarity of items.</li>
      </ul>
    </p>
  </div>

</div>
@stop
