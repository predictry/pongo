@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard')
@section('content')
@include('frontend.partials.notification')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_with_action')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
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
                                    if ($key === "active") {
                                        $val = ($o->$key) ? Lang::get("panel.yes") : Lang::get("panel.no");
                                        echo "<td>" . $val . "</td>";
                                    }
                                    else {
                                        if (isset($o->$key))
                                            echo "<td>" . $o->$key . "</td>";
                                        else
                                            echo "<td>-</td>";
                                    }
                                    ?>
                                    @endforeach
                                    @if ($edit || $delete || $view || $custom_action)
                                    <td class="">
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
                </div>
            </div>
        </div>
    </div>
</div>
@include('frontend.partials.viewmodal')	
@if ($paginator !== null)
{{ $paginator->links('frontend.partials.paginator') }}
@endif
@stop
