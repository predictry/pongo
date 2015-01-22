@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(HTML::script('assets/inspinia/js/plugins/chosen/chosen.jquery.js'), HTML::script('assets/js/moment.min.js'), HTML::script('assets/js/bootstrap-datetimepicker.min.js'), HTML::script('assets/js/script.helper.js'), HTML::script('assets/js/script.panel.rules.js')) ])
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Rules' => 'v2/rules']])
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Form</h5>
                </div>
                <div class="ibox-content">
                    @include('frontend.partials.notification')
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tabRuleInfo" data-toggle="tab">Info</a></li>
                        <li><a href="#tabRuleDetail" data-toggle="tab">Details</a></li>
                        <li><a href="#tabRuleItem" data-toggle="tab">Rules</a></li>
                    </ul>
                    <?php if ($type === "create") : ?>
                        {{ Form::open(array('url' => 'rules/submit', 'class' => 'rulesetForm')) }}
                    <?php elseif ($type === "edit") : ?>
                        {{ Form::model($ruleset, array('route' => array('rules.update', $ruleset->id), 'url' => 'rules/' . $ruleset->id . '/edit', 'class' => 'rulesetForm')) }}
                    <?php endif; ?>
                    <div class="tab-content">
                        <div class="tab-pane active pt20 form-horizontal" id="tabRuleInfo">
                            <!-- combination -->
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
                        </div>
                        <div class="tab-pane pt20 form-horizontal" id="tabRuleDetail">
                            <!-- ruleset -->
                            <div class="form-group {{ $var = $errors->first('expiry_type') }} {{ ($var !== '') ? 'has-error' : '' }}">
                                <label for='expiry_type' class="col-sm-2 control-label">Expiry Type</label>
                                <div class="col-sm-10">
                                    @if($type === 'edit')
                                    <?php echo Form::select('expiry_type', $enum_expiry_types, $ruleset->expiry_type, array('class' => 'form-control', 'id' => 'expiry_type')); ?>
                                    @else
                                    <?php echo Form::select('expiry_type', $enum_expiry_types, "no_expiry", array('class' => 'form-control', 'id' => 'expiry_type')); ?>
                                    @endif
                                    <span class="help-block">{{ $errors->first('expiry_type') }}</span>
                                </div>
                            </div>		
                            <div class="clearfix hr-line-dashed mt0"></div>
                            <div class="form-group {{ $var = $errors->first('expiry_value') }} {{ ($var !== '') ? 'has-error' : '' }}">
                                <label for='expiry_date_or_value' class="col-sm-2 control-label">Expiry Value</label>
                                <div class="col-sm-10" id="expiry_value_box">
                                    <?php
                                    if ($type === 'edit') {
                                        if (!isset($ruleset->expiry_datetime)) {
                                            echo Form::text('expiry_value', null, array('class' => 'form-control', 'placeholder' => 'Expiry Value', 'id' => 'expiry_value'));
                                            ?>
                                            <script type="text/javascript">
                                                var expiry_date = '';
                                            </script>
                                            <div class='input-group date hide' id='datetimepicker' data-date-format="YYYY-MM-DD hh:mm:ss A">
                                                <input type='text' class="form-control disabled" name="expiry_value_temp" readonly="" />
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <?php
                                        }
                                        else {
                                            $expiry_datetime = new \Carbon\Carbon($ruleset->expiry_datetime);
                                            echo Form::text('expiry_value_temp', $expiry_datetime->toDateTimeString(), array('class' => 'form-control hide', 'placeholder' => 'Expiry Value', 'id' => 'expiry_value'));
                                            ?>
                                            <script type="text/javascript">
                                                var expiry_date = '<?php echo $expiry_datetime->toDateTimeString(); ?>';
                                            </script>
                                            <div class='input-group date' id='datetimepicker' data-date-format="YYYY-MM-DD hh:mm:ss A">
                                                <input type='text' class="form-control disabled" name="expiry_value" readonly=""/>
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                            <?php
                                        }
                                    }
                                    else {
                                        echo Form::text('expiry_value', null, array('class' => 'form-control', 'placeholder' => 'Expiry Value', 'id' => 'expiry_value'));
                                        ?>
                                        <script type="text/javascript">
                                            var expiry_date = '';
                                        </script>
                                        <div class='input-group date hide' id='datetimepicker' data-date-format="YYYY-MM-DD hh:mm:ss A">
                                            <input type='text' class="form-control disabled" name="expiry_value_temp" readonly="" />
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    <?php } ?>
                                </div>
                                <span class="help-block">{{ $errors->first('expiry_value') }}</span>
                            </div>
                            <?php echo Form::hidden("expiry_value_dt", "", array("id" => "expiry_value_dt")); ?>
                        </div>

                        <div class="tab-pane pt20" id="tabRuleItem">
                            <!-- rule item -->
                            <div class="" id="item_rules_container">
                                <?php if ($type === "create") : ?>
                                    @include(getenv('FRONTEND_SKINS') . $theme . '.panels.rules.itemrule')	
                                <?php elseif ($type === "edit") : ?>
                                    @if ($number_of_items === 1)
                                    @include(getenv('FRONTEND_SKINS') . $theme . '.panels.rules.itemrule')	
                                    @endif
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <a href="javascript:void(0);" class="btn btn-default" onClick="addItemRule();"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-2">
                                <button class="btn btn-white" type="reset">Cancel</button>
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div><!-- end of tab-content -->
                    {{ Form::close(); }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop