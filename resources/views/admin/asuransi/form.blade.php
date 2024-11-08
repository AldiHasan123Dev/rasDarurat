@php
    $pelayaran = \App\Models\Pelayaran::pluck('nama','id');
    $nama = [
        'TOKYO MARINE' => 'TOKYO MARINE',
        'MSIG' => 'MSIG',
        'PALISADE' => 'PALISADE',
        'BHAYANGKARA' => 'BHAYANGKARA',
        'BHAYANGKARA + ADMIN' => 'BHAYANGKARA + ADMIN',
        'MARINE CARGO HARTA' => 'MARINE CARGO HARTA',
        'Marine Cargo Harta + Admin' => 'Marine Cargo Harta + Admin'
    ];
@endphp

<div class="row">
<x-input :value="$asuransi->pelayaran_id??old('pelayaran_id')" :col="6" :label="'Pelayaran'" :type="'select'" :options="$pelayaran" :name="'pelayaran_id'" :required="true"></x-input>
<x-input :value="$asuransi->nama??old('nama')" :col="6" :label="'Asuransi'" :type="'select'" :options="$nama" :name="'nama'" :required="true"></x-input>
<x-input :value="$asuransi->rate??old('rate')" :col="6" :label="'Rate'" :type="'number'" :name="'rate'" :required="true"></x-input>
<x-input :value="$asuransi->admin??old('admin')" :col="6" :label="'Admin'" :type="'number'" :name="'admin'" :required="true"></x-input>
<x-input :value="$asuransi->min??old('min')" :col="6" :label="'Min'" :type="'number'" :name="'min'" :required="true"></x-input>
<x-input :value="$asuransi->max??old('max')" :col="6" :label="'Max'" :type="'number'" :name="'max'" :required="true"></x-input>
<x-input :value="$asuransi->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($asuransi)?'Tambah':'Update' }} Data</button>
</div>
</div>
