<div class="row">
<x-select :options="[]" :value="$hutangpelayaran->tarif_pelayaran_id??old('tarif_pelayaran_id')" :col="6" :label="'Tarif_pelayaran_id'" :type="'select'" :name="'tarif_pelayaran_id'" :required="true"></x-select>
<x-select :options="[]" :value="$hutangpelayaran->order_id??old('order_id')" :col="6" :label="'Order_id'" :type="'select'" :name="'order_id'" :required="true"></x-select>
<x-input :value="$hutangpelayaran->jumlah??old('jumlah')" :col="6" :label="'Jumlah'" :type="'number'" :name="'jumlah'" :required="true"></x-input>
<x-select :options="[]" :value="$hutangpelayaran->status??old('status')" :col="6" :label="'Status'" :type="'select'" :name="'status'" :required="true"></x-select>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($hutangpelayaran)?'Tambah':'Update' }} Data</button>
</div>
</div>