<div class="form-group @if($errors->has($name)) has-error @endif">
    {{ Form::label($name, __($label), ['class' => 'control-label']) }}
    {{ Form::text($name, $errors->any() ? old($name) : $value, array_merge(['class' => 'form-control'], $attributes)) }}
    @foreach($errors->get($name) as $error)
        <span class="help-block">{{ $error }}</span>
    @endforeach
</div>