<div class="row">
<x-input :value="$tagihantrucking->order_id??old('order_id')" :col="6" :label="'Order_id'" :type="'number'" :name="'order_id'" :required="true"></x-input>
<x-input :value="$tagihantrucking->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$tagihantrucking->jumlah??old('jumlah')" :col="6" :label="'Jumlah'" :type="'number'" :name="'jumlah'" :required="true"></x-input>
<x-input :value="$tagihantrucking->catatan??old('catatan')" :col="6" :label="'Catatan'" :type="'text'" :name="'catatan'" :required="true"></x-input>
<x-input :value="$tagihantrucking->status??old('status')" :col="6" :label="'Status'" :type="'number'" :name="'status'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($tagihantrucking)?'Tambah':'Update' }} Data</button>
</div>
</div>