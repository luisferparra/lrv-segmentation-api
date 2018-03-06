@extends('adminlte::page') 
@section('title', 'Data Management') 
@section('content_header')
<h1>Data Management</h1>
@stop 
@section('content') @if (session('status'))
<div class="alert alert-{{ session('status') }}">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> {{ session('msg') }}
</div>
@endif {!! Form::open(['route' =>  (!isset($valId)) ? ['AdminValuesNewPost', $tableControlId]: ['AdminValuesEditPost',$tableControlId,$valId]]) !!}

<div class="row">
    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">User </h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <div class="box-body">
                {!! Form::bsInputText('val_crm','Value at CRM',(!isset($valId)) ? null : $data->val_crm,['placeholder'=>'Value at CRM']) !!}
                {!! Form::bsInputText('val_normalized','Front Value',(!isset($valId)) ? null : $data->val_normalized,['placeholder'=>'Front Value']) !!}


            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                {!! Form::bsSubmit((!isset($valId)) ? 'Insert' : 'Update',['class'=>'btn btn-primary']) !!}
            </div>
        </div>
    </div>

</div>
{!! Form::close() !!} 
@stop