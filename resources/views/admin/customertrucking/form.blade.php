<div class="row">
<x-input :value="$customertrucking->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$customertrucking->pic??old('pic')" :col="6" :label="'PIC'" :type="'text'" :name="'pic'" :required="true"></x-input>
<x-input :value="$customertrucking->alamat??old('alamat')" :col="6" :label="'Alamat'" :type="'text'" :name="'alamat'" :required="true"></x-input>
<x-input :value="$customertrucking->hp??old('hp')" :col="6" :label="'HP'" :type="'text'" :name="'hp'" :required="true"></x-input>
<x-input :value="$customertrucking->nik??old('nik')" :col="6" :label="'NIK'" :type="'text'" :name="'nik'" :required="true"></x-input>
<x-input :value="$customertrucking->npwp??old('npwp')" :col="6" :label="'NPWP'" :type="'text'" :name="'npwp'" :required="true"></x-input>
<x-input :value="$customertrucking->nama_npwp??old('nama_npwp')" :col="6" :label="'Nama NPWP'" :type="'text'" :name="'nama_npwp'" :required="true"></x-input>
<x-input :value="$customertrucking->alamat_npwp??old('alamat_npwp')" :col="6" :label="'Alamat NPWP'" :type="'text'" :name="'alamat_npwp'" :required="true"></x-input>
<x-input 
    :label="'Keterangan'" 
    :col="6" 
    :name="'keterangan'" 
    :value="$customertrucking->keterangan ?? old('keterangan')" 
    :type="'textarea'" 
    :rows="5" 
    :cols="30" 
    :required="true">
</x-input>

<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($customertrucking)?'Tambah':'Update' }} Data</button>
</div>
</div>
