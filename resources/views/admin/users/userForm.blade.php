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

{!! Form::open(['route' => ((isset($userData)) ? ['AdminUserEditPost',$userData->id] : 'AdminUsersNewPost')]) !!} 


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
                    {!! Form::bsInputText('name','Users Name',(isset($userData['name'])?$userData['name'] : null),['placeholder'=>'Name']) !!}
                    {!! Form::bsInputText('email','Users Email',(isset($userData['email'])?$userData['email']:null),['placeholder'=>'Email']) !!}
                    {!! Form::bsInputText('password','Password',null,['placeholder'=>'Password']) !!}
                    @if (isset($userData)) 
                    <small class="label label-info">Password is ENCRYPTED and already stored. If it is not going to be updated, please, leave this field empty</small>
                    <input type="hidden" name="editing" id="editing" value="1"/>
                    <input type="hidden" name="id" id="id" value="{{$userData->id}}"/>@endif
                    {!! Form::bsDropdown('roles','Roles', $roles,(isset($userRoles) ? $userRoles:null),array_merge(['multiple'=>true,'id'=>'roles'],$disableAttr)) !!}
                   
                
              </div>
              <!-- /.form-group -->
              
            </div>
            <!-- /.col -->
            <div class="col-md-3">
              <div class="form-group">
                {!! Form::bsCheckBox('active','Active','1',(isset($userData['active'])?$userData['active']:null),$disableAttr) !!}
               
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                @if (isset($userData))
                <ul class="nav nav-pills nav-stacked">
                <li><i class="fa fa-circle-o text-red"></i> <label>Created Date:</label> {{$userData->created_at}}</li>
                <li><i class="fa fa-circle-o text-yellow"></i> <label>Last Updated Date:</label> {{$userData->updated_at}}</li>
                <li><i class="fa fa-circle-o text-blue"></i> <label>Last Loggin Date:</label> {{$userData->last_logged_at}}</li>
                
                </ul>
               
                  
                @endif
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
                {!! Form::bsSubmit((isset($userData)) ? 'Update' : 'Insert',['class'=>'btn btn-primary']) !!}
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