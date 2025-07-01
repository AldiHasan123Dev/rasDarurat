<div class="row">
<x-input :value="$kendaraan->tipe??old('tipe')" :col="6" :label="'Tipe'" :type="'select'" :options="['20'=>'20\'','40'=>'40\'']"  :name="'tipe'" :required="true"></x-input>
<x-input :value="$kendaraan->nopol??old('nopol')" :col="6" :label="'Nopol'" :type="'text'" :name="'nopol'" :required="true"></x-input>
<x-input :value="$kendaraan->milik??old('milik')" :col="6" :label="'Milik'" :type="'select'" :options="['R1'=>'R1','R2'=>'R2','VENDOR'=>'VENDOR']" :name="'milik'" :required="true"></x-input>
<x-input :value="$kendaraan->masa_pkb??old('masa_pkb')" :col="6" :label="'Masa PKB'" :type="'date'" :name="'masa_pkb'" :required="true"></x-input>
<x-input :value="$kendaraan->kir??old('kir')" :col="6" :label="'KIR'" :type="'date'" :name="'kir'" :required="true"></x-input>
<x-input :value="$kendaraan->stid??old('stid')" :col="6" :label="'STID'" :type="'date'" :name="'stid'" :required="true"></x-input>
<x-input :value="$kendaraan->no_rangka??old('no_rangka')" :col="6" :label="'No. Rangka'" :type="'text'" :name="'no_rangka'" :required="true"></x-input>
<x-input :value="$kendaraan->no_mesin??old('no_mesin')" :col="6" :label="'No. Mesin'" :type="'text'" :name="'no_mesin'" :required="true"></x-input>
<x-input :value="$kendaraan->warna??old('warna')" :col="6" :label="'Warna'" :type="'text'" :name="'warna'" :required="true"></x-input>
<x-input :value="$kendaraan->tahun??old('tahun')" :col="6" :label="'Tahun'" :type="'text'" :name="'tahun'" :required="true"></x-input>
<x-input :value="$kendaraan->is_active??old('is_active')" :col="6" :label="'Status'" :type="'select'" :options="['1'=>'Active','0'=>'Tidak Aktif']" :name="'is_active'" :required="true"></x-input>
<x-input :value="$kendaraan->keterangan??old('keterangan')" :col="6" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($kendaraan)?'Tambah':'Update' }} Data</button>
</div>
</div>
