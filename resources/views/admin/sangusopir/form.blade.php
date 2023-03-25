<div class="row">
<x-input :value="$sangusopir->tujuan??old('tujuan')" :col="6" :label="'Tujuan'" :type="'text'" :name="'tujuan'" :required="true"></x-input>
<x-input :value="$sangusopir->ukuran??old('ukuran')" :col="6" :label="'Ukuran'" :type="'text'" :name="'ukuran'" :required="true"></x-input>
<x-input :value="$sangusopir->sangu??old('sangu')" :col="6" :label="'Sangu'" :type="'text'" :name="'sangu'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($sangusopir)?'Tambah':'Update' }} Data</button>
</div>
</div>