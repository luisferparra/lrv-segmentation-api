<a class="confirm" data-href="{{ $route }}" data-toggle="modal" data-target="#confirm" data-description="{{ $description }}">
    {!! Form::button(__($value), array_merge(['class' => 'btn btn-default'], $attributes)) !!}
</a>