<div class="row">
<x-input :value="$transaksi->pembayar_id??old('pembayar_id')" :col="6" :label="'Pembayar_id'" :type="'number'" :name="'pembayar_id'" :required="true"></x-input>
<x-input :value="$transaksi->invoice??old('invoice')" :col="6" :label="'Invoice'" :type="'text'" :name="'invoice'" :required="true"></x-input>
<x-input :value="$transaksi->nsfp??old('nsfp')" :col="6" :label="'Nsfp'" :type="'text'" :name="'nsfp'" :required="true"></x-input>
<x-input :value="$transaksi->keterangan??old('keterangan')" :col="6" :label="'Keterangan'" :type="'text'" :name="'keterangan'" :required="true"></x-input>
<x-input :value="$transaksi->tujuan??old('tujuan')" :col="6" :label="'Tujuan'" :type="'text'" :name="'tujuan'" :required="true"></x-input>
<x-input :value="$transaksi->sub_total??old('sub_total')" :col="6" :label="'Sub_total'" :type="'number'" :name="'sub_total'" :required="true"></x-input>
<x-input :value="$transaksi->tagihan??old('tagihan')" :col="6" :label="'Tagihan'" :type="'number'" :name="'tagihan'" :required="true"></x-input>
<x-input :value="$transaksi->ppn??old('ppn')" :col="6" :label="'Ppn'" :type="'number'" :name="'ppn'" :required="true"></x-input>
<x-input :value="$transaksi->asuransi??old('asuransi')" :col="6" :label="'Asuransi'" :type="'number'" :name="'asuransi'" :required="true"></x-input>
<x-input :value="$transaksi->admin??old('admin')" :col="6" :label="'Admin'" :type="'number'" :name="'admin'" :required="true"></x-input>
<x-input :value="$transaksi->total??old('total')" :col="6" :label="'Total'" :type="'number'" :name="'total'" :required="true"></x-input>
<x-input :value="$transaksi->pph??old('pph')" :col="6" :label="'Pph'" :type="'number'" :name="'pph'" :required="true"></x-input>
<x-input :value="$transaksi->job??old('job')" :col="6" :label="'Job'" :type="'text'" :name="'job'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($transaksi)?'Tambah':'Update' }} Data</button>
</div>
</div>