@extends('adminlte::page') 
@section('title', 'Users Management') 
@section('content_header')
<h1>Users Management</h1>
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
                        <table id="tableUsersList" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $datum)
                                    <tr>
                                        <td>{{$datum->id}}</td>
                                        <td>{{$datum->name}}</td>
                                        <td>{{$datum->email}}</td>
                                        <td> @forelse ($datum->roles as $role) - {{$role->name}}<br /> @empty @endforelse
                                        </td>
                                        <td>
                                            {{$datum->created_at->format('Y-m-d')}}
                                        </td>
                                        <td>
                                            @if(!is_null($datum->updated_at)) {{$datum->updated_at->format('Y-m-d')}} @endif
                                        </td>
                                        <td>
                                
                                                <a href="">
                                                    <button type="button" class="btn btn-warning">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </a>
                                                <a href="">
                                                        <button type="button" class="btn btn-info">
                                                            <i class="fa fa-bar-chart"></i>
                                                        </button>
                                                    </a>
                                                <a  class="confirm" data-href="" data-toggle="modal" data-target="#confirm" data-description="remove">
                                                    <button type="button" class="btn btn-danger">
                                                        <i class="fa fa-remove"></i>
                                                    </button>
                                                </a>
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
                <a href="{{route('AdminUsersNew')}}">
                    <button type=button class="btn btn-info pull-right-container">New User</button>
                </a>
            </div>
        </div>
    </div>
    
    <div class="modal fade modal-warning" id="confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Confirm Action.</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to <span class="confirm-description"></span> this item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">NO</button>
                    <a class="btn btn-warning btn-ok">yes</a>
                </div>
            </div>
        </div>
    </div>


@stop 
@section ('css')
    <!-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css"/>-->
    
@stop 
@section('js')
    <script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script>
        $(function () {
        $('#tableUsersList').DataTable();
        
                $('#confirm').on('show.bs.modal', function (e) {
                    $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
                    $(this).find('.confirm-description').text($(e.relatedTarget).data('description'));
                });
            
    })
    </script>
    
@stop