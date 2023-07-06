<div class="row">
    @if ($bttb)
        <input type="hidden" name="order_id" id="order_id" value="{{ $bttb->order_id }}">
    @else
        @if (empty($order))

        @else
            <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">
        @endif
    @endif
<x-input :value="$bttb->no_gudang??old('no_gudang')" :col="6" :label="'No. Gudang'" :type="'text'" :name="'no_gudang'" :required="true"></x-input>
<x-input :value="$bttb->barang_id??old('barang_id')" :col="6" :label="'Barang'" :type="'text'" :name="'barang_id'" :id="'barang_bttb'" :required="true"></x-input>
<x-input :value="$bttb->qty??old('qty')" :col="6" :label="'Qty'" :type="'number'" :name="'qty'" :required="true"></x-input>
<x-input :value="$bttb->satuan_id??old('satuan_id')" :col="6" :label="'Satuan'" :type="'text'" :name="'satuan_id'" :required="true"></x-input>
<x-input :value="$bttb->p??old('p')" :col="4" :label="'P'" :type="'number'" :name="'p'"></x-input>
<x-input :value="$bttb->l??old('l')" :col="4" :label="'L'" :type="'number'" :name="'l'"></x-input>
<x-input :value="$bttb->t??old('t')" :col="4" :label="'T'" :type="'number'" :name="'t'"></x-input>
<x-input :value="$bttb->t??old('vol')" :col="6" :label="'Vol Manual'" :type="'number'" :name="'vol'" :readonly="true"></x-input>
<x-input :value="$bttb->t??old('berat')" :col="6" :label="'Berat'" :type="'number'" :name="'berat'"></x-input>
<x-input :value="$bttb->tgl_masuk??date('Y-m-d')" :col="6" :label="'Tgl Masuk'" :type="'date'" :name="'tgl_masuk'" :required="true"></x-input>
{{-- @if ($bttb)
    <x-input :value="$bttb->pengirim_id" :col="6" :label="'Pengirim'" :type="'select'" :options="[$bttb->pengirim_id=>$bttb->pengirim->nama]" :name="'pengirim_id'" :required="true"></x-input>
@else
    @if (empty($order))
        <x-input :value="''" :col="12" :label="'Pengirim'" :type="'select'" :options="[]" :name="'pengirim_id'" :required="true"></x-input>
    @else
        <x-input :value="$order->pengirim_id" :col="12" :label="'Pengirim'" :type="'select'" :options="[]" :name="'pengirim_id'" :required="true"></x-input>
    @endif
@endif --}}
<x-input :value="$bttb->pengirim_id??old('pengirim_id')" :col="12" :label="'Pengirim'" :id="'pengirim_bttb'" :type="'text'" :name="'pengirim_id'" :required="true"></x-input>
<x-input :value="$bttb->keterangan??old('keterangan')" :col="12" :id="'keterangan-bttb'" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'" :required="true"></x-input>
</div>
