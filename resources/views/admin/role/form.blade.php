<div class="row">
<x-input :value="$role->name??old('name')" :col="6" :label="'Name'" :type="'text'" :name="'name'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($role)?'Tambah':'Update' }} Data</button>
</div>
</div>