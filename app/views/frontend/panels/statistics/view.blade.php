@extends('frontend.layouts.dashboard')
@section('content')
<div class="col-sm-5 col-sm-offset-3 col-md-5 col-md-offset-2 main">
	<h2>{{ $pageTitle or 'Overview' }}</h2>
	<hr/>
	@include('frontend.partials.notification')
	<table class="table table-bordered table-striped">
		@foreach ($full_stats as $key => $val)
		<tr>
			<td class=""><?php echo ucwords(str_replace('_', ' ', $key)); ?></td>
			<td>{{ $val }}</td>
		</tr>
		@endforeach
	</table>
</div>
<div class="col-sm-4 col-md-5 main">
	<h2>{{ $pageTitle2 or 'Default' }}</h2>
	<hr/>
	@include('frontend.partials.notification')
	<table class="table table-bordered table-striped">
		@foreach ($actions_made_by_user as $key => $val)
		<tr>
			<td class=""><?php echo ucwords(str_replace('_', ' ', $key)); ?></td>
			<td>{{ $val }}</td>
		</tr>
		@endforeach
	</table>
</div>
@stop