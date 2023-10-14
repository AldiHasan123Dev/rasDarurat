<div class="row">
<x-input :value="$lokasi->nama??old('nama')" :col="6" :label="'Nama Lokasi'" :type="'text'" :name="'nama'" :required="true"></x-input>
<div class="col-6 mb-2">
    <label for="publis_rate">Publis Rate</label>
    <input type="number" class="form-control" id="publis_rate-{{ $lokasi->id ?? 0 }}" name="publis_rate" value="{{ $lokasi->publis_rate ?? 0 }}" onclick="this.select()" onkeyup="hitung({{ $lokasi->id ?? 0 }})">
</div>
<div class="col-6 mb-2">
    <label for="diskon">Diskon</label>
    <input type="number" class="form-control" id="diskon-{{ $lokasi->id ?? 0 }}" name="diskon" value="{{ $lokasi->diskon ?? 0 }}" onclick="this.select()" onkeyup="hitung({{ $lokasi->id ?? 0 }})">
</div>
{{-- <x-input :value="$lokasi->publis_rate??0" :col="6" :label="'Publis Rate'" :id="'rate_c'" :type="'number'" :name="'publis_rate'" :required="true"></x-input>
<x-input :value="$lokasi->diskon??0" :col="6" :label="'Diskon'" :id="'discount_c'" :type="'number'" :name="'diskon'" :required="true"></x-input> --}}
<x-input :value="$lokasi->harga??0" :readonly="true" :col="6" :id="'harga-'.($lokasi->id??0)" :label="'Harga Net'" :type="'number'" :name="'harga'" :required="true"></x-input>
{{-- <x-input :value="$lokasi->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input> --}}
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($lokasi)?'Tambah':'Update' }} Data</button>
</div>
</div>
