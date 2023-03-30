<div class="row">
<x-input :value="$kendaraan->tipe??old('tipe')" :col="6" :label="'Tipe'" :type="'select'" :options="['20'=>'20\'','40'=>'40\'']"  :name="'tipe'" :required="true"></x-input>
<x-input :value="$kendaraan->nopol??old('nopol')" :col="6" :label="'Nopol'" :type="'text'" :name="'nopol'" :required="true"></x-input>
<x-input :value="$kendaraan->milik??old('milik')" :col="6" :label="'Milik'" :type="'select'" :options="['R1'=>'R1','R2'=>'R2','VENDOR'=>'VENDOR']" :name="'milik'" :required="true"></x-input>
<x-input :value="$kendaraan->is_active??old('is_active')" :col="6" :label="'Status'" :type="'select'" :options="['1'=>'Active','0'=>'Tidak Aktif']" :name="'is_active'" :required="true"></x-input>
<x-input :value="$kendaraan->keterangan??old('keterangan')" :col="6" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($kendaraan)?'Tambah':'Update' }} Data</button>
</div>
</div>
