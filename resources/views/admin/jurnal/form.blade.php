<div class="row">
<x-select :options="[]" :value="$jurnal->coa_id??old('coa_id')" :col="6" :label="'Coa_id'" :type="'select'" :name="'coa_id'" :required="true"></x-select>
<x-select :options="[]" :value="$jurnal->order_id??old('order_id')" :col="6" :label="'Order_id'" :type="'select'" :name="'order_id'" :required="true"></x-select>
<x-input :value="$jurnal->nomor??old('nomor')" :col="6" :label="'Nomor'" :type="'text'" :name="'nomor'" :required="true"></x-input>
<x-input :value="$jurnal->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$jurnal->debit??old('debit')" :col="6" :label="'Debit'" :type="'number'" :name="'debit'" :required="true"></x-input>
<x-input :value="$jurnal->credit??old('credit')" :col="6" :label="'Credit'" :type="'number'" :name="'credit'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($jurnal)?'Tambah':'Update' }} Data</button>
</div>
</div>