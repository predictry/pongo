@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', 
['scripts' => array(HTML::script('assets/js/script.helper.js'), 
              HTML::script('assets/js/script.panel.items.js'))])
@section('content')
  
  <div class="row items_wrapper">
    @foreach($items as $item)
    <div class="col-md-4 item_wrapper">
      
      <div class="ibox float-e-margins">
        <div class="ibox-title">
          <h5>{{ $item['name'] }}</h5>
        </div>

        <div class="ibox-content">
          <img class="item_image" src="{{ $item['img_url'] }}" />
          <h4>Category: {{ $item['category'] }}</h4>
          
        </div>

      </div>
    
    </div>
    @endforeach
  </div>

@include('frontend.partials.viewmodal')	
@stop
