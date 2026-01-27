@php
    $ports = App\Models\Port::pluck('name', 'id');
@endphp
<div class="row">
    @csrf
    @if (!empty($order))
        @if ($order->tarif)
            {{-- @if ($order->tarif->is_active == 0)
                <x-input :value="$pembayar" :col="6" :label="'Pembayar'" :type="'text'" :name="'null'" :disabled="true"></x-input>
                <x-input :value="$order->jadwal_kapal->kapal.' || '.$order->jadwal_kapal->pelayaran->nama.' || VOY. '.$order->jadwal_kapal->voyage" :col="6" :label="'Kapal'" :type="'text'" :name="'null'" :disabled="true" ></x-input>
            @else
            @endif --}}
            <x-input :value="$order->tarif_id ?? old('tarif_id')" :col="6" :label="'Pembayar'" :type="'select'" :options="$tarif"
                :name="'tarif_id'" :required="true"></x-input>
            <x-input :value="$order->jadwal_kapal_id ?? old('jadwal_kapal_id')" :col="6" :label="'Kapal'" :type="'select'" :options="[]"
                :name="'jadwal_kapal_id'" :required="true"></x-input>
        @else
            <x-input :value="$order->tarif_id ?? old('tarif_id')" :col="6" :label="'Pembayar'" :type="'select'" :options="$tarif"
                :name="'tarif_id'" :required="true"></x-input>
            <x-input :value="$order->jadwal_kapal_id ?? old('jadwal_kapal_id')" :col="6" :label="'Kapal'" :type="'select'" :options="[]"
                :name="'jadwal_kapal_id'" :required="true"></x-input>
        @endif
    @else
        <x-input :value="$order->tarif_id ?? old('tarif_id')" :col="6" :label="'Pembayar'" :type="'select'" :options="$tarif"
            :name="'tarif_id'" :required="true"></x-input>
        <x-input :value="$order->jadwal_kapal_id ?? old('jadwal_kapal_id')" :col="6" :label="'Kapal'" :type="'select'" :options="[]"
            :name="'jadwal_kapal_id'" :required="true"></x-input>
    @endif
    <x-input :value="''" :name="'tarif'" :col="3" :label="'Tarif'" :type="'text'"
        :disabled="true"></x-input>
    <x-input :value="''" :name="'satuan'" :col="3" :label="'Unit'" :type="'text'"
        :disabled="true"></x-input>
    <x-input :value="''" :name="'dari'" :col="3" :label="'Dari'" :type="'text'"
        :disabled="true"></x-input>
    <x-input :value="''" :name="'tujuan'" :col="3" :label="'Tujuan'" :type="'text'"
        :disabled="true"></x-input>
    <x-input :value="''" :name="'shipment'" :col="3" :label="'Shipment'" :type="'text'"
        :disabled="true"></x-input>
    <x-input :value="''" :name="'kondisi'" :col="3" :label="'Kondisi'" :type="'text'"
        :disabled="true"></x-input>
    <x-input :value="$order->pengirim->nama ?? old('pengirim_id')" :col="3" :label="'Pengirim'" :type="'text'" :name="'pengirim_id'"
        :required="true"></x-input>
    <x-input :value="$order->penerima->nama ?? old('penerima_id')" :col="3" :label="'Penerima'" :type="'text'" :name="'penerima_id'"
        :required="true"></x-input>
    <x-input :value="$order->barang->nama ?? old('barang_id')" :col="3" :label="'Barang'" :type="'text'" :name="'barang_id'"
        :required="true"></x-input>
    <x-input :value="$order->nopol ?? old('nopol')" :col="3" :label="'Nopol'" :type="'text'" :name="'nopol'"></x-input>
    <x-input :value="$order->trucking ?? old('trucking')" :col="3" :label="'Trucking'" :type="'select'" :options="['' => '', 'XPDC' => 'XPDC', 'SUPP' => 'SUPP', 'CUST' => 'CUST']"
        :name="'trucking'"></x-input>
    <x-input :value="$order->container ?? old('container')" :col="3" :label="'No. Container'" :type="'text'"
        :name="'container'"></x-input>
    <x-input :value="$order->seal ?? old('seal')" :col="3" :label="'Seal'" :type="'text'"
        :name="'seal'"></x-input>
    {{-- <x-input :options="$satuan" :value="$order->satuan??old('satuan')" :col="3" :label="'Satuan'" :type="'select'" :name="'satuan'"></x-input> --}}
    <x-input :value="$order->stuffing ?? old('stuffing')" :col="3" :label="'Stuffing'" :type="'date'"
        :name="'stuffing'"></x-input>
    <x-input :value="$order->full ?? old('full')" :col="3" :label="'Tanggal Full'" :type="'date'"
        :name="'full'"></x-input>
    {{-- <x-input :value="$order->barang_diantar??old('barang_diantar')" :col="3" :label="'Barang Diantar'" :type="'date'" :name="'barang_diantar'"></x-input> --}}
    {{-- <x-input :value="$order->ba_kirim??old('ba_kirim')" :col="3" :label="'BA Kirim'" :type="'date'" :name="'ba_kirim'"></x-input> --}}
    <x-input :value="$order->agen ?? 'NON AGEN'" :col="3" :label="'Tipe Agen'" :type="'select'" :options="['AGEN' => 'AGEN', 'NON AGEN' => 'NON AGEN']"
        :name="'agen'"></x-input>
    <div class="col-3" id="nag">
        <x-input :value="$order->penerima_bl->nama ?? old('penerima_bl_id')" :label="'Penerima BL'" :type="'text'" :name="'penerima_bl_id'"></x-input>
    </div>
    <div class="col-3" id="ag">
        <x-input :value="$order->agent->nama ?? old('agen_id')" :label="'Penerima BL'" :type="'text'" :name="'agen_id'"></x-input>
    </div>
    {{-- <x-input :value="$order->ba_kembali??old('ba_kembali')" :col="3" :label="'Ba Kembali'" :type="'date'" :name="'ba_kembali'"></x-input> --}}
    <x-input :value="$order->asuransi ?? old('asuransi')" :col="3" :label="'Asuransi'" :type="'select'" :options="['ADA INC' => 'ADA INC', 'ADA EXC' => 'ADA EXC', 'TIDAK' => 'TIDAK']"
        :name="'asuransi'"></x-input>
    <x-input :value="$order->no_bl ?? old('no_bl')" :col="3" :label="'No. BL'" :type="'text'"
        :name="'no_bl'"></x-input>
    <x-input :value="$order->resi ?? old('resi')" :col="3" :label="'No. Resi'" :type="'text'"
        :name="'resi'"></x-input>
    <x-input :value="$order->komisi ?? old('komisi')" :col="3" :label="'RC Customer/fee'" :type="'number'" :name="'komisi'"
        :required="true"></x-input>
    <x-input :value="$order->port_id ?? 1" :col="3" :label="'Port'" :type="'select'" :options="$ports"
        :name="'port_id'"></x-input>
    <x-input :value="$order->keterangan ?? old('keterangan')" :col="12" :label="'Keterangan'" :type="'textarea'"
        :name="'keterangan'"></x-input>
    {{-- <div class="col-6 mb-3">
        <label for="tipe">Tipe</label>
        <div class="d-flex gap-3">
            <label for="tipe1">
                <input type="radio" name="tipe" {{ $order?($order->tipe=='muatan'?'checked':''):'checked' }} id="tipe1" value="muatan">
                MUATAN
            </label>
            <label for="tipe2">
                <input type="radio" name="tipe" {{ $order?($order->tipe=='bongkaran'?'checked':''):'' }} id="tipe2" value="bongkaran">
                BONGKARAN
            </label>
        </div>
    </div> --}}
</div>
