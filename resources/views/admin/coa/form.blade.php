@php
    $data_coa = array();
    $coas = App\Models\COA::select('nama','id','kode')->get();
    foreach ($coas as $key => $value) {
        $data_coa[$value->id] = $value->kode.' - '.$value->nama;
    }
@endphp

<div class="row">
<x-input :options="$data_coa" :value="$coa->coa_id??old('coa_id')" :col="6" :label="'COA Induk'" :type="'select'" :name="'coa_id'" :required="true"></x-input>
<x-input :value="$coa->kode??old('kode')" :col="6" :label="'Kode'" :type="'text'" :name="'kode'" :required="true"></x-input>
<x-input :value="$coa->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$coa->kategori??old('kategori')" :col="6" :label="'Kategori LR'" :type="'select'" :options="['A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G']" :name="'kategori'" :required="true"></x-input>
<x-input :value="$coa->keterangan??old('keterangan')" :col="6" :label="'Keterangan'" :type="'text'" :name="'keterangan'" :required="false"></x-input>
<x-input :value="$account->is_active??old('is_active')" :col="6" :label="'Status'" :type="'select'" :options="[0=>'Non Aktif',1=>'Aktif']" :name="'is_active'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($coa)?'Tambah':'Update' }} Data</button>
</div>
</div>
