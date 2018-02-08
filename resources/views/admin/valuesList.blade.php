@extends('adminlte::page') 
@section('title', 'Registros') 
@section('content_header')
<h1>@if (session('section')){{session('section')}} @endif</h1>
@stop 
@section('content') 
@if (session('status'))
<div class="alert alert-{{ session('status') }}">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> {{ session('msg') }}
</div>
@endif


<div class="row">
        <div class="col-xs-9">
            <div class="box">
 
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="tableFieldList" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Relational Value</th>
                                <th>Display Value</th>
                                
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
                                    {{$datum->val_crm}}
                                </td>
                                <td>
                                    {{$datum->val_normalized}}
                                </td>
                                
                                <td>
                                    <a href="{{route('AdminValuesEdit',['tableControl'=>$tableControlId,'valueId'=>$datum->id])}}">
                                        <button type="button" class="btn btn-warning">
                                            <i class="fa fa-edit"></i>
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
            <!-- -->
            <div class="box-footer">
                <a href="{{route('AdminValuesNew',['tableControl'=>$tableControlId])}}">
                    <button type=button class="btn btn-info pull-right-container">New Value in Table</button>
                </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                  <span class="info-box-icon bg-aqua"><i class="ion ion-ios-people-outline"></i></span>
      
                  <div class="info-box-content">
                    <span class="info-box-text">Likes</span>
                    <span class="info-box-number">{{number($cont)}}</span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
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