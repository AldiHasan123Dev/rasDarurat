<div class="row">
<x-input :value="$truk->kode??old('kode')" :col="6" :label="'Kode'" :type="'text'" :name="'kode'" :required="true"></x-input>
<x-input :value="$truk->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$truk->pic??old('pic')" :col="6" :label="'Pic'" :type="'text'" :name="'pic'"></x-input>
<x-input :value="$truk->kota??old('kota')" :col="6" :label="'Kota'" :type="'text'" :name="'kota'" :required="true"></x-input>
<x-input :value="$truk->telp??old('telp')" :col="6" :label="'Telp'" :type="'text'" :name="'telp'" :required="true"></x-input>
<x-input :value="$truk->fax??old('fax')" :col="6" :label="'Fax'" :type="'text'" :name="'fax'"></x-input>
<x-input :value="$truk->email??old('email')" :col="6" :label="'Email'" :type="'text'" :name="'email'"></x-input>
<x-input :value="$truk->hp??old('hp')" :col="6" :label="'HP'" :type="'text'" :name="'hp'"></x-input>
<x-input :value="$truk->alamat??old('alamat')" :col="12" :label="'Alamat'" :type="'textarea'" :name="'alamat'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($truk)?'Tambah':'Update' }} Data</button>
</div>
</div>
