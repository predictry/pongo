@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard')
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Items' => 'v2/items']])

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
                        {{ Form::open(array('url' => 'items/submit', 'class' => 'form-horizontal loginForm')) }}
                    <?php elseif ($type === "edit") : ?>
                        {{ Form::model($item, array('route' => array('items.update', $item->id), 'url' => 'items/' . $item->id . '/edit', 'class' => 'form-horizontal itemForm')) }}
                    <?php endif; ?>
                    <div class="form-group {{ $var = $errors->first('name') }} {{ ($var !== '') ? 'has-error' : '' }}">
                        <label for='name' class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Description', 'id' => 'name')); ?>
                            <span class="help-block">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group {{ $var = $errors->first('item_url') }} {{ ($var !== '') ? 'has-error' : '' }}">
                        <label for='item_url' class="col-sm-2 control-label">Item URL</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('item_url', null, array('class' => 'form-control', 'placeholder' => 'Item URL', 'id' => 'item_url')); ?>
                            <span class="help-block">{{ $errors->first('item_url') }}</span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group {{ $var = $errors->first('img_url') }} {{ ($var !== '') ? 'has-error' : '' }}">
                        <label for='img_url' class="col-sm-2 control-label">Image URL</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('img_url', null, array('class' => 'form-control', 'placeholder' => 'Image URL', 'id' => 'img_url')); ?>
                        </div>
                        <span class="help-block">{{ $errors->first('img_url') }}</span>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <?php echo Form::hidden("active", true); ?>
                    <div class="form-group">
                        <label for='img_url' class="col-sm-2 control-label"></label>
                        <div class="col-sm-4">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default btn-sm <?php echo ($activated === true) ? 'active' : ''; ?>">
                                    <input type="radio" name="active" value="true" > Activated
                                </label>
                                <label class="btn btn-default btn-sm <?php echo ($activated === false) ? 'active' : ''; ?>">
                                    <input type="radio" name="active" value="false"> Deactivated
                                </label>
                            </div>
                        </div>
                        <span class="help-block">{{ $errors->first('img_url') }}</span>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop