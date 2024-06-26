<div class="row">
<x-input :value="$agen->kode??old('kode')" :col="6" :label="'Kode'" :type="'text'" :name="'kode'" :required="true"></x-input>
<x-input :value="$agen->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$agen->pic??old('pic')" :col="6" :label="'Pic'" :type="'text'" :name="'pic'"></x-input>
<x-input :value="$agen->kota??old('kota')" :col="6" :label="'Kota'" :type="'text'" :name="'kota'" :required="true"></x-input>
<x-input :value="$agen->telp??old('telp')" :col="6" :label="'Telp'" :type="'text'" :name="'telp'" :required="true"></x-input>
<x-input :value="$agen->fax??old('fax')" :col="6" :label="'Fax'" :type="'text'" :name="'fax'"></x-input>
<x-input :value="$agen->email??old('email')" :col="6" :label="'Email'" :type="'text'" :name="'email'"></x-input>
<x-input :value="$agen->hp??old('hp')" :col="6" :label="'HP'" :type="'text'" :name="'hp'"></x-input>
<x-input :value="$agen->top??old('top')" :col="6" :label="'top'" :type="'number'" :name="'top'"></x-input>
<x-input :value="$agen->alamat??old('alamat')" :col="12" :label="'Alamat'" :type="'textarea'" :name="'alamat'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($agen)?'Tambah':'Update' }} Data</button>
</div>
</div>

