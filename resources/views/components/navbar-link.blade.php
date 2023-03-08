<div class="nav-item-wrapper my-2">
    <a use:link class="nav-link label-1" href="#" role="button" aria-expanded="false">
        <div class="d-flex align-items-center">
            <span class="nav-link-icon"><span class="fas fa-home"></span></span>
            <span class="nav-link-text-wrapper"><span class="nav-link-text">Dashboard</span></span>
        </div>
    </a>
</div>
<div class="nav-item-wrapper">
    <a class="nav-link dropdown-indicator label-1" href="#home" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="home">
        <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span class="fas fa-database"></span></span><span class="nav-link-text mr-2">Master</span><div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div>
        </div>
    </a>
    <div class="parent-wrapper label-1">
        <ul class="nav collapse parent show" id="home">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('customer.index') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Customer</span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('jadwalkapal.index') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Jadwal Kapal</span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('pelayaran.index') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Suplier</span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('user.index') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">User</span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('kapal.index') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Data</span></div>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="nav-item-wrapper">
    <a class="nav-link dropdown-indicator label-1" href="#ekspedisi" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="ekspedisi">
        <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span class="fas fa-train"></span></span><span class="nav-link-text mr-2">Ekspedisi</span><div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div>
        </div>
    </a>
    <div class="parent-wrapper label-1">
        <ul class="nav collapse parent" id="ekspedisi">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('cetak.suratJalan') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Surat Jalan </span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('cetak.pickOrder') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">PO</span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('order.index') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Order</span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('order.index',['filter-order'=>'ba_kembali']) }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">BA Kembali</span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('order.index',['filter-order'=>'invoice']) }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Invoice</span></div>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="nav-item-wrapper">
    <a class="nav-link dropdown-indicator label-1" href="#keuangan" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="keuangan">
        <div class="d-flex align-items-center">
        <span class="nav-link-icon"><span class="fas fa-dollar"></span></span><span class="nav-link-text mr-2">Keuangan</span><div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div>
        </div>
    </a>
    <div class="parent-wrapper label-1">
        <ul class="nav collapse parent" id="keuangan">
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Kas / Bank <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Laba / Rugi <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">COA <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Hutang <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Piutang <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Neraca <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Jurnal <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="nav-item-wrapper my-2">
    <a use:link class="nav-link label-1" href="#" role="button" aria-expanded="false">
        <div class="d-flex align-items-center">
            <span class="nav-link-icon"><span class="fas fa-list"></span></span>
            <span class="nav-link-text-wrapper"><span class="nav-link-text">Laporan</span></span>
        </div>
    </a>
</div>
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
