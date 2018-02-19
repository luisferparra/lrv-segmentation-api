@extends('adminlte::page') 
@section('title', 'Users Management') 
@section('content_header')
<h1>Users Management</h1>
@stop 
@section('content') 


@if (session('status'))

<div class="alert alert-{{ session('status') }}">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> {{ session('msg') }}
</div>
@endif 

{!! Form::open(['route' => 'AdminUsersNewPost']) !!} 


<div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"></h3>

          <!-- <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div> 
        -->
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                    {!! Form::bsInputText('name','Users Name',null,['placeholder'=>'Name']) !!}
                    {!! Form::bsInputText('email','Users Email',null,['placeholder'=>'Email']) !!}
                    {!! Form::bsInputText('password','Password Email',null,['placeholder'=>'Password']) !!}
                    
                    {!! Form::bsDropdown('roles','roles', $roles,null,['multiple'=>true,'id'=>'roles']) !!}
                    
                
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <div class="col-md-6">
              <div class="form-group">
                
              </div>
              <!-- /.form-group -->
              <div class="form-group">
                
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <div class="box-footer">
                {!! Form::bsSubmit('Insert',['class'=>'btn btn-primary']) !!}
            </div>
        </div>
        <!-- /.box-body -->
       
      </div>
      <!-- /.box -->
    </div>

            
        
{!! Form::close() !!} 
@stop
@section('js')
<!-- Select2 -->
<script>
        $(function () {
$('#roles').select2()
        });

</script>
@stop