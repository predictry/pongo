@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.recommendation', ['scripts' => [HTML::script('assets/js/script.helper.js')]])
@section('content')
{{ HTML::script('assets/js/holder.js') }}
<script type="text/javascript">
    function imgError(image) {
        image.onerror = "";
        image.src = "../assets/img/no-image.jpg";
        return true;
    }

</script>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-3">
            @if (count($paginator) > 0)
            <?php
            $counter = 0;
            $divider = 4;

            $divider = count($paginator) <= 12 ? 3 : $divider;
            $divider = count($paginator) <= 8 ? 2 : $divider;
            $divider = count($paginator) <= 4 ? 1 : $divider;
            ?>
            @foreach ($paginator as $obj)

            @if (isset($obj))

            @if ($counter > 1 && $counter % $divider === 0)
        </div><div class='col-sm-3'>
            @endif

            <div class="panel product">
                <img src="@if(isset($metas[$obj->id]) && isset($metas[$obj->id]['img_url'])) {{ $metas[$obj->id]['img_url'] }} @else {{ asset('img/no-image.jpg') }} @endif" class="img-rounded img-responsive" onerror="imgError(this)"/>
                <h4 class="name">@if(isset($metas[$obj->id]) && isset($metas[$obj->id]['name'])) {{ $metas[$obj->id]['name'] }} @endif</h4>
                <h5 class="price">Price: @if(isset($metas[$obj->id]) && isset($metas[$obj->id]['price'])) {{ number_format($metas[$obj->id]['price'], 2) }} @else {{ number_format(0, 2) }} @endif</h5>

                <a href="{{ URL::to('v2/demo/show/' . $obj->id) }}" class="btn btn-primary btn-block btn-sm text-capitalize">Show me recommendation</a>
            </div>

            <?php $counter+=1; ?>
            @endif

            @endforeach
            @endif

        </div>

        <div class="col-sm-12 text-center"> 

            @if ($paginator !== null)
            {{ $paginator->links('frontend.partials.paginator') }}
            @endif
        </div>
    </div>
</div>
@stop