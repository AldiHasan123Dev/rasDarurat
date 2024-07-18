<div class="row">
<x-input :value="$setting->name??old('name')" :col="6" :label="'Name'" :type="'text'" :name="'name'" :required="true"></x-input>
<x-input :value="$setting->email??old('email')" :col="6" :label="'Email'" :type="'text'" :name="'email'" :required="true"></x-input>
<x-input :value="$setting->phone??old('phone')" :col="6" :label="'Phone'" :type="'text'" :name="'phone'" :required="true"></x-input>
<x-input :value="$setting->address??old('address')" :col="6" :label="'Address'" :type="'text'" :name="'address'" :required="true"></x-input>
<x-input :value="$setting->fax??old('fax')" :col="6" :label="'Fax'" :type="'text'" :name="'fax'" :required="true"></x-input>
<x-input :value="$setting->type_job_year??old('type_job_year')" :col="6" :label="'Type_job_year'" :type="'text'" :name="'type_job_year'" :required="true"></x-input>
<x-input :value="$setting->short_name??old('short_name')" :col="6" :label="'Short_name'" :type="'text'" :name="'short_name'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($setting)?'Tambah':'Update' }} Data</button>
</div>
</div>
