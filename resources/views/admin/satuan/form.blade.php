<div class="row">
<x-input :value="$satuan->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($satuan)?'Tambah':'Update' }} Data</button>
</div>
</div>