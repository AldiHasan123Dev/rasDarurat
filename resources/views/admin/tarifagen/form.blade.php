@php
    $agens = \App\Models\Agen::pluck('nama','id');
    $lokasi = \App\Models\Lokasi::pluck('nama','id');
    $customers = \App\Models\Customer::pluck('nama','id');
@endphp
<div class="row">
<x-input :value="$tarifagen->agen_id??old('agen_id')" :col="12" :label="'Agen'" :type="'select'" :options="$agens" :name="'agen_id'" :required="true"></x-input>
<x-input :value="$tarifagen->pembayar_id??old('pembayar_id')" :col="6" :label="'Pembayar'" :type="'select'" :options="$customers" :name="'pembayar_id'" :required="true"></x-input>
<x-input :value="$tarifagen->penerima_id??old('penerima_id')" :col="6" :label="'Penerima'" :type="'select'" :options="$customers" :name="'penerima_id'" :required="true"></x-input>
<x-input :value="$tarifagen->tanggal??old('tanggal')" :col="6" :label="'Tanggal'" :type="'date'" :name="'tanggal'" :required="true"></x-input>
<x-input :value="$tarifagen->tipe??old('tipe')" :col="6" :label="'Shipment'" :type="'select'" :options="$shipments" :name="'tipe'" :required="true"></x-input>
<x-input :value="$tarifagen->dari??old('dari')" :col="6" :label="'Dari'" :type="'select'" :options="$lokasi" :name="'dari'" :required="true"></x-input>
<x-input :value="$tarifagen->tujuan??old('tujuan')" :col="6" :label="'Tujuan'" :type="'select'" :options="$lokasi" :name="'tujuan'" :required="true"></x-input>
<x-input :value="$tarifagen->tarif??old('tarif')" :col="6" :label="'Tarif'" :type="'number'" :name="'tarif'" :required="true"></x-input>
<x-input :value="$tarifagen->kubikasi??old('kubikasi')" :col="6" :label="'Kubikasi'" :type="'number'" :name="'kubikasi'" :required="true"></x-input>
<x-input :value="$tarifagen->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'text'" :name="'keterangan'" :required="true"></x-input>
<div class="col-6 mb-2">
    <label for="is_active_1"><input type="radio" name="is_active" value="1" id="is_active_1" checked> Active</label>
    <label for="is_active_0"><input type="radio" name="is_active" value="0" id="is_active_0">Non Active</label>
</div>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">Simpan Data</button>
</div>
</div>
