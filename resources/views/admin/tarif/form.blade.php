<div class="row">
    <x-input :options="$pelayaran" :value="$tarif->pelayaran_id??old('pelayaran_id')" :col="12" :label="'Pelayaran'" :type="'select'" :name="'pelayaran_id'" :required="true"></x-input>
    <x-input :options="[]" :value="$tarif->customer_id??old('customer_id')" :col="6" :label="'Customer'" :type="'select'" :name="'customer_id'" :required="true"></x-input>
    <x-input :options="$shipment" :value="$tarif->shipment??old('shipment')" :col="6" :label="'Shipment'" :type="'select'" :name="'shipment'" :required="true"></x-input>
    <x-input :options="$lokasi" :value="$tarif->dari??old('dari')" :col="6" :label="'Dari'" :type="'select'" :name="'dari'" :required="true"></x-input>
    <x-input :options="$lokasi" :value="$tarif->tujuan??old('tujuan')" :col="6" :label="'Tujuan'" :type="'select'" :name="'tujuan'" :required="true"></x-input>
    <x-input :options="$kondisi" :value="$tarif->kondisi??old('kondisi')" :col="6" :label="'Kondisi'" :type="'select'" :name="'kondisi'" :required="true"></x-input>
    <x-input :options="$satuan" :value="$tarif->satuan??old('satuan')" :col="6" :label="'Satuan'" :type="'select'" :name="'satuan'" :disabled="true"></x-input>
       <x-input :options="$satuan" 
         :value="old('satuan_inv', $tarif->satuan_inv ?? '')" 
         :col="6" 
         :label="'Satuan Invoice'" 
         :type="'select'" 
         :name="'satuan_inv'" 
         :required="true"
         :id="'satuan_inv'">
</x-input>
    <x-input :value="$tarif->tarif??old('tarif')" :id="'tarif-price'" :col="6" :label="'Tarif'" :type="'number'" :name="'tarif'" :required="true"></x-input>
    <x-input :value="$order->stuffing??'LUAR'" :col="6" :required="true" :label="'Stuffing Tipe'" :type="'select'" :options="['LUAR'=>'LUAR','DALAM'=>'DALAM']" :name="'stuffing'"></x-input>
    {{-- <x-input :value="$tarif->min_qty??old('min_qty')" :col="6" :label="'Min Qty'" :type="'text'" :name="'min_qty'"></x-input> --}}
    {{-- <x-input :value="$tarif->unit??old('unit')" :col="6" :label="'Unit'" :type="'text'" :name="'unit'"></x-input> --}}
    <x-input :value="$tarif->keterangan??old('keterangan')" :col="12" :label="'Keterangan'" :type="'textarea'" :name="'keterangan'" :required="true"></x-input>
</div>
