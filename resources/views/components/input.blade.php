<div class="col-{{ $col ?? '12'}} mb-3">
    <label>{{ $label ?? '' }}</label>
    @if ($type=='text'|| $type=='password'||$type=='email'||$type=='number' || $type=='file' || $type=='date')
        <input type="{{ $type }}" name="{{ $name??'' }}" value="{{ $value ?? old($name) }}" id="{{ $id ?? $name }}" class="form-control" {{ !empty($attr)?implode(' ', $attr) : '' }}>
    @elseif($type=='select')
    @php
        $value = $value ?? old($name);
    @endphp
        <select name="{{ $name }}" id="{{ $id ?? $name }}" class="form-control" {{ !empty($attr)?implode(' ', $attr) : '' }}>
            <option disabled selected>Pilih {{ $label }}</option>
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
