@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(HTML::script('assets/inspinia/js/plugins/chosen/chosen.jquery.js'), HTML::script('assets/js/script.helper.js'), HTML::script('assets/js/script.panel.widgets.js')) ])
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Widgets' => 'v2/widgets']])
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Form</h5>
                </div>
                <div class="ibox-content">
                    @include('frontend.partials.notification')
                    <?php if ($type === "create") : ?>
                        {{ Form::open(array('url' => 'widgets/submit', 'class' => 'widgetForm')) }}
                    <?php elseif ($type === "edit") : ?>
                        {{ Form::model($widget, array('route' => array('widgets.update', $widget->id), 'url' => 'widgets/' . $widget->id . '/edit', 'class' => 'form-horizontal itemForm')) }}
                    <?php endif; ?>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tabPlacementInfo" data-toggle="tab">Info</a></li>
                        <li><a href="#tabRulesetItem" data-toggle="tab">Ruleset &amp; Filter</a></li>
                        <li><a href="#tabAlgorithm" data-toggle="tab">Algorithm</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active pt20 form-horizontal" id="tabPlacementInfo">
                            <div class="form-group {{ $var = $errors->first('name') }} {{ ($var !== '') ? 'has-error' : '' }}">
                                <label for='name' class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-10">
                                    <?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name', 'id' => 'name')); ?>
                                    <span class="help-block">{{ $errors->first('name') }}</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group {{ $var = $errors->first('description') }} {{ ($var !== '') ? 'has-error' : '' }}">
                                <label for='description' class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-10">
                                    <?php echo Form::textarea('description', null, array('class' => 'form-control', 'placeholder' => 'Description', 'id' => 'description', 'rows' => 4)); ?>
                                    <span class="help-block">{{ $errors->first('description') }}</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </div>
                        <div class="tab-pane pt20 form-horizontal" id="tabRulesetItem">
                            <!-- rule item -->
                            <div id="item_rules_container">
                                {{-- <div class="action_buttons pull-right"><a href="javascript:void(0);" class="btn btn-default" onClick="addItemPlacementRuleset();"><i class="fa fa-plus"></i></a></div> --}}
                                <div class="clearfix"></div>
                                <?php if ($type === "create") : ?>
                                    @include(getenv('FRONTEND_SKINS') . $theme . '.panels.widgets.itemruleset')
                                <?php elseif ($type === "edit") : ?>
                                    @if ($number_of_items === 1)
                                    @include(getenv('FRONTEND_SKINS') . $theme . '.panels.widgets.itemruleset')	
                                    @endif
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div class="clearfix hr-line-dashed mt0"></div>
                            <div id="item_filters_container">
                                <div class="clearfix"></div>
                                <?php if ($type === "create") : ?>
                                    @include(getenv('FRONTEND_SKINS') . $theme . '.panels.widgets.itemfilter')	
                                <?php elseif ($type === "edit") : ?>
                                    @if ($number_of_items === 1)
                                    @include(getenv('FRONTEND_SKINS') . $theme . '.panels.widgets.itemfilter')	
                                    @endif
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div class="clearfix hr-line-dashed mt0"></div>

                        </div>
                        <div class="tab-pane pt20 form-horizontal" id="tabAlgorithm">
                            <div class="form-group {{ $var = $errors->first('algo') }} {{ ($var !== '') ? 'has-error' : '' }}">
                                <label for='name' class="col-sm-2 control-label">Algorithm</label>
                                <div class="col-sm-10">
                                    <?php echo Form::select("algo", $algorithm_list, ($type === "edit" && $widget) ? $widget->reco_type : null, array('class' => 'form-control chosen-select-item', 'id' => "algo", 'data-placeholder' => "")); ?>
                                    <span class="help-block">{{ $errors->first('algo') }}</span>
                                </div>
                            </div>
                            <div class="clearfix hr-line-dashed mt0"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <button class="btn btn-white" type="reset">Cancel</button>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

