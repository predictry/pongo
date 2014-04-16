@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	<a class="btn btn-default" href="{{ URL::previous(); }}"><i class="fa fa-reply"></i> Back</a>
	<h2>{{ $pageTitle or 'Form Member' }}</h2>
	<hr/>
	<!-- Nav tabs -->
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
				<div class="col-sm-4">
					<?php echo Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name', 'id' => 'name')); ?>
				</div>
				<span class="help-block">{{ $errors->first('name') }}</span>
			</div>

			<div class="form-group {{ $var = $errors->first('description') }} {{ ($var !== '') ? 'has-error' : '' }}">
				<label for='description' class="col-sm-2 control-label">Description</label>
				<div class="col-sm-4">
					<?php echo Form::textarea('description', null, array('class' => 'form-control', 'placeholder' => 'Description', 'id' => 'description', 'rows' => 4)); ?>
				</div>
				<span class="help-block">{{ $errors->first('description') }}</span>
			</div>
		</div>
		<div class="tab-pane pt20 form-horizontal" id="tabRuleDetail">
			<!-- ruleset -->
			<div class="form-group {{ $var = $errors->first('expiry_type') }} {{ ($var !== '') ? 'has-error' : '' }}">
				<label for='expiry_type' class="col-sm-2 control-label">Expiry Type</label>
				<div class="col-sm-4">
					@if($type === 'edit')
					<?php echo Form::select('expiry_type', $enum_expiry_types, $ruleset->expiry_type, array('class' => 'form-control')); ?>
					@else
					<?php echo Form::select('expiry_type', $enum_expiry_types, "no_expiry", array('class' => 'form-control')); ?>
					@endif
				</div>
				<span class="help-block">{{ $errors->first('expiry_type') }}</span>
			</div>		
			<div class="form-group {{ $var = $errors->first('expiry_value') }} {{ ($var !== '') ? 'has-error' : '' }}">
				<label for='expiry_date_or_value' class="col-sm-2 control-label">Expiry Value</label>
				<div class="col-sm-4">
					@if($type === 'edit')
					@if (!isset($ruleset->expiry_datetime))
					<?php echo Form::text('expiry_value', null, array('class' => 'form-control', 'placeholder' => 'Expiry Value', 'id' => 'expiry_value')); ?>
					@else
					<?php
					$expiry_datetime = new \Carbon\Carbon($ruleset->expiry_datetime);
					echo Form::text('expiry_value', $expiry_datetime->toDateString(), array('class' => 'form-control', 'placeholder' => 'Expiry Value', 'id' => 'expiry_value'));
					?>
					@endif
					@else
					<?php echo Form::text('expiry_value', null, array('class' => 'form-control', 'placeholder' => 'Expiry Value', 'id' => 'expiry_value')); ?>
					@endif
				</div>
				<span class="help-block">{{ $errors->first('expiry_value') }}</span>
			</div>
		</div>


		<div class="tab-pane pt20" id="tabRuleItem">
			<!-- rule item -->
			<div class="row" id="item_rules_container">
				<?php if ($type === "create") : ?>
					@include('frontend.panels.rules.itemrule')	
				<?php endif; ?>
			</div>
		</div>
		<div class="clearfix form-horizontal">
			<div class="form-group">
				<div class="pull-right">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</div>
		</div>
	</div><!-- end of tab-content -->
	{{ Form::close(); }}
</div><!-- end of main -->
@stop