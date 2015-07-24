@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.blank_dashboard', ['scripts' => [HTML::script('assets/js/script.panel.sites.js')]])
@section('content')
<div class="col-xs-12 main">
    <div class="text-center">
        <h1 class="mb20">Welcome to Predictry.</h1>
        <div class="row mb20">
            <div class="col-xs-offset-3 col-xs-6">
                <p style="line-height: 24px;">Add your website so that we can start tracking and processing recommendations</p>
            </div>
        </div>
    </div>
    <div class="well-create-button-container text-center">
        <a data-toggle="modal" id="btnViewModal" data-target="#viewModal" class="btn btn-primary btn-lg btnViewModal tt">Add New Site</a>
    </div>
</div>
@include('frontend.partials.viewmodalnormal')	
@stop
