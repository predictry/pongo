@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	<?php if ($type === "create") : ?>
		{{ Form::open(array('url' => 'panel/submitFunel', 'class' => 'form-horizontal loginForm')) }}
	<?php elseif ($type === "edit") : ?>
		{{ Form::model($action, array('route' => array('actions.update', $action->id), 'url' => 'actions/' . $action->id . '/edit', 'class' => 'form-horizontal itemForm')) }}
	<?php endif; ?>
	<a class="btn btn-default" href="{{ URL::previous(); }}"><i class="fa fa-reply"></i> Back</a>
	<h2>{{ $pageTitle or 'Form Member' }}</h2>
	<hr/>
	<div class="form-group {{ $var = $errors->first('name') }} {{ ($var !== '') ? 'has-error' : '' }}">
		<label for='name' class="col-sm-2 control-label">Name</label>
		<div class="col-sm-4">
			<?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name', 'id' => 'name')); ?>
		</div>
		<span class="help-block">{{ $errors->first('name') }}</span>
	</div>
	<div id="item_funel_action_container">
		@include('frontend.panels.funels.itemaction')
	</div>
	<div class="form-group">
		<label for='action_id[]' class="col-sm-2 control-label"></label>
		<div class="col-sm-4">
			<a href="javascript:void(0);" class="btn btn-default" onClick="addItemFunel();"><i class="fa fa-plus"></i></a>
		</div>
	</div>
	<div class="action_buttons">
	</div>
	<div class="form-group pull-right">
		<div class="col-sm-offset-2 col-sm-4">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
	{{ Form::close() }}
	@stop
