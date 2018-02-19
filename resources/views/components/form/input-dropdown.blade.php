<div class="form-group @if($errors->has($name)) has-error @endif">
        {{ Form::label((isset($attributes['multiple']) && ($attributes['multiple']==true || $attributes['multiple'] == "multiple")) ? $name."[]" : $name, __($label), ['class' => 'control-label']) }}
        @if($errors->any())
        {{ Form::select((isset($attributes['multiple']) && ($attributes['multiple']==true || $attributes['multiple'] == "multiple")) ? $name."[]" : $name, $options, old($name), array_merge(['class' => 'form-control'], $attributes)) }}
        @else
        {{ Form::select((isset($attributes['multiple']) && ($attributes['multiple']==true || $attributes['multiple'] == "multiple")) ? $name."[]" : $name, $options, $value, array_merge(['class' => 'form-control'], $attributes)) }}
        @endif
        @foreach($errors->get($name) as $error)
            <span class="help-block">{{ $error }}</span>
        @endforeach
    </div>