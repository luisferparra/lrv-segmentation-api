<div class="form-group @if($errors->has($name)) has-error @endif">
    {{ Form::label($name, __($label), ['class' => 'control-label']) }}
    {{ Form::checkbox($name, $value, $errors->any() ? old($checked) : $checked, array_merge([], $attributes)) }}
 
    
  
    @foreach($errors->get($name) as $error)
        <span class="help-block">{{ $error }}</span>
    @endforeach
</div>