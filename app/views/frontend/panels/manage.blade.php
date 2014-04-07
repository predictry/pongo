@extends('frontend.layouts.dashboard')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	<div class="row">
		<h1 class="col-sm-6 pull-left">Manage <?php echo ucfirst($moduleName) . "(s)"; ?></h1>
		<div class="col-sm-6 action_buttons text-right">
			<a href="{{ URL::to( URL::current() . "/create" ) }}" class="btn btn-primary btn-sm"><i class="fa fa-user"></i> Add New {{ $moduleName }}</a>
		</div>
		<hr class="line"/>
	</div>
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
                <tr>
					<th>#</th>
					@foreach($table_header as $key => $th)
					<th>{{ $th }}</th>
					@endforeach
					<th></th>
                </tr>
			</thead>
			<tbody>
				<?php $i = (isset($page) && $page > 1) ? ( ($page - 1) * $paginator->getPerPage() ) + 1 : 1; ?>
				@if (count($paginator) > 0)
				@foreach($paginator as $o)
				@if (isset($o))
				<tr>
					<td>{{ $i }}</td>
					@foreach($table_header as $key => $th)
					<?php echo "<td>" . $o->$key . "</td>"; ?>
					@endforeach
					<td class="pull-right">
						{{ Form::open(array('url' => 'members/' . $o->id . '/delete', "class" => "mb0")) }}
						{{ Form::hidden("member_id", $o->id) }}
						<a class="btn btn-default btn-sm" href="{{ URL::to( URL::current() . "/" . $o->id . "/edit" ) }}"><i class="fa fa-edit"></i></a>
						<button type="submit" onclick="return confirm('Are you sure want to remove this?');" class="btn btn-default btn-sm" href="{{ URL::to( URL::current() . "/" . $o->id . "/delete" )  }}"><i class="fa fa-trash-o"></i></button>
						{{ Form::close() }}
					</td>
				</tr>
				<?php $i++; ?>
				@endif
				@endforeach
				@else
				<tr>
					<td class="text-center" colspan="<?php echo count($table_header) + 2; ?>">{{ $str_message }}</td>
				</tr>
				@endif
			</tbody>
		</table>
	</div>
	@if ($paginator !== null)
	{{ $paginator->links('frontend.partials.paginator') }}
	@endif
	@stop