@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(HTML::script('assets/inspinia/js/plugins/chosen/chosen.jquery.js'), HTML::script('assets/js/moment.min.js'), HTML::script('assets/js/bootstrap-datetimepicker.min.js'), HTML::script('assets/js/script.helper.js'), HTML::script('assets/js/script.panel.widget_filters.js')) ])
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Filters' => 'v2/filters']])

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
                        {{ Form::open(array('url' => 'filters/submit', 'class' => 'form-horizontal filterForm')) }}
                    <?php elseif ($type === "edit") : ?>
                        {{ Form::model($filter, array('route' => array('filters.update', $filter->id), 'url' => 'filters/' . $filter->id . '/edit', 'class' => 'form-horizontal filterForm')) }}
                    <?php endif; ?>

                    <div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
                        <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'filter name', 'id' => 'name')); ?>
                        </div>
                        <span class="help-block">{{$errors->first('name')}}</span>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div id="filter_item_container">
                        <?php if ($type === "create") : ?>
                            @include(getenv('FRONTEND_SKINS') . $theme . '.panels.filters.itemfilter')
                        <?php elseif ($type === "edit") : ?>
                            @if($number_of_items === 1)
                            @include(getenv('FRONTEND_SKINS') . $theme . '.panels.filters.itemfilter')
                            @endif
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <a href="javascript:void(0);" class="btn btn-default" onClick="addItemFilter();"><i class="fa fa-plus"></i></a>
                        </div>
                    </div>
                    <div class="action_buttons"></div>

                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <button class="btn btn-white" type="reset">Cancel</button>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                    {{ Form::close(); }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop