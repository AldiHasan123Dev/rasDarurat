<div class="row">
    <x-input :value="$order->tarif_id??old('tarif_id')" :col="6" :label="'Pembayar'" :type="'select'" :options="$tarif" :name="'tarif_id'" :required="true"></x-input>
    <x-input :value="$order->jadwal_kapal_id??old('jadwal_kapal_id')" :col="6" :label="'Kapal'" :type="'select'" :options="[]" :name="'jadwal_kapal_id'" :required="true"></x-input>
    <x-input :value="''" :name="'tarif'" :col="3" :label="'Tarif'" :type="'text'" :disabled="true"></x-input>
    <x-input :value="''" :name="'satuan'" :col="3" :label="'Unit'" :type="'text'" :disabled="true"></x-input>
    <x-input :value="''" :name="'dari'" :col="3" :label="'Dari'" :type="'text'" :disabled="true"></x-input>
    <x-input :value="''" :name="'tujuan'" :col="3" :label="'Tujuan'" :type="'text'" :disabled="true"></x-input>
    <x-input :value="''" :name="'shipment'" :col="3" :label="'Shipment'" :type="'text'" :disabled="true"></x-input>
    <x-input :value="''" :name="'kondisi'" :col="3" :label="'Kondisi'" :type="'text'" :disabled="true"></x-input>
    <x-input :value="$order->pengirim_id??old('pengirim_id')" :col="3" :label="'Pengirim'" :type="'select'" :options="[]" :name="'pengirim_id'" :required="true" id="selectPengirim"></x-input>
    <x-input :value="$order->penerima_id??old('penerima_id')" :col="3" :label="'Penerima'" :type="'select'" :options="[]" :name="'penerima_id'" :required="true" id="selectPenerima"></x-input>
    <x-input :value="$order->barang_id??old('barang_id')" :col="3" :label="'Barang'" :type="'select'" :options="$barang" :name="'barang_id'" :required="true" id="selectBarang"></x-input>
    {{-- <x-input :options="$satuan" :value="$order->satuan??old('satuan')" :col="3" :label="'Satuan'" :type="'select'" :name="'satuan'"></x-input> --}}
    <x-input :value="$order->ba_kirim??old('ba_kirim')" :col="3" :label="'BA Kirim'" :type="'date'" :name="'ba_kirim'"></x-input>
    <x-input :value="$order->stuffing??old('stuffing')" :col="3" :label="'Stuffing'" :type="'date'" :name="'stuffing'"></x-input>
    <x-input :value="$order->agen??'NON AGEN'" :col="3" :label="'Tipe Agen'" :type="'select'" :options="['AGEN'=>'AGEN','NON AGEN'=>'NON AGEN']" :name="'agen'"></x-input>
    <x-input :value="$order->asuransi??old('asuransi')" :col="3" :label="'Asuransi'" :type="'select'" :options="['ADA INC'=>'ADA INC','ADA EXC'=>'ADA EXC','TIDAK'=>'TIDAK']" :name="'asuransi'"></x-input>
    <x-input :value="$order->full??old('full')" :col="3" :label="'Tanggal Full'" :type="'date'" :name="'full'"></x-input>
    <x-input :value="$order->barang_diantar??old('barang_diantar')" :col="3" :label="'Barang Diantar'" :type="'date'" :name="'barang_diantar'"></x-input>
    <x-input :value="$order->ba_kembali??old('ba_kembali')" :col="3" :label="'Ba Kembali'" :type="'date'" :name="'ba_kembali'"></x-input>
    <x-input :value="$order->resi??old('resi')" :col="3" :label="'No. Resi'" :type="'text'" :name="'resi'"></x-input>
    <x-input :value="$order->nopol??old('nopol')" :col="3" :label="'Nopol'" :type="'text'" :name="'nopol'"></x-input>
    <x-input :value="$order->trucking??old('trucking')" :col="3" :label="'Trucking'" :type="'select'" :options="['XPDC'=>'XPDC','SUPP'=>'SUPP']" :name="'trucking'"></x-input>
    <x-input :value="$order->container??old('container')" :col="3" :label="'No. Container'" :type="'text'" :name="'container'"></x-input>
    <x-input :value="$order->seal??old('seal')" :col="3" :label="'Seal'" :type="'text'" :name="'seal'"></x-input>
    <x-input :value="$order->no_bl??old('no_bl')" :col="3" :label="'No. BL'" :type="'text'" :name="'no_bl'"></x-input>
    <div class="col-3" id="nag">
        <x-input :value="$order->penerima_bl_id??old('penerima_bl_id')" :label="'Penerima BL'" :type="'select'" :options="[]" :name="'penerima_bl_id'"></x-input>
    </div>
    <div class="col-3" id="ag">
        <x-input :options="$agent" :value="$order->agen_id??old('agen_id')" :label="'Penerima BL'" :type="'select'" :name="'agen_id'"></x-input>
    </div>
    <x-input :value="$order->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'"></x-input>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">{{ empty($order)?'Tambah':'Update' }} Data</button>
    </div>
</div>

