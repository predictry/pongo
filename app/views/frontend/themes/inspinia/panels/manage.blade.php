@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(HTML::script('assets/js/script.helper.js'), HTML::script('assets/js/script.panel.items.js'))])
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_with_action')
<div class="wrapper wrapper-content animated fadeInRight manage">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    @include('frontend.partials.notification')
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
                                        <div class="btn-group">
                                            {{ Form::open(array('url' => URL::to( URL::current() . "/" . $o->id . "/delete" ), "class" => "mb0")) }}
                                            {{ Form::hidden("member_id", $o->id) }}
                                            <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle">Action <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                                @if ($edit) <li><a href="{{URL::to( URL::current() . "/" . $o->id . "/edit" ) }}"  data-toggle="tooltip" data-placement="bottom" title="{{Lang::get('panel.edit')}}">{{Lang::get('panel.edit')}}</a></li> @endif
                                                @if ($view) <li><a class="btnViewModal" data-toggle="modal" id="btnViewModal{{ $o->id }}" data-target="#viewModal" data-id="{{ $o->id }}" href=" {{ URL::to( URL::current() . "/" . $o->id . "/view" ) }}" data-toggle="tooltip" data-placement="bottom" title="{{Lang::get('panel.view')}}" >{{Lang::get('panel.view')}}</a></li> @endif
                                                @if ($delete) <li><a type="submit" onclick="return confirm('{{Lang::get('panel.remove.confirm')}}');" href="{{ URL::to( URL::current() . "/" . $o->id . "/delete" )  }}"  data-toggle="tooltip" data-placement="bottom" title="{{Lang::get('panel.remove')}}">{{Lang::get('panel.remove')}}</a></li> @endif
                                                @if ($custom_action)
                                                @include($custom_action_view, array('id' => $o->id, 'site_name' => isset($o->name) ? $o->name: ''))
                                                @endif
                                            </ul>
                                        </div>
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
                    @if ($paginator !== null)
                    {{ $paginator->links('frontend.partials.paginator') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@include('frontend.partials.viewmodal')	

@stop
