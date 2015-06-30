@extends('admin.layouts.dashboard')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2><?php echo ucfirst($moduleName) . "(s)"; ?></h2>
    </div>
</div>  
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
                                @if (count($items) > 0)
                                @foreach($items as $o)
                                @if (isset($o))
                                <tr>
                                    <td>{{ $i }}</td>
                                    @foreach($table_header as $key => $th)
                                    <?php
                                    if (isset($o->$key))
                                        echo "<td>" . $o->$key . "</td>";
                                    else
                                        echo "<td>-</td>";
                                    ?>
                                    @endforeach
                                    <td class="pull-right">
                                        <a data-toggle="modal" href=" {{ URL::to( URL::current() . "/" . $o->id . "/view" ) }}" class="btn btn-default btn-sm btnViewModal tt"  data-toggle="tooltip" data-placement="bottom" title="{{Lang::get('panel.view')}}" ><i class="fa fa-search"></i></a> 
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
