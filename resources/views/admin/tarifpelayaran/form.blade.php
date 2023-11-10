@php
    $agens = \App\Models\Pelayaran::pluck('nama','id');
    $lokasi = \App\Models\Lokasi::pluck('nama','id');
    $customers = \App\Models\Customer::pluck('nama','id');
    $ports = \App\Models\Port::pluck('name','id');
@endphp

<div class="row">
    <x-input :value="$tarifpelayaran->customer_id??old('customer_id')" :col="12" :label="'Pembayar'" :type="'select'" :options="$customers" :name="'customer_id'" :required="false"></x-input>
    <x-input :value="$tarifpelayaran->pelayaran_id??old('pelayaran_id')" :col="12" :label="'Pelayaran'" :type="'select'" :options="$agens" :name="'pelayaran_id'" :required="true"></x-input>
    <x-input :value="$tarifpelayaran->tanggal??old('tanggal')" :col="6" :label="'Tanggal'" :type="'date'" :name="'tanggal'" :required="true"></x-input>
    <x-input :value="$tarifpelayaran->tipe??old('tipe')" :col="6" :label="'Shipment'" :type="'select'" :options="$shipments" :name="'tipe'" :required="true"></x-input>
    <x-input :value="$tarifpelayaran->port_id??old('port_id')" :col="6" :label="'Dari'" :type="'select'" :options="$ports" :name="'port_id'" :required="true"></x-input>
    <x-input :value="$tarifpelayaran->tujuan??old('tujuan')" :col="6" :label="'Tujuan'" :type="'select'" :options="$lokasi" :name="'tujuan'" :required="true"></x-input>
    <x-input :value="$tarifpelayaran->tarif??old('tarif')" :col="6" :label="'Tarif'" :type="'number'" :name="'tarif'" :required="true"></x-input>
    <x-input :value="$tarifpelayaran->kubikasi??old('kubikasi')" :col="6" :label="'Kubikasi'" :type="'number'" :name="'kubikasi'" :required="true"></x-input>
    <x-input :value="$tarifpelayaran->komoditi??old('komoditi')" :col="12" :label="'Komoditi'" :type="'text'" :name="'komoditi'" :required="true"></x-input>
    <x-input :value="$tarifpelayaran->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'text'" :name="'keterangan'" :required="true"></x-input>
    <div class="col-6 mb-2">
        <label for="is_active_1"><input type="radio" name="is_active" value="1" id="is_active_1" checked> Active</label>
        <label for="is_active_0"><input type="radio" name="is_active" value="0" id="is_active_0">Non Active</label>
    </div>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">Simpan Data</button>
    </div>
    </div>
