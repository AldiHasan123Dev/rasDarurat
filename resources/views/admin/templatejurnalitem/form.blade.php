<div class="row">
<x-select :options="[]" :value="$templatejurnalitem->template_jurnal_id??old('template_jurnal_id')" :col="6" :label="'Template_jurnal_id'" :type="'select'" :name="'template_jurnal_id'" :required="true"></x-select>
<x-select :options="[]" :value="$templatejurnalitem->coa_id??old('coa_id')" :col="6" :label="'Coa_id'" :type="'select'" :name="'coa_id'" :required="true"></x-select>
<x-select :options="[]" :value="$templatejurnalitem->tipe??old('tipe')" :col="6" :label="'Tipe'" :type="'select'" :name="'tipe'" :required="true"></x-select>
<x-input :value="$templatejurnalitem->no??old('no')" :col="6" :label="'No'" :type="'text'" :name="'no'" :required="true"></x-input>
<x-input :value="$templatejurnalitem->deskripsi??old('deskripsi')" :col="6" :label="'Deskripsi'" :type="'text'" :name="'deskripsi'" :required="true"></x-input>
<x-input :value="$templatejurnalitem->jumlah??old('jumlah')" :col="6" :label="'Jumlah'" :type="'number'" :name="'jumlah'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($templatejurnalitem)?'Tambah':'Update' }} Data</button>
</div>
</div>