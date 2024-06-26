@php
    $role = Auth::user()->role_id;
    $access = App\Models\RoleAccess::join('sub_menu','sub_menu.id','=','role_access.sub_menu_id')
                ->join('menu','menu.id','=','sub_menu.menu_id')
                ->where('role_access.role_id',$role)
                ->select('menu.title as label','menu.name','menu.icon','menu.id','sub_menu.url','sub_menu.title','role_access.role_id','role_access.sub_menu_id','sub_menu.order')
                ->orderBy('menu.order','asc')
                ->orderBy('sub_menu.order')
                ->get()
                ->groupBy('id');
    // dd($access);
@endphp

<div class="nav-item-wrapper my-2">
    <a class="nav-link label-1" href="/home" role="button" aria-expanded="false">
        <div class="d-flex align-items-center">
            <span class="nav-link-icon"><span class="fas fa-home"></span></span>
            <span class="nav-link-text-wrapper"><span class="nav-link-text">Dashboard</span></span>
        </div>
    </a>
</div>

@foreach ($access as $item)
<div class="nav-item-wrapper">
    <a class="nav-link dropdown-indicator label-1 collapsed" href="#{{ $item->first()->name }}" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="home">
        <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span class="{{ $item->first()->icon }}"></span></span><span class="nav-link-text mr-2">{{ $item->first()->label }}</span><div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div>
        </div>
    </a>
    <div class="parent-wrapper label-1">
        <ul class="nav collapse parent {{ $item->where('url',request()->url())->first() ? 'show' : '' }}" id="{{ $item->first()->name }}">
            @foreach ($item as $menu)
                <li class="nav-item">
                    <a class="nav-link {{ request()->url()==$menu->url ? 'active' : '' }}" href="{{ $menu->url }}" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-text">{{ $menu->title }}</span></div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endforeach
{{-- <div class="nav-item-wrapper">
    <a class="nav-link dropdown-indicator label-1" href="#ccetak" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="ccetak">
        <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span class="fas fa-print"></span></span><span class="nav-link-text mr-2">Cetak</span><div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div>
        </div>
    </a>
    <div class="parent-wrapper label-1">
        <ul class="nav collapse parent" id="ccetak">
            <li class="collapsed-nav-item-title d-none">Surat Jalan</li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('cetak.suratJalan') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Surat Jalan </span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">PO</li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('cetak.pickOrder') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">PO</span></div>
                </a>
            </li>
        </ul>
    </div>
</div> --}}
