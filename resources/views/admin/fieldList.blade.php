@extends('adminlte::page') 
@section('title', 'Registros') 
@section('content_header')
<h1>Segmentation Fields n Tables</h1>
@stop 
@section('content') @if (session('status'))
<div class="alert alert-{{ session('status') }}">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> {{ session('msg') }}
</div>
@endif
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!--<div class="box-header">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <form id='frmSearch' name='frmSearch' method="GET" action="">
                        <div class="input-group input-group-sm" style="width: 150px;">

                            <input type="text" name="table_search" id='table_search' class="form-control pull-right" placeholder="Search" value="">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>-->
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table id="tableFieldList" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>ApiName</th>
                            <th>DataType</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $datum)
                        <tr>
                            <td>
                                {{$datum->id}}
                            </td>
                            <td>
                                {{$datum->name}}
                            </td>
                            <td>
                                {{$datum->action}}
                            </td>
                            <td>
                                {{$datum->description}}
                            </td>
                            <td>
                                {{$datum->api_name}}
                            </td>
                            <td>
                                {{$datum->data_type->name}}
                            </td>
                            <td>
                                {{$datum->created_at->format('Y-m-d')}}
                            </td>
                            <td>
                                @if(!is_null($datum->updated_at)) {{$datum->updated_at->format('Y-m-d')}} @endif
                            </td>
                            <td>
                                
                                <a href="{{route('AdminFieldEdit',['tableControl'=>$datum->id])}}">
                                    <button type="button" class="btn btn-warning">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </a>
                                @if ($datum->action=='normal')
                                <a href="{{route('AdminValuesIndex',['tableControl'=>$datum->id])}}">
                                        <button type="button" class="btn btn-info">
                                            <i class="fa fa-bar-chart"></i>
                                        </button>
                                    </a>
                                    @endif
                                    {{ Form::bsConfirmIcon(route('AdminFieldsRemove',['tableControl'=>$datum->id]),'remove')}}
                                
                            </td>
                        </tr>
                        @empty @endforelse

                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
        <div class="box-footer">
            <a href="{{route('AdminFieldsNew')}}">
                <button type=button class="btn btn-info pull-right-container">New Segmentation Field</button>
            </a>
        </div>
    </div>
</div>



@stop 
@section ('css')
<!-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css"/>-->
@stop 
@section('js')
<!--  <script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>-->
<script>
    $(function () {
        $('#tableFieldList').DataTable();
        
                $('#confirm').on('show.bs.modal', function (e) {
                    $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
                    $(this).find('.confirm-description').text($(e.relatedTarget).data('description'));
                });
            
    })
</script>
@stop