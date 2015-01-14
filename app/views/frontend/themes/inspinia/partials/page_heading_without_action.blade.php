<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-6">
        <h2>{{$pageTitle}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{URL::to('v2/home')}}">Dashboard</a>
            </li>
            <li class="active">
                <strong>{{$currentPage or ''}}</strong>
            </li>
        </ol>
    </div>
</div>  