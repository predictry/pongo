<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>Sites Overview</h5>
    </div>
    <div class="ibox-content sites-overview">
        <div class="panel-body">
            <div class="panel-group" id="accorSiteOverview">
                @foreach($sites as $site)

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accorSiteOverview" href="#{{$site->name}}">{{$site->name}}</a>
                        </h5>
                    </div>
                    <div id="{{$site->name}}" class="panel-collapse collapse" data-site="{{$site->name}}">
                        <div class="panel-body">
                            {{--@include('admin.panels.dashboard.overview_summary', ['overviews' => $overviews])--}}
                        </div>
                    </div>
                </div>

                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">

</script>