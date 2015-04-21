@extends('frontend.layouts.blankdashboard')
@section('content')
<div class="col-sm-12 col-md-12 main">
    @include('frontend.partials.notification')
    <h2><?php echo Lang::get("user.site.data.collection"); ?></h2>
    <hr/>
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default pages-wrap">
                <div class="panel-heading">
                    <h3 class="panel-title">Pages</h3>
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
<!--                            <div class="clearfix pt20">
                                <p class="pull-left small">Once you have implement the JS embed code, and have send your first action. Validate by clicking button below.
                                    If it's success, you will see green check icon on the right side of the action name.</p>
                                <a class="btn btn-default pull-right" href="javascript:void();" onclick="checkIfActionImplemented('{{ $tenant_id }}', '{{ $name }}');">Validate</a>
                            </div>-->
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop