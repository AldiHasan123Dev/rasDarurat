<div class="row">
<x-input :value="$tagihan->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$tagihan->jumlah??old('jumlah')" :col="6" :label="'Jumlah'" :type="'number'" :name="'jumlah'" :required="true"></x-input>
<x-input :value="$tagihan->catatan??old('catatan')" :col="6" :label="'Catatan'" :type="'textarea'" :name="'catatan'" :required="true"></x-input>
<x-input :value="$tagihan->status??old('status')" :col="6" :label="'Status'" :type="'number'" :name="'status'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($tagihan)?'Tambah':'Update' }} Data</button>
</div>
</div>