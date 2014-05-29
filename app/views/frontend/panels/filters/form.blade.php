@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	<a class="btn btn-default" href="{{ URL::previous(); }}"><i class="fa fa-reply"></i> Back</a>
	<h2>{{ $pageTitle or 'Form Member' }}</h2>
	<hr/>
	<?php if ($type === "create") : ?>
		{{ Form::open(array('url' => 'filters/submit', 'class' => 'form-horizontal filterForm')) }}
	<?php elseif ($type === "edit") : ?>
		{{ Form::model($filter, array('route' => array('filters.update', $filter->id), 'url' => 'filters/' . $filter->id . '/edit', 'class' => 'form-horizontal rulesetForm')) }}
	<?php endif; ?>

	<div class="form-group {{$var = $errors->first('name')}} {{ ($var !== '') ? 'has-error' : ''}}">
		<div class="row">
			<label for="name" class="col-sm-2 control-label">Name</label>
			<div class="col-sm-4">
				<?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'filter name', 'id' => 'name')); ?>
			</div>
			<span class="help-block">{{$errors->first('name')}}</span>
		</div>
	</div>

	<div id="filter_item_container">
		<?php if ($type === "create") : ?>
			@include('frontend.panels.filters.itemfilter')
		<?php elseif ($type === "edit") : ?>
			@if($number_of_items === 1)
			@include('frontend.panels.filters.itemfilter')
			@endif
		<?php endif; ?>
	</div>
	<div class="form-group">
		<div class="row">
			<label for='action_id[]' class="col-sm-2 control-label"></label>
			<div class="col-sm-4">
				<a href="javascript:void(0);" class="btn btn-default" onClick="addItemFilter();"><i class="fa fa-plus"></i></a>
			</div>
		</div>
	</div>
	<div class="action_buttons">
	</div>
	<div class="form-group pull-right">
		<div class="col-sm-offset-2 col-sm-4">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
	{{ Form::close(); }}
</div><!-- end of main -->
@stop