<div class="row">
<input type="hidden" name="order_id" value="{{ $bttb->order_id??$order->id }}">
<x-input :value="$bttb->no_gudang??old('no_gudang')" :col="6" :label="'No. Gudang'" :type="'text'" :name="'no_gudang'" :required="true"></x-input>
<x-input :value="$bttb->barang_id??old('barang_id')" :col="6" :label="'Barang'" :type="'select'" :options="$barang" :name="'barang_id'" :required="true"></x-input>
<x-input :value="$bttb->qty??old('qty')" :col="6" :label="'Qty'" :type="'number'" :name="'qty'" :required="true"></x-input>
<x-input :value="$bttb->satuan_id??old('satuan_id')" :col="6" :label="'Satuan'" :type="'select'" :options="$satuan" :name="'satuan_id'" :required="true"></x-input>
<x-input :value="$bttb->tgl_masuk??date('Y-m-d')" :col="6" :label="'Tgl Masuk'" :type="'date'" :name="'tgl_masuk'" :required="true"></x-input>
<x-input :value="$bttb->pengirim_id??$order->pengirim_id" :col="6" :label="'Pengirim'" :type="'select'" :options="$pengirim" :name="'pengirim_id'" :required="true"></x-input>
<x-input :value="$bttb->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($bttb)?'Tambah':'Update' }} Data</button>
</div>
</div>