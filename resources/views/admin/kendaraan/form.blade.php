<div class="row">
<x-input :value="$kendaraan->tipe??old('tipe')" :col="6" :label="'Tipe'" :type="'text'" :name="'tipe'" :required="true"></x-input>
<x-input :value="$kendaraan->nopol??old('nopol')" :col="6" :label="'Nopol'" :type="'text'" :name="'nopol'" :required="true"></x-input>
<x-input :value="$kendaraan->milik??old('milik')" :col="6" :label="'Milik'" :type="'text'" :name="'milik'" :required="true"></x-input>
<x-input :value="$kendaraan->is_active??old('is_active')" :col="6" :label="'Is_active'" :type="'number'" :name="'is_active'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($kendaraan)?'Tambah':'Update' }} Data</button>
</div>
</div>