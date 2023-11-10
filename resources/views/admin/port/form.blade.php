<div class="row">
<x-input :value="$port->name??old('name')" :col="6" :label="'Name'" :type="'text'" :name="'name'" :required="true"></x-input>
{{-- <x-input :value="$port->status??old('status')" :col="6" :label="'Status'" :type="'number'" :name="'status'" :required="true"></x-input> --}}
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($port)?'Tambah':'Update' }} Data</button>
</div>
</div>
