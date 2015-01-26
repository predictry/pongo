@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard')
@section('content')
<div class="main form-group">
    <div class="col-sm-12">
        @include('frontend.partials.notification')
        <h2><?php echo Lang::get("user.site.integration.steps"); ?></h2>
        <hr/>
        <ul class="nav nav-pills nav-justified thumbnail setup-panel">
            <li class="active"><a href="#step-embed-js">
                    <h4 class="list-group-item-heading">Step 1</h4>
                    <p class="list-group-item-text">Embed JS</p>
                </a></li>
            <li class="disabled"><a href="#step-data-collection">
                    <h4 class="list-group-item-heading">Step 2</h4>
                    <p class="list-group-item-text">Data Collection</p>
                </a></li>
        </ul>
    </div>
</div>
<div class="setup-content" id="step-embed-js">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <p>Paste the code below in your site's HTML (preferably as close as possible to the open <code>&lt;head&gt;</code> tag and we're ready to go. (Predictry is designed not to slow down your site)</p>
                <textarea class="form-control js-code" rows="10" onclick="this.select()">
<script type="text/javascript">
                                var _predictry = _predictry || [];
                                (function () {
                                _predictry.push(['setTenantId', "{{ $site->name }}"], ['setApiKey', "{{ $site->api_key }}"]);
                                        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
                                        g.type = 'text/javascript';
                                        g.defer = true;
                                        g.async = true;
                                        g.src = '//d2gq0qsnoi5tbv.cloudfront.net/v2/p.min.js';
                                        s.parentNode.insertBefore(g, s);
                                })();</script>
                </textarea>
                <div class="row">
                    <div class="col-sm-6">
                        <p class="pt20 text-capitalize"><b>We care about site performance</b></p>
                        <p>Our JavaScript is loaded asynchronously in a non-blocking fashion way, meaning that it's loaded while your other page resources are loading. 
                            It doesn't delay visual page rendering, nor does it delay the javascript document ready or <i>onload</i> event.
                            So your users see absolutely no difference in performance.</p>
                    </div>
                    <div class=" col-sm-6 pt20">
                        <button class="btn btn-default pull-right" id="btn-embed-continue">Apply & Continue</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="setup-content" id="step-data-collection">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <p><b>Step 2.1</b> Select the action</p>
                        <div class="panel panel-default pages-wrap">
                            <div class="panel-heading">
                                <h3 class="panel-title">Actions</h3>
                            </div>   
                            <div class="panel-body">
                                <ul class="list-group pages">
                                    <?php
                                    $i            = 1;
                                    $action_names = [];
                                    foreach ($data->sections as $section) {

                                        if (!$section->disabled) {
                                            $actions = $section->actions;
                                            ?>
                                            <li class="list-group-item">
                                                <div class="row toggle" id="dropdown-detail-{{ $i }}" data-toggle="detail-{{ $i }}">
                                                    <div class="col-xs-10">{{ $section->title }}</div>
                                                    <div class="col-xs-2"><i class="fa fa-chevron-down pull-right"></i></div>
                                                </div>
                                                <div id="detail-{{ $i }}">
                                                    <div class="row">
                                                        <ul class="nav nav-stacked nav-pills actions">
                                                            <?php
                                                            foreach ($actions as $action) {
                                                                ?>
                                                                <li id="item-action-{{ $action->name }}">
                                                                    <div class="col-xs-10">
                                                                        <a href="javascript:void();" onclick="getActionProperties('{{ $tenant_id }}', '{{ $action->name }}');">
                                                                            {{ ucwords(str_replace('_', ' ', $action->name)) }}
                                                                            <span class="status"></span>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-xs-2">
                                                                        <i class="fa fa-info-circle" data-toggle="tooltip" role="tooltip" data-placement="right" title="{{ $action->info or "" }}"></i>
                                                                    </div>
                                                                </li>
                                                                <?php
                                                                array_push($action_names, $action->name);
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <p><b>Step 2.2</b> Organize the properties</p>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Properties</h3>
                            </div>
                            <div class="panel-body" id="action_properties">

                                <p class="text-center">No property displayed</p>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <p><b>Step 2.3</b> Snipped Data JS code ready. Assign the data, and place it. That's it!</p>
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">JS</h3>
                                <span class="pull-right">
                                    <!-- Tabs -->
                                    <ul class="nav panel-tabs">
                                        @foreach ($action_names as $name)
                                        <li class=""><a href="#tab_{{ $name }}" data-toggle="tab">{{ ucwords(str_replace('_', ' ', $name)) }}</a></li>
                                        @endforeach
                                    </ul>
                                </span>
                            </div>
                            <div class="panel-body">
                                <div class="tab-content">
                                    @foreach ($action_names as $name)
                                    <div class="tab-pane" id="tab_{{ $name }}">
                                        <textarea class="form-control" style="margin-top: 0;" rows="15" onclick="this.select()"></textarea>
                                        <div class="clearfix pt20">
                                            <p class="pull-left small">Once you have implement the JS embed code, and have send your first action. Validate by clicking button below.
                                                If it's success, you will see green check icon on the right side of the action name.</p>
                                            <a class="btn btn-default pull-right" href="javascript:void();" onclick="checkIfActionImplemented('{{ $tenant_id }}', '{{ $name }}');">Validate</a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix ">
                    <a class="btn btn-default pull-right" id="btn-data-collection-continue" onclick="saveIntegrationConfiguration('{{ $tenant_id }}')">Save Configurations</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
                                    var action_names = <?php echo json_encode($action_names); ?>;
</script>
@stop