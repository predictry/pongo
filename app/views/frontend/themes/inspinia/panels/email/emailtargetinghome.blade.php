@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(
HTML::script('assets/js/script.helper.js'),
HTML::script('assets/js/data_collection.js'),
HTML::script('assets/js/prism.js')),
HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'),
HTML::script('assets/js/moment.min.js'),
HTML::script('assets/js/daterangepicker.js'),
HTML::script('assets/js/highcharts.js'),
HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
HTML::script('assets/js/bootstrap-datetimepicker.min.js'),
HTML::script('assets/js/script.helper.js'),
HTML::script('assets/js/script.panel.filters.js'),
HTML::script('assets/js/visual.js'),
HTML::script('http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js')])
@section('content')
    @include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Email Targeting' => 'v2/email/home']])
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <a class="btn btn-w-m btn-primary" href="{{route('emailNew')}}">Start a New Campaign</a>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Your Email Campaigns</h5>
                    </div>
                    <div class="ibox-content">
                        <table class="footable table table-stripped toggle-arrow-tiny default breakpoint footable-loaded" data-page-size="8">
                            <thead>
                            <tr>
                                <th data-toggle="true" class="footable-visible footable-first-column footable-sortable footable-sorted">
                                    Campaign Name
                                    <span class="footable-sort-indicator"></span>
                                </th>
                                <th class="footable-visible footable-sortable">
                                    Date Created
                                    <span class="footable-sort-indicator"></span>
                                </th>
                                <th class="footable-visible footable-sortable">
                                    Modified Date
                                    <span class="footable-sort-indicator"></span>
                                </th>
                                <th class="footable-visible footable-last-column footable-sortable">
                                    Status
                                    <span class="footable-sort-indicator"></span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (isset($paginator))
                            @foreach($paginator as $o)
                                <tr>
                                    <td class="footable-visible footable-first-column"><a href="view/{{$o->id}}"><span class="footable-toggle"></span>{{ $o->campaignname }}</a></td>
                                    <td class="footable-visible">{{ $o->created_at }}</td>
                                    <td class="footable-visible">{{ $o->updated_at }}</td>
                                    <td class="footable-visible footable-last-column">{{ $o->status }}</td>
                                </tr>
                            @endforeach
                            @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop