@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	<!-- Nav tabs -->
	<?php if ($type === "create") : ?>
		{{ Form::open(array('url' => 'placements/submit', 'class' => 'placementForm')) }}
	<?php elseif ($type === "edit") : ?>
		{{ Form::model($placement, array('route' => array('placements.update', $placement->id), 'url' => 'placements/' . $placement->id . '/edit', 'class' => 'form-horizontal itemForm')) }}
	<?php endif; ?>
	<a class="btn btn-default" href="{{ URL::previous(); }}"><i class="fa fa-reply"></i> Back</a>
	<h2>{{ $pageTitle or '' }}</h2>
	<hr/>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tabPlacementInfo" data-toggle="tab">Info</a></li>
		<li><a href="#tabRulesetItem" data-toggle="tab">Rulesets</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active pt20 form-horizontal" id="tabPlacementInfo">
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
		<div class="tab-pane pt20 form-horizontal" id="tabRulesetItem">
			<!-- rule item -->
			<div id="item_rules_container">
				{{-- <div class="action_buttons pull-right">
					<a href="javascript:void(0);" class="btn btn-default" onClick="addItemPlacementRuleset();"><i class="fa fa-plus"></i></a>
				</div> --}}
				<div class="clearfix"></div>
				<?php if ($type === "create") : ?>
					@include('frontend.panels.placements.itemruleset')	
				<?php elseif ($type === "edit") : ?>
					<?php
					if ($number_of_items === 1)
					{
						?>
						@include('frontend.panels.placements.itemruleset')	
						<?php
					}
				endif;
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="pull-right">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
	{{ Form::close() }}
</div>
@stop

