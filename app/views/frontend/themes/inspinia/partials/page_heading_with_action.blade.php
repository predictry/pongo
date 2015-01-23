<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2><?php echo Lang::get("panel.manage"); ?> <?php echo ucfirst($moduleName) . "(s)"; ?></h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{URL::to('v2/home')}}">Dashboard</a>
            </li>
            @foreach($upper as $key => $uri)
            <li>
                <a href="{{URL::to($uri)}}">{{$key}}</a>
            </li>                
            @endforeach
            <li class="active">
                <strong>{{$pageTitle or ''}}</strong>
            </li>
        </ol>
    </div>
    <div class="col-sm-8">
        <div class="title-action action_buttons">
            @if ($create)
            <a href="{{ URL::to( URL::current() . "/create" ) }}" class="btn btn-primary btn"><i class="fa fa-plus"></i> <?php echo Lang::get("panel.add.new"); ?> {{ $moduleName }}</a>
            @endif
        </div>
    </div>
</div>  