@extends('adminlte::page') 
@section('title', 'Data Management') 
@section('content_header')
<h1>Data Management</h1>
@stop 
@section('content') @if (session('status'))
<div class="alert alert-{{ session('status') }}">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> {{ session('msg') }}
</div>
@endif {!! Form::open(['route' =>  'AdminValuesNewPost'])
!!}

<div class="row">
    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">User </h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <div class="box-body">
                {!! Form::bsInputText('val_crm','Value at CRM',null,['placeholder'=>'Value at CRM']) !!}
                {!! Form::bsInputText('val_normalized','Front Value',null,['placeholder'=>'Front Value']) !!}


            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                {!! Form::bsSubmit(route('AdminValuesNewPost',[]),'Insert',['class'=>'btn btn-primary']) !!}
            </div>
        </div>
    </div>

</div>
{!! Form::close() !!} 
@stop