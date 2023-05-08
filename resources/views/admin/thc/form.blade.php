@php
    $lokasi = App\Models\Lokasi::pluck('nama','id');
@endphp

<div class="row">
<x-input :options="$lokasi" :value="$thc->lokasi_id??old('lokasi_id')" :col="12" :label="'Tujuan'" :type="'select'" :name="'lokasi_id'" :required="true"></x-input>
<x-input :value="$thc->cont_20??old('cont_20')" :col="12" :label="'Cont 20'" :type="'number'" :name="'cont_20'" :required="true"></x-input>
<x-input :value="$thc->cont_40??old('cont_40')" :col="12" :label="'Cont 40'" :type="'number'" :name="'cont_40'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($thc)?'Tambah':'Update' }} Data</button>
</div>
</div>
