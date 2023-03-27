<div class="row">
<x-input :value="$sopir->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$sopir->alamat??old('alamat')" :col="6" :label="'Alamat'" :type="'text'" :name="'alamat'" :required="true"></x-input>
<x-input :value="$sopir->hp??old('hp')" :col="6" :label="'Hp'" :type="'text'" :name="'hp'" :required="true"></x-input>
<x-input :value="$kendaraan->is_active??old('is_active')" :col="6" :label="'Status'" :type="'select'" :options="['1'=>'Active','0'=>'Tidak Aktif']" :name="'is_active'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($sopir)?'Tambah':'Update' }} Data</button>
</div>
</div>
