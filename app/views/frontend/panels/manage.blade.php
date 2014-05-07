@extends('frontend.layouts.dashboard')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	@include('frontend.partials.notification')
	<div class="row">
		@if ($isManage)
		<h1 class="col-sm-6 pull-left"><?php echo Lang::get("panel.manage"); ?> <?php echo ucfirst($moduleName) . "(s)"; ?></h1>
		@else
		<h1 class="col-sm-9 pull-left">{{ $pageTitle or 'Default' }}</h1>
		@endif
		@if ($create)
		<div class="col-sm-6 action_buttons text-right">
			<a href="{{ URL::to( URL::current() . "/create" ) }}" class="btn btn-primary btn"><i class="fa fa-plus"></i> <?php echo Lang::get("panel.add.new"); ?> {{ $moduleName }}</a>
		</div>
		@endif
		<div class="clearfix"></div>
		<hr class="line"/>

		@if($selector)
		@include($selector_view, $selector_vars)
		@endif
	</div>
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
                <tr>
					<th>#</th>
					@foreach($table_header as $key => $th)
					<th>{{ $th }}</th>
					@endforeach
					@if ($edit || $delete || $view || $custom_action)
					<th></th>
					@endif
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
					<?php
					if ($key === "active")
					{
						$val = ($o->$key) ? Lang::get("panel.yes") : Lang::get("panel.no");
						echo "<td>" . $val . "</td>";
					}
					else
					{
						if (isset($o->$key))
							echo "<td>" . $o->$key . "</td>";
						else
							echo "<td>-</td>";
					}
					?>
					@endforeach
					@if ($edit || $delete || $view || $custom_action)
					<td class="pull-right">
						{{ Form::open(array('url' => URL::to( URL::current() . "/" . $o->id . "/delete" ), "class" => "mb0")) }}
						{{ Form::hidden("member_id", $o->id) }}
						@if ($edit) <a class="btn btn-default btn-sm tt" href=" {{ URL::to( URL::current() . "/" . $o->id . "/edit" ) }}"  data-toggle="tooltip" data-placement="bottom" title="{{Lang::get('panel.edit')}}"><i class="fa fa-edit"></i></a> @endif
						@if ($delete) <button type="submit" onclick="return confirm('{{Lang::get('panel.remove.confirm')}}');" class="btn btn-default btn-sm tt" href="{{ URL::to( URL::current() . "/" . $o->id . "/delete" )  }}"  data-toggle="tooltip" data-placement="bottom" title="{{Lang::get('panel.remove')}}"><i class="fa fa-trash-o"></i></button> @endif
						@if ($view) <a data-toggle="modal" id="btnViewModal{{ $o->id }}" data-target="#viewModal" data-id="{{ $o->id }}" href=" {{ URL::to( URL::current() . "/" . $o->id . "/view" ) }}" class="btn btn-default btn-sm btnViewModal tt"  data-toggle="tooltip" data-placement="bottom" title="{{Lang::get('panel.view')}}" ><i class="fa fa-search"></i></a> @endif
						@if ($custom_action)
						@include($custom_action_view, array('id' => $o->id))
						@endif
						{{ Form::close() }}
					</td>
					@endif
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
	@include('frontend.partials.viewmodal')	
	@if ($paginator !== null)
	{{ $paginator->links('frontend.partials.paginator') }}
	@endif
	@stop
