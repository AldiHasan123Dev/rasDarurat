<div class="row">
<x-input :value="$asuransi->pelayaran_id??old('pelayaran_id')" :col="6" :label="'Pelayaran_id'" :type="'text'" :name="'pelayaran_id'" :required="true"></x-input>
<x-input :value="$asuransi->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$asuransi->rate??old('rate')" :col="6" :label="'Rate'" :type="'number'" :name="'rate'" :required="true"></x-input>
<x-input :value="$asuransi->admin??old('admin')" :col="6" :label="'Admin'" :type="'number'" :name="'admin'" :required="true"></x-input>
<x-input :value="$asuransi->min??old('min')" :col="6" :label="'Min'" :type="'number'" :name="'min'" :required="true"></x-input>
<x-input :value="$asuransi->max??old('max')" :col="6" :label="'Max'" :type="'number'" :name="'max'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($asuransi)?'Tambah':'Update' }} Data</button>
</div>
</div>