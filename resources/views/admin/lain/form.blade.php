<div class="row">
<x-input :value="$lain->nama??old('nama')" :col="12" :label="'Kategori'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$lain->cont_20??old('cont_20')" :col="6" :label="'Cont 20'" :type="'number'" :name="'cont_20'" :required="true"></x-input>
<x-input :value="$lain->cont_40??old('cont_40')" :col="6" :label="'Cont 40'" :type="'number'" :name="'cont_40'" :required="true"></x-input>
<x-input :value="$lain->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($lain)?'Tambah':'Update' }} Data</button>
</div>
</div>
