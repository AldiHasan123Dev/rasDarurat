@php
    $accounts = App\Models\Account::where('is_active',1)->pluck('nama','id');
@endphp

<div class="row">
<x-input :options="$accounts" :value="$subaccount->account_id??old('account_id')" :col="6" :label="'Account'" :type="'select'" :name="'account_id'" :required="true"></x-input>
<x-input :value="$subaccount->kode??old('kode')" :col="6" :label="'Kode'" :type="'text'" :name="'kode'" :required="true"></x-input>
<x-input :value="$subaccount->nama??old('nama')" :col="6" :label="'Nama'" :type="'text'" :name="'nama'" :required="true"></x-input>
<x-input :value="$subaccount->keterangan??old('keterangan')" :col="6" :label="'Keterangan'" :type="'text'" :name="'keterangan'" :required="true"></x-input>
<x-input :value="$account->is_active??old('is_active')" :col="6" :label="'Status'" :type="'select'" :options="[0=>'Non Aktif',1=>'Aktif']" :name="'is_active'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($subaccount)?'Tambah':'Update' }} Data</button>
</div>
</div>
