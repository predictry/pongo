<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-6">
        <h2>{{$pageTitle}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{URL::to('home')}}">Dashboard</a>
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
</div>  