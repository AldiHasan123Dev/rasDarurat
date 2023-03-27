<div class="row">
<x-input :value="$ordertrucking->order_id??old('order_id')" :col="6" :label="'Order_id'" :type="'text'" :name="'order_id'" :required="true"></x-input>
<x-input :value="$ordertrucking->customer_id??old('customer_id')" :col="6" :label="'Customer_id'" :type="'text'" :name="'customer_id'" :required="true"></x-input>
<x-input :value="$ordertrucking->sopir_id??old('sopir_id')" :col="6" :label="'Sopir_id'" :type="'text'" :name="'sopir_id'" :required="true"></x-input>
<x-input :value="$ordertrucking->kendaraan_id??old('kendaraan_id')" :col="6" :label="'Kendaraan_id'" :type="'text'" :name="'kendaraan_id'" :required="true"></x-input>
<x-input :value="$ordertrucking->dari??old('dari')" :col="6" :label="'Dari'" :type="'text'" :name="'dari'" :required="true"></x-input>
<x-input :value="$ordertrucking->tujuan??old('tujuan')" :col="6" :label="'Tujuan'" :type="'text'" :name="'tujuan'" :required="true"></x-input>
<x-input :value="$ordertrucking->type??old('type')" :col="6" :label="'Type'" :type="'text'" :name="'type'" :required="true"></x-input>
<x-input :value="$ordertrucking->sangu??old('sangu')" :col="6" :label="'Sangu'" :type="'text'" :name="'sangu'" :required="true"></x-input>
<x-input :value="$ordertrucking->simpanan??old('simpanan')" :col="6" :label="'Simpanan'" :type="'text'" :name="'simpanan'" :required="true"></x-input>
<x-input :value="$ordertrucking->tagihan??old('tagihan')" :col="6" :label="'Tagihan'" :type="'string'" :name="'tagihan'" :required="true"></x-input>
<x-input :value="$ordertrucking->kuli??old('kuli')" :col="6" :label="'Kuli'" :type="'text'" :name="'kuli'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($ordertrucking)?'Tambah':'Update' }} Data</button>
</div>
</div>