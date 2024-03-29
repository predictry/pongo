@extends('frontend.layouts.blankdashboard')
@section('content')
<div class="col-xs-offset-2 col-xs-8 main">
	@include('frontend.partials.notification')
	@include('frontend.panels.widgets.wizardsteps')

	<div class="row setup-content form-horizontal" id="step-1">
		<div class="col-xs-12">
			<div class="wizardWidget">
				@include('frontend.panels.widgets.wizardformwidget')
			</div>
			<div class="clearfix"></div>
			<button type="submit" class="btn btn-primary pull-right" id="btnWizardWidgetInfo">Next</button>
		</div>
	</div>
	<div class="row setup-content" id="step-2">
		<div class="col-xs-12">
			{{--<div class="action_buttons pull-right">
				<a href="javascript:void(0);" class="btn btn-default" onClick="addItemPlacementRuleset();"><i class="fa fa-plus"></i></a>
				<a data-toggle="modal" id="btnViewModal" data-target="#viewModal" href=" {{ URL::to("/rules/formCreate") }}" class="btn btn-default btnViewModal"  data-toggle="tooltip" data-placement="bottom" title="View" >Add New Ruleset</a>
			</div>--}}
			<div class="clearfix"></div>
			@include('frontend.panels.widgets.wizardformruleset')
			<div class="clearfix"></div>
			<button type="submit" class="btn btn-primary pull-right" id="btnWizardComplete">Submit &amp; Next</button>
		</div>
	</div>
	<div class="row setup-content" id="step-3">
		<div class="col-xs-12">
			<div id="wizardEmbedJS"></div>
		</div>
	</div>
</div>
@include('frontend.partials.viewmodal')	
@stop

