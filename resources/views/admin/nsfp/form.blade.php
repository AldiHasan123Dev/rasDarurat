<div class="row">
<x-input :value="$nsfp->nomor??old('nomor')" :col="12" :label="'Nomor'" :type="'text'" :name="'nomor'" :required="true"></x-input>
<x-input :value="$nsfp->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'" :required="true"></x-input>
{{-- <x-input :value="$nsfp->available??old('available')" :col="6" :label="'Available'" :type="'number'" :name="'available'" :required="true"></x-input> --}}
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($nsfp)?'Tambah':'Update' }} Data</button>
</div>
</div>
