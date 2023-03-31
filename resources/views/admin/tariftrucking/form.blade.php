<div class="row">
{{-- <x-input :value="$tariftrucking->customer_id??old('customer_id')" :col="6" :label="'Customer'" :type="'select'" :options="$customers" :name="'customer_id'" :required="true"></x-input> --}}
<x-input :value="$tariftrucking->tujuan_id??old('tujuan_id')" :col="6" :label="'Tujuan'" :type="'select'" :options="$tujuan" :name="'tujuan_id'" :required="true"></x-input>
<x-input :value="$tariftrucking->tipe??old('tipe')" :col="6" :label="'Tipe'" :type="'select'" :options="['20'=>'20\'','40'=>'40\'','COMBO'=>'COMBO']" :name="'tipe'" :required="true"></x-input>
<x-input :value="$tariftrucking->tarif??old('tarif')" :col="6" :label="'Tarif'" :type="'rupiah'" :name="'tarif'" :required="true"></x-input>
<x-input :value="$tariftrucking->is_active??old('is_active')" :col="6" :label="'Status'" :type="'select'" :options="['1'=>'Aktif','0'=>'Non Aktif']" :name="'is_active'" :required="true"></x-input>

</div>
