<div class="row">
<x-input :value="$sangusopir->tujuanInfo->nama??old('tujuan')" :col="6" :label="'Tujuan'" :type="'text'" :name="'tujuan'" :required="true"></x-input>
<x-input :value="$sangusopir->ukuran_20??old('ukuran_20')" :col="6" :label="'Sangu 20'" :type="'rupiah'" :name="'ukuran_20'" :required="true"></x-input>
<x-input :value="$sangusopir->ukuran_40??old('ukuran_40')" :col="6" :label="'Sangu 40'" :type="'rupiah'" :name="'ukuran_40'" :required="true"></x-input>
<x-input :value="$sangusopir->ukuran_combo??old('ukuran_combo')" :col="6" :label="'Sangu Combo 2x20'" :type="'rupiah'" :name="'ukuran_combo'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($sangusopir)?'Tambah':'Update' }} Data</button>
</div>
</div>
