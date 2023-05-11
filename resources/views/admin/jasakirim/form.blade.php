@php
    $lokasi = App\Models\Lokasi::pluck('nama','id');
    $ekspedisi = [
        'JNE' => 'JNE',
        'JNT' => 'JNT',
        'POS' => 'POS',
    ];
@endphp

<div class="row">
<x-input :options="$lokasi" :value="$jasakirim->lokasi_id??old('lokasi_id')" :col="6" :label="'Tujuan'" :type="'select'" :name="'lokasi_id'" :required="true"></x-input>
<x-input :value="$jasakirim->barcode??old('barcode')" :col="6" :label="'Barcode'" :type="'text'" :name="'barcode'" :required="true"></x-input>
<x-input :value="$jasakirim->tgl_kirim??old('tgl_kirim')" :col="6" :label="'Tgl Kirim'" :type="'date'" :name="'tgl_kirim'" :required="true"></x-input>
<x-input :value="$jasakirim->tgl_terima??old('tgl_terima')" :col="6" :label="'Tgl Terima'" :type="'date'" :name="'tgl_terima'" :required="true"></x-input>
<x-input :value="$jasakirim->nominal??old('nominal')" :col="6" :label="'Nominal'" :type="'number'" :name="'nominal'" :required="true"></x-input>
<x-input :options="$ekspedisi" :value="$jasakirim->ekspedisi??old('ekspedisi')" :col="6" :label="'Ekspedisi'" :type="'select'" :name="'ekspedisi'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($jasakirim)?'Tambah':'Update' }} Data</button>
</div>
</div>
