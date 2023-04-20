<div class="row">
<x-input :value="$sangusopir->tujuanInfo->nama??old('tujuan')" :col="12" :label="'Tujuan'" :type="'text'" :name="'tujuan'" :required="true"></x-input>
<x-input :value="$sangusopir->ukuran_20??old('ukuran_20')" :col="6" :label="'Borongan Sopir 20'" :type="'rupiah'" :name="'ukuran_20'" :required="true"></x-input>
<x-input :value="$sangusopir->borongan_kuli_20??old('borongan_kuli_20')" :col="6" :label="'Borongan Kuli 20'" :type="'rupiah'" :name="'borongan_kuli_20'" :required="true"></x-input>
<x-input :value="$sangusopir->ukuran_40??old('ukuran_40')" :col="6" :label="'Borongan Sopir 40'" :type="'rupiah'" :name="'ukuran_40'" :required="true"></x-input>
<x-input :value="$sangusopir->borongan_kuli_40??old('borongan_kuli_40')" :col="6" :label="'Borongan Kuli 40'" :type="'rupiah'" :name="'borongan_kuli_40'" :required="true"></x-input>
<x-input :value="$sangusopir->ukuran_combo??old('ukuran_combo')" :col="6" :label="'Borongan Sopir Combo 2x20'" :type="'rupiah'" :name="'ukuran_combo'" :required="true"></x-input>
<x-input :value="$sangusopir->borongan_kuli_combo??old('borongan_kuli_combo')" :col="6" :label="'Borongan Kuli Combo 2x20'" :type="'rupiah'" :name="'borongan_kuli_combo'" :required="true"></x-input>
<x-input :value="$sangusopir->is_active??old('is_active')" :col="6" :label="'Status'" :type="'select'" :options="['1'=>'Active','0'=>'Tidak Aktif']" :name="'is_active'" :required="true"></x-input>
{{-- <div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($sangusopir)?'Tambah':'Update' }} Data</button>
</div> --}}
</div>
