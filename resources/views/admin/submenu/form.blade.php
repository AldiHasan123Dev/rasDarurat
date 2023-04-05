@php
    $menus = App\Models\Menu::pluck('title','id')
@endphp

<div class="row">
    <x-input :value="$submenu->menu_id??old('menu_id')" :col="6" :label="'Menu'" :type="'select'" :options="$menus" :name="'menu_id'" :required="true"></x-input>
    <x-input :value="$submenu->title??old('title')" :col="6" :label="'Title'" :type="'text'" :name="'title'" :required="true"></x-input>
    <x-input :value="$submenu->icon??old('icon')" :col="6" :label="'Icon'" :type="'text'" :name="'icon'" :required="true"></x-input>
    <x-input :value="$submenu->name??old('name')" :col="6" :label="'Name'" :type="'text'" :name="'name'" :required="true"></x-input>
    <x-input :value="$submenu->url??old('url')" :col="6" :label="'Url'" :type="'text'" :name="'url'" :required="true"></x-input>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">{{ empty($submenu)?'Tambah':'Update' }} Data</button>
    </div>
</div>
