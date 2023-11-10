@props(['label','col','name','value','type','attr','options','rows','cols','required','disabled','id','readonly','autocomplete'])
@php
    $required = $required ?? false;
    $disabled = $disabled ?? false;
    $readonly = $readonly ?? false;
    $autocomplete = $autocomplete ?? false;
@endphp
<div class="col-{{ $col ?? '12'}} mb-3 autocomplete">
    <label>{{ $label ?? '' }}</label><br>
    @if ($type=='text'|| $type=='password'||$type=='email'||$type=='number' || $type=='file' || $type=='date' || $type=='rupiah')
        <input type="{{ $type=='rupiah'?'text':$type }}" name="{{ $name??'' }}" value="{{ $value ?? old($name) }}" id="{{ $id ?? $name }}" class="form-control w-100 {{ $type=='rupiah'?'rupiah':'' }}" step="any" {{ !empty($attr)?implode(' ', $attr) : '' }} {{ $required?'required':'' }} {{ $readonly?'readonly':'' }} {{ $disabled?'disabled':'' }}>
    @elseif($type=='select')
    @php
        $value = $value ?? old($name);
    @endphp
        <select name="{{ $name }}" id="{{ $id ?? $name }}" class="form-control form-select" {{ $readonly?'readonly':'' }} {{ $disabled?'disabled':'' }} {{ !empty($attr)?implode(' ', $attr) : '' }}>
            <option disabled selected></option>
            @forelse ($options as $idx => $option)
                <option value="{{ $idx }}" {{ $value==$idx ? 'selected' : '' }}>{{ $option }}</option>
            @empty
            <option value=""></option>
            @endforelse
        </select>
    @elseif($type=='textarea')
        <textarea name="{{ $name }}" cols="{{ $cols ?? '30' }}" rows="{{ $rows ?? '3' }}" id="{{ $id ?? $name }}" class="form-control" {{ !empty($attr)?implode(' ', $attr) : '' }}>{{ $value ?? old($name) }}</textarea>
    @endif
    <div class="invalid-feedback"></div>
</div>
