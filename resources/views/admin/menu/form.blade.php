<div class="row">
<x-input :value="$menu->title??old('title')" :col="6" :label="'Title'" :type="'text'" :name="'title'" :required="true"></x-input>
<x-input :value="$menu->icon??old('icon')" :col="6" :label="'Icon'" :type="'text'" :name="'icon'" :required="true"></x-input>
<x-input :value="$menu->name??old('name')" :col="6" :label="'Name'" :type="'text'" :name="'name'" :required="true"></x-input>
<x-input :value="$menu->url??old('url')" :col="6" :label="'Url'" :type="'text'" :name="'url'" :required="true"></x-input>
<div class="col-12 mb-2 px-1">
    <button type="submit" class="btn btn-success btn-sm">{{ empty($menu)?'Tambah':'Update' }} Data</button>
</div>
</div>