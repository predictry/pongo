@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(HTML::script('assets/js/script.helper.js'), HTML::script('assets/js/script.panel.items.js'))])
@section('content')
<div class="row items_wrapper">
  <div class="container">
    @foreach ($items as $item) 
      <div class="col-md-4">
        <div class="ibox-title">
          <h4>{{ $site['name'] }}</h4>
        </div>
        
        <div class="ibox-content">
          <img class="img-responsive" src="{{ $site['relative_url_desktop'] }}{{ $item['img_url'] }}" />  
        </div>
      </div>
    @endforeach
  </div>
</div>
@stop
