<div class="row">
<x-select :options="[]" :value="$hutangagen->agen_id??old('agen_id')" :col="6" :label="'Agen_id'" :type="'select'" :name="'agen_id'" :required="true"></x-select>
<x-select :options="[]" :value="$hutangagen->order_id??old('order_id')" :col="6" :label="'Order_id'" :type="'select'" :name="'order_id'" :required="true"></x-select>
<x-input :value="$hutangagen->harga??old('harga')" :col="6" :label="'Harga'" :type="'number'" :name="'harga'" :required="true"></x-input>
<x-input :value="$hutangagen->tanggal_kirim??old('tanggal_kirim')" :col="6" :label="'Tanggal_kirim'" :type="'date'" :name="'tanggal_kirim'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($hutangagen)?'Tambah':'Update' }} Data</button>
</div>
</div>
