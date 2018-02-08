@extends('adminlte::page') 
@section('title', 'Registros') 
@section('content_header')
<h1>Segmentation Fields and Tables</h1>
@stop 
@section('content') 


@if (session('status'))

<div class="alert alert-{{ session('status') }}">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> {{ session('msg') }}
</div>
@endif 


{!! Form::open(['route' => (isset($tableControl)) ?['AdminFieldUpdatePost',$tableControl->id] : 'AdminFieldNewPost']) !!} 

@php 
$data_type_id = ''; $action_id=''; 
if ($errors->any()):
$data_type_id = old('data_type_id'); $action_id = old('action'); else: if (isset($tableControl)): $data_type_id = $tableControl->data_type_id;
 endif; if (isset($tableControl)): $action_id = $tableControl->action; endif; endif; @endphp

<div class="row">
    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">User </h3>
            </div>
           
            <!-- /.box-header -->
            <!-- form start -->
            <div class="box-body">
                <div class="form-group @if($errors->has('name')) has-error @endif">
                    <label for="name">Name (of the table)</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name of the table" value="@if ($errors->any()){{old('name')}}@elseif (isset($tableControl)){{$tableControl->name}}@endif" @if (isset($tableControl)) readonly @endif>                    @if($errors->has('name')) <span class="help-block">{{$errors->get('name')[0]}}</span>@endif
                </div>
                <!--                        <div class="form-group has-error">
                            <label class="control-label" for="inputError">- <i class="fa fa-times-circle-o"></i> -Input with
                                error</label>
                            <input type="text" class="form-control" id="inputError" placeholder="Enter ...">
                            <span class="help-block">Help block with error</span>
                        </div>-->
                <div class="form-group @if($errors->has('action')) has-error @endif">
                    <label for="action">Internal Action Type</label><small><br /><strong>bit:</strong> does not have associated table of values.<br /><strong>ignore:</strong> cannot be selected for segmentation</small>
                    <select id="action" name="action" class="form-control">
                              <option value='normal' @if (!is_null($action_id) && isset($tableControl) && $action_id == 'normal') selected @endif>normal</option>
                            <option value='bit' @if (!is_null($action_id) && $action_id == 'bit') selected @endif>bit</option>
                            <option value='ignored' @if (!is_null($action_id) && $action_id == 'ignored') selected @endif>ignored</option>
</select> @if($errors->has('action')) <span class="help-block">{{$errors->get('action')[0]}}</span>@endif
                </div>
                <div class="form-group  @if($errors->has('description')) has-error @endif">
                    <label for="description">Description</label>
                    <input type="string" class="form-control" id="description" name="description" placeholder="Description" value="@if ($errors->any()){{old('description')}}@elseif (isset($tableControl)){{$tableControl->description}}@endif">                    @if($errors->has('description')) <span class="help-block">{{$errors->get('description')[0]}}</span>@endif
                </div>
                <div class="form-group  @if($errors->has('api_name')) has-error @endif">
                    <label for="api_name">Api Name</label><small>* Without Spaces. System will normalize input data and store it in lower case</small>
                    <input type="string" class="form-control" id="api_name" name="api_name" placeholder="Api Name" value="@if ($errors->any()){{old('api_name')}}@elseif (isset($tableControl)){{$tableControl->api_name}}@endif">                    @if($errors->has('api_name')) <span class="help-block">{{$errors->get('api_name')[0]}}</span>@endif

                </div>
                @php $data_type_id = (empty($data_type_id)) ? '' : $data_type_id; @endphp
                <div class="form-group @if($errors->has('data_type_id')) has-error @endif">
                    <label>Data Type</label>
                    <select class="form-control" name='data_type_id' id='data_type_id'>
                                <option></option>
                                @foreach ($data_types as $data_type)
                                <option value='{{$data_type->id}}' @if (!is_null($data_type_id) && isset($tableControl) && $data_type_id == $data_type->id) selected @endif>{{$data_type->name}}</option>
                                @endforeach
                            </select> @if($errors->has('data_type_id')) <span class="help-block">{{$errors->get('data_type_id')[0]}}</span>@endif
                </div>

            </div>
            <!-- /.box-body -->
            {{ csrf_field() }}
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>


    <div class="col-md-5">
        @if (!empty($segmentationCount))

        <!-- Info Boxes Style 2 -->
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="ion ion-ios-people-outline"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{$segmentationCount['first']['label']}}</span>
                <span class="info-box-number">{{ number($segmentationCount['first']['num']) }}</span>

            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="ion ion-ios-list-outline"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{$segmentationCount['second']['label'] }}</span>
                <span class="info-box-number">{{ number($segmentationCount['second']['num']) }}</span>

            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        @elseif (!empty($tableCount))
        <!-- Info Boxes Style 2 -->
        <div class="info-box bg-red">
            <span class="info-box-icon"><i class="ion ion-ios-pulse-strong"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ $tableCount['first']['label'] }}</span>
                <span class="info-box-number">{{ number($tableCount['first']['num']) }}</span>

            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="ion ion-ios-pie-outline"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ $tableCount['second']['label'] }}</span>
                <span class="info-box-number">{{ $tableCount['second']['num'] }}</span>

            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        @endif
    </div>

</div>


{!! Form::close() !!} 
@stop