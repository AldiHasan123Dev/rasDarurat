<div class="row">
<x-input :value="$customertrucking->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$customertrucking->alamat??old('alamat')" :col="6" :label="'Alamat'" :type="'text'" :name="'alamat'" :required="true"></x-input>
<x-input :value="$customertrucking->hp??old('hp')" :col="6" :label="'Hp'" :type="'text'" :name="'hp'" :required="true"></x-input>
<x-input :value="$customertrucking->nik??old('nik')" :col="6" :label="'Nik'" :type="'text'" :name="'nik'" :required="true"></x-input>
<x-input :value="$customertrucking->npwp??old('npwp')" :col="6" :label="'Npwp'" :type="'text'" :name="'npwp'" :required="true"></x-input>
<x-input :value="$customertrucking->nama_npwp??old('nama_npwp')" :col="6" :label="'Nama_npwp'" :type="'text'" :name="'nama_npwp'" :required="true"></x-input>
<x-input :value="$customertrucking->alamat_npwp??old('alamat_npwp')" :col="6" :label="'Alamat_npwp'" :type="'text'" :name="'alamat_npwp'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($customertrucking)?'Tambah':'Update' }} Data</button>
</div>
</div>