@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.recommendation', ['scripts' => [HTML::script('assets/js/script.helper.js')] ])
@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <a href="{{ URL::to('v2/demo') }}" class="text-capitalize"><i class="fa fa-long-arrow-left"></i> Back to catalog</a>
                </div>
                <div class="ibox-content">

                    <div class="row">

                        <div class="col-lg-6">
                            @if(isset($item_metas['img_url']))
                            <img src="{{ $item_metas['img_url'] }}" class="img-responsive"/>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <h2>{{ $item['name'] }}</h2>
                            <h3>Price: @if(isset($item_metas['price'])) {{ number_format($item_metas['price'], 2) }} @else 0 @endif</h3>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        @include(getenv('FRONTEND_SKINS') . $theme . '.panels.demo.recommended_items')
    </div>
</div>
@stop