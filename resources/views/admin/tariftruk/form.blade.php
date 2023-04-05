@php
    $agens = \App\Models\Truk::pluck('nama','id');
    $lokasi = \App\Models\Lokasi::pluck('nama','id');
@endphp
<div class="row">
<x-input :value="$taritruk->truk_id??old('truk_id')" :col="12" :label="'Truk'" :type="'select'" :options="$agens" :name="'truk_id'" :required="true"></x-input>
<x-input :value="$taritruk->tanggal??old('tanggal')" :col="6" :label="'Tanggal'" :type="'date'" :name="'tanggal'" :required="true"></x-input>
<x-input :value="$taritruk->tipe??old('tipe')" :col="6" :label="'Shipment'" :type="'select'" :options="$shipments" :name="'tipe'" :required="true"></x-input>
<x-input :value="$taritruk->dari??old('dari')" :col="6" :label="'Dari'" :type="'select'" :options="$lokasi" :name="'dari'" :required="true"></x-input>
<x-input :value="$taritruk->tujuan??old('tujuan')" :col="6" :label="'Tujuan'" :type="'select'" :options="$lokasi" :name="'tujuan'" :required="true"></x-input>
<x-input :value="$taritruk->tarif??old('tarif')" :col="6" :label="'Tarif'" :type="'number'" :name="'tarif'" :required="true"></x-input>
<x-input :value="$taritruk->kubikasi??old('kubikasi')" :col="6" :label="'Kubikasi'" :type="'number'" :name="'kubikasi'" :required="true"></x-input>
<x-input :value="$taritruk->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'text'" :name="'keterangan'" :required="true"></x-input>
<div class="col-6 mb-2">
    <label for="is_active_1"><input type="radio" name="is_active" value="1" id="is_active_1" checked> Active</label>
    <label for="is_active_0"><input type="radio" name="is_active" value="0" id="is_active_0">Non Active</label>
</div>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($taritruk)?'Tambah':'Update' }} Data</button>
</div>
</div>
