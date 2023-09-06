<div class="form-group {{$errors->has($name) ? 'has-error has-feedback' : '' }}">
  <label for="{{$name}}" class="placeholder"><b>{{$label}}</b></label>

  <select name="{{$name}}" value="{{isset($value) ? $value : ''}}" wire:model="{{$name}}" @isset($handleChange)
    wire:change="{{$handleChange}}($event.target.value)" @endisset class="form-control">
    {{$slot}}
  </select>
  <small id="helpId" class="text-danger">{{ $errors->has($name) ? $errors->first($name) : '' }}</small>
</div>