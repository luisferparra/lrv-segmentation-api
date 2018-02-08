<div class="form-group @if($errors->has($name)) has-error @endif">
    {{ Form::label($name, __($label), ['class' => 'control-label']) }}
    @if($errors->any())
    {{ Form::select($name, $options, old($name), array_merge(['class' => 'form-control'], $attributes)) }}
    @else
    {{ Form::select($name, $options, $value, array_merge(['class' => 'form-control'], $attributes)) }}
    @endif
    @foreach($errors->get($name) as $error)
        <span class="help-block">{{ $error }}</span>
    @endforeach
</div>