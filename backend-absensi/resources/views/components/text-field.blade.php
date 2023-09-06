<div class="form-group {{$errors->has($name) ? 'has-error has-feedback' : '' }}">
    @if (in_array($type, ['text', 'password', 'date', 'email','number']))
    <label for="{{$name}}" class="placeholder"><b>{{$label}}</b></label>
    @endif

    <input id="{{$name}}" value="{{isset($value) ? $value : ''}}" name="{{$name}}" wire:model="{{$name}}"
        type="{{$type ?? 'text'}}" class="form-control" {{isset($readonly) ? 'readonly' : ''}}>
    <small id="helpId" class="text-danger">{{ $errors->has($name) ? $errors->first($name) : '' }}</small>
</div>