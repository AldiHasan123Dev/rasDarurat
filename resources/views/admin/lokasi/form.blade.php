<div class="row">
<x-input :value="$lokasi->nama??old('nama')" :col="6" :label="'Nama Lokasi'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$lokasi->rate??0" :col="6" :label="'Publis Rate'" :id="'rate_c'" :type="'number'" :name="'rate'" :required="true"></x-input>
<x-input :value="$lokasi->discount??0" :col="6" :label="'Diskon'" :id="'discount_c'" :type="'number'" :name="'discount'" :required="true"></x-input>
<x-input :value="$lokasi->harga_net??0" :readonly="true" :col="6" :id="'harga_net_c'" :label="'Harga Net'" :type="'number'" :name="'harga_net'" :required="true"></x-input>
<x-input :value="$lokasi->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($lokasi)?'Tambah':'Update' }} Data</button>
</div>
</div>
