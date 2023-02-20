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
            <li class="collapsed-nav-item-title d-none">Customer</li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('customer.index') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Customer</span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">Jadwal Kapal</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Jadwal Kapal <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">Suplier</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Suplier <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">User</li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('user.index') }}" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">User</span></div>
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
            <li class="collapsed-nav-item-title d-none">Order</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Order <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">Invoice</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Invoice <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">BA Kembali</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">BA Kembali <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">Kirim Invoice</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Kirim Invoice <i class="fas fa-lock"></i></span></div>
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
            <li class="collapsed-nav-item-title d-none">Kas / Bank</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Kas / Bank <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">Laba / Rugi</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Laba / Rugi <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">COA</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">COA <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">Hutang</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Hutang <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">Piutang</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Piutang <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">Neraca</li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-expanded="false">
                <div class="d-flex align-items-center"><span class="nav-link-text">Neraca <i class="fas fa-lock"></i></span></div>
                </a>
            </li>
            <li class="collapsed-nav-item-title d-none">Jurnal</li>
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
<div class="nav-item-wrapper">
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
</div>
