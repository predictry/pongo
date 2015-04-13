@extends('admin.layouts.dashboard', ['scripts' => array(HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), HTML::script('assets/js/moment.min.js'), HTML::script('assets/js/daterangepicker.js'), HTML::script('assets/inspinia/js/plugins/switchery/switchery.js'), HTML::script('assets/js/bootstrap-datetimepicker.min.js'), HTML::script('assets/js/script.helper.js'), HTML::script('assets/js/script.admin.panel.dashboard.js'))])
@section('content')
<div class="wrapper wrapper-content">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <div class="pull-right">
                @include(getenv('FRONTEND_SKINS') . $theme . '.panels.filter_date')
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @include('admin.panels.dashboard.overview')
        </div>
    </div>
</div>
@stop
