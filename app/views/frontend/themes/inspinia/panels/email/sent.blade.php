@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', [
    'scripts' => [
        HTML::script('assets/js/script.helper.js'),
        HTML::script('assets/js/data_collection.js'),
        HTML::script('assets/js/prism.js'),
        HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'),
        HTML::script('assets/js/moment.min.js'),
        HTML::script('assets/js/daterangepicker.js'),
        HTML::script('assets/js/highcharts.js'),
        HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
        HTML::script('assets/js/bootstrap-datetimepicker.min.js'),
        HTML::script('assets/js/script.helper.js'),
        HTML::script('assets/js/script.panel.filters.js'),
        HTML::script('assets/js/visual.js'),
    ]
])
@section('content')
    @include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Email Targeting' => 'v2/email/home']])
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="float-e-margins">
                    <div class="ibox-title"><h5>Email Targeting Result</h5></div>
                    <div class="ibox-content">
                        <p>{{ $message }}</p>
                        @if (isset($validationErrors))
                            <ul>
                            @foreach ($validationErrors as $validationError)
                                <li>{{ $validationError or "" }}</li>
                            @endforeach
                            </ul>
                        @endif
                        <button type="button" class="btn btn-primary fa fa-home" onclick="location.href='home'">&nbsp;Home</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
