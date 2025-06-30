@extends('layouts.admin')
@section('style')
    <style>
        table{
            position: relative;
            overflow-y: scroll;
        }
        th{
            background-color: white !important;
            position: sticky !important;
            top: 0;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA" aria-controls="offcanvasCOA">Tambah COA</button>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="height: 600px">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead style="background-color: white">
                            <tr style="background-color: white">
                                <th>#</th>
                                <th>ID</th>
                                @if (!$is_ras)
                                    <th>ID RAS</th>
                                @endif
                                <th>LR</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>View Cont</th>
                                <th>View Nopol</th>
                                <th>View No JOB</th>
                                <th>View Invoice XPDC</th>
                                 <th>View Invoice Agen</th>
                                <th>View Invoice Truck</th>
                                <th>View Invoice Vendor Truck</th>
                                 <th>View Invoice External</th>
                                <th>View No BG</th>
                                <th>View No Bupot</th>
                                <th>View Tgl Bupot</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_active')" {{ $item->is_active==1?'checked':'' }}></td>
                                    <td>{{ $item->id }}</td>
                                    @if (!$is_ras)
                                        <td>
                                            <input type="text" value="{{ $item->coa_ras }}" pattern="\d{1,5}" onkeyup="updateRas(this,{{ $item->id }})" style="width: 50px">
                                        </td>
                                    @endif
                                    <td>{{ $item->kategori }}</td>
                                    <td>{{ $item->kode }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_cont')" {{ $item->is_cont==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_nopol')" {{ $item->is_nopol==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_nojob')" {{ $item->is_nojob==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_invoice')" {{ $item->is_invoice==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_invoice_agen')" {{ $item->is_invoice_agen==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_invoice_trucking')" {{ $item->is_invoice_trucking==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_invoice_vendor')" {{ $item->is_invoice_vendor==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_invoice_external')" {{ $item->is_invoice_external==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_nobg')" {{ $item->is_nobg==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_nobupot')" {{ $item->is_nobupot==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_tglbupot')" {{ $item->is_tglbupot==1?'checked':'' }}></td>
                                    <td>
                                        <button class="py-0 px-0 btn text-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA-{{ $item->id }}" aria-controls="offcanvasCOA-{{ $item->id }}"><i class="fas fa-pencil"></i></button>
                                        <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCOA-{{ $item->id }}" aria-labelledby="offcanvasCOA-{{ $item->id }}Label">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="offcanvasCOA-{{ $item->id }}Label">Form Update COA</h5>
                                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                            </div>
                                            <div class="offcanvas-body">
                                                <form action="{{ route('coa.update',$item) }}" method="post">
                                                    @csrf
                                                    @method('PUT')
                                                    @include('admin.coa.form',['coa'=>$item])
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @if ($item->coas->count()>0)
                                    @foreach ($item->coas as $a)
                                    <tr>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_active')" {{ $a->is_active==1?'checked':'' }}></td>
                                        <td>{{ $a->id }}</td>
                                        @if (!$is_ras)
                                            <td>
                                                <input type="text" value="{{ $a->coa_ras }}" pattern="\d{1,5}" onkeyup="updateRas(this,{{ $a->id }})" style="width: 50px">
                                            </td>
                                        @endif
                                        <td>{{ $a->kategori }}</td>
                                        <td>{{ $a->kode }}</td>
                                        <td>{{ $a->nama }}</td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_cont')" {{ $a->is_cont==1?'checked':'' }}></td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_nopol')" {{ $a->is_nopol==1?'checked':'' }}></td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_nojob')" {{ $a->is_nojob==1?'checked':'' }}></td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_invoice')" {{ $a->is_invoice==1?'checked':'' }}></td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_invoice_agen')" {{ $a->is_invoice_agen==1?'checked':'' }}></td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_invoice_trucking')" {{ $a->is_invoice_trucking==1?'checked':'' }}></td>
                                         <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_invoice_vendor')" {{ $a->is_invoice_vendor==1?'checked':'' }}></td>
                                          <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_invoice_external')" {{ $a->is_invoice_external==1?'checked':'' }}></td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_nobg')" {{ $a->is_nobg==1?'checked':'' }}></td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_nobupot')" {{ $a->is_nobupot==1?'checked':'' }}></td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_tglbupot')" {{ $a->is_tglbupot==1?'checked':'' }}></td>
                                        <td>
                                            <button class="py-0 px-0 btn text-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA-{{ $a->id }}" aria-controls="offcanvasCOA-{{ $a->id }}"><i class="fas fa-pencil"></i></button>
                                            <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCOA-{{ $a->id }}" aria-labelledby="offcanvasCOA-{{ $a->id }}Label">
                                                <div class="offcanvas-header">
                                                    <h5 class="offcanvas-title" id="offcanvasCOA-{{ $a->id }}Label">Form Update COA</h5>
                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                </div>
                                                <div class="offcanvas-body">
                                                    <form action="{{ route('coa.update',$a) }}" method="post">
                                                        @csrf
                                                        @method('PUT')
                                                        @include('admin.coa.form',['coa'=>$a])
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                        @if ($a->coas->count()>0)
                                            @foreach ($a->coas as $b)
                                            <tr>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_active')" {{ $b->is_active==1?'checked':'' }}></td>
                                                <td>{{ $b->id }}</td>
                                                @if (!$is_ras)
                                                    <td>
                                                        <input type="text" value="{{ $b->coa_ras }}" pattern="\d{1,5}" onkeyup="updateRas(this,{{ $b->id }})" style="width: 50px">
                                                    </td>
                                                @endif
                                                <td>{{ $b->kategori }}</td>
                                                <td>{{ $b->kode }}</td>
                                                <td>{{ $b->nama }}</td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_cont')" {{ $b->is_cont==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_nopol')" {{ $b->is_nopol==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_nojob')" {{ $b->is_nojob==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_invoice')" {{ $b->is_invoice==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_invoice_agen')" {{ $b->is_invoice_agen==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_invoice_trucking')" {{ $b->is_invoice_trucking==1?'checked':'' }}></td>
                                                 <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_invoice_vendor')" {{ $b->is_invoice_vendor==1?'checked':'' }}></td>
                                                  <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_invoice_external')" {{ $b->is_invoice_external==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_nobg')" {{ $b->is_nobg==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_nobupot')" {{ $b->is_nobupot==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_tglbupot')" {{ $b->is_tglbupot==1?'checked':'' }}></td>
                                                <td>
                                                    <button class="py-0 px-0 btn text-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA-{{ $b->id }}" aria-controls="offcanvasCOA-{{ $b->id }}"><i class="fas fa-pencil"></i></button>
                                                    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCOA-{{ $b->id }}" aria-labelledby="offcanvasCOA-{{ $b->id }}Label">
                                                        <div class="offcanvas-header">
                                                            <h5 class="offcanvas-title" id="offcanvasCOA-{{ $b->id }}Label">Form Update COA</h5>
                                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                        </div>
                                                        <div class="offcanvas-body">
                                                            <form action="{{ route('coa.update',$b) }}" method="post">
                                                                @csrf
                                                                @method('PUT')
                                                                @include('admin.coa.form',['coa'=>$b])
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if ($b->coas->count()>0)
                                                @foreach ($b->coas as $c)
                                                    <tr>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_active')" {{ $c->is_active==1?'checked':'' }}></td>
                                                        <td>{{ $c->id }}</td>
                                                        @if (!$is_ras)
                                                            <td>
                                                                <input type="text" value="{{ $c->coa_ras }}" pattern="\d{1,5}" onkeyup="updateRas(this,{{ $c->id }})" style="width: 50px">
                                                            </td>
                                                        @endif
                                                        <td>{{ $c->kategori }}</td>
                                                        <td>{{ $c->kode }}</td>
                                                        <td>{{ $c->nama }}</td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_cont')" {{ $c->is_cont==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_nopol')" {{ $c->is_nopol==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_nojob')" {{ $c->is_nojob==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_invoice')" {{ $c->is_invoice==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_invoice_agen')" {{ $c->is_invoice_agen==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_invoice_trucking')" {{ $c->is_invoice_trucking==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_invoice_vendor')" {{ $c->is_invoice_vendor==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_invoice_external')" {{ $c->is_invoice_external==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_nobg')" {{ $c->is_nobg==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_nobupot')" {{ $c->is_nobupot==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_tglbupot')" {{ $c->is_tglbupot==1?'checked':'' }}></td>
                                                        <td>
                                                            <button class="py-0 px-0 btn text-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA-{{ $c->id }}" aria-controls="offcanvasCOA-{{ $c->id }}"><i class="fas fa-pencil"></i></button>
                                                            <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCOA-{{ $c->id }}" aria-labelledby="offcanvasCOA-{{ $c->id }}Label">
                                                                <div class="offcanvas-header">
                                                                    <h5 class="offcanvas-title" id="offcanvasCOA-{{ $c->id }}Label">Form Update COA</h5>
                                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                                </div>
                                                                <div class="offcanvas-body">
                                                                    <form action="{{ route('coa.update',$c) }}" method="post">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        @include('admin.coa.form',['coa'=>$c])
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @if ($c->coas->count()>0)
                                                        @foreach ($c->coas as $d)
                                                            <tr>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_active')" {{ $d->is_active==1?'checked':'' }}></td>
                                                                <td>{{ $d->id }}</td>
                                                                @if (!$is_ras)
                                                                    <td>
                                                                        <input type="text" value="{{ $d->coa_ras }}" pattern="\d{1,5}" onkeyup="updateRas(this,{{ $d->id }})" style="width: 50px">
                                                                    </td>
                                                                @endif
                                                                <td>{{ $d->kategori }}</td>
                                                                <td>{{ $d->kode }}</td>
                                                                <td>{{ $d->nama }}</td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_cont')" {{ $d->is_cont==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_nopol')" {{ $d->is_nopol==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_nojob')" {{ $d->is_nojob==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_invoice')" {{ $d->is_invoice==1?'checked':'' }}></td>
                                                                 <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_invoice_agen')" {{ $d->is_invoice_agen==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_invoice_trucking')" {{ $d->is_invoice_trucking==1?'checked':'' }}></td>
                                                                 <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_invoice_vendor')" {{ $d->is_invoice_vendor==1?'checked':'' }}></td>
                                                                  <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_invoice_external')" {{ $d->is_invoice_external==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_nobg')" {{ $d->is_nobg==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_nobupot')" {{ $d->is_nobupot==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_tglbupot')" {{ $d->is_tglbupot==1?'checked':'' }}></td>
                                                                <td>
                                                                    <button class="py-0 px-0 btn text-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA-{{ $d->id }}" aria-controls="offcanvasCOA-{{ $d->id }}"><i class="fas fa-pencil"></i></button>
                                                                    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCOA-{{ $d->id }}" aria-labelledby="offcanvasCOA-{{ $d->id }}Label">
                                                                        <div class="offcanvas-header">
                                                                            <h5 class="offcanvas-title" id="offcanvasCOA-{{ $d->id }}Label">Form Update COA</h5>
                                                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="offcanvas-body">
                                                                            <form action="{{ route('coa.update',$d) }}" method="post">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                @include('admin.coa.form',['coa'=>$d])
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @if ($d->coas->count()>0)
                                                                @foreach ($d->coas as $e)
                                                                    <tr>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_active')" {{ $e->is_active==1?'checked':'' }}></td>
                                                                        <td>{{ $e->id }}</td>
                                                                        <td>{{ $e->kategori }}</td>
                                                                        <td>{{ $e->kode }}</td>
                                                                        <td>{{ $e->nama }}</td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_cont')" {{ $e->is_cont==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_nopol')" {{ $e->is_nopol==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_nojob')" {{ $e->is_nojob==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_invoice')" {{ $e->is_invoice==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_invoice_agen')" {{ $e->is_invoice_agen==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_invoice_trucking')" {{ $e->is_invoice_trucking==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_invoice_vendor')" {{ $e->is_invoice_vendor==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_invoice_external')" {{ $e->is_invoice_external==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_nobg')" {{ $e->is_nobg==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_nobupot')" {{ $e->is_nobupot==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_tglbupot')" {{ $e->is_tglbupot==1?'checked':'' }}></td>
                                                                        <td>
                                                                            <button class="py-0 px-0 btn text-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA-{{ $e->id }}" aria-controls="offcanvasCOA-{{ $e->id }}"><i class="fas fa-pencil"></i></button>
                                                                            <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCOA-{{ $e->id }}" aria-labelledby="offcanvasCOA-{{ $e->id }}Label">
                                                                                <div class="offcanvas-header">
                                                                                    <h5 class="offcanvas-title" id="offcanvasCOA-{{ $e->id }}Label">Form Update COA</h5>
                                                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="offcanvas-body">
                                                                                    <form action="{{ route('coa.update',$e) }}" method="post">
                                                                                        @csrf
                                                                                        @method('PUT')
                                                                                        @include('admin.coa.form',['coa'=>$e])
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                        @if ($e->coas->count()>0)
                                                                            @foreach ($e->coas as $f)
                                                                            <tr>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_active')" {{ $f->is_active==1?'checked':'' }}></td>
                                                                                <td>{{ $f->id }}</td>
                                                                                <td>{{ $f->kategori }}</td>
                                                                                <td>{{ $f->kode }}</td>
                                                                                <td>{{ $f->nama }}</td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_cont')" {{ $f->is_cont==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_nopol')" {{ $f->is_nopol==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_nojob')" {{ $f->is_nojob==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_invoice')" {{ $f->is_invoice==1?'checked':'' }}></td>
                                                                                 <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_invoice_agen')" {{ $f->is_invoice_agen==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_invoice_trucking')" {{ $f->is_invoice_trucking==1?'checked':'' }}></td>
                                                                                 <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_invoice_vendor')" {{ $f->is_invoice_vendor==1?'checked':'' }}></td>
                                                                                  <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_invoice_external')" {{ $f->is_invoice_external==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_nobg')" {{ $f->is_nobg==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_nobupot')" {{ $f->is_nobupot==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_tglbupot')" {{ $f->is_tglbupot==1?'checked':'' }}></td>
                                                                                <td>
                                                                                    <button class="py-0 px-0 btn text-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA-{{ $f->id }}" aria-controls="offcanvasCOA-{{ $f->id }}"><i class="fas fa-pencil"></i></button>
                                                                                    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCOA-{{ $f->id }}" aria-labelledby="offcanvasCOA-{{ $f->id }}Label">
                                                                                        <div class="offcanvas-header">
                                                                                            <h5 class="offcanvas-title" id="offcanvasCOA-{{ $f->id }}Label">Form Update COA</h5>
                                                                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="offcanvas-body">
                                                                                            <form action="{{ route('coa.update',$f) }}" method="post">
                                                                                                @csrf
                                                                                                @method('PUT')
                                                                                                @include('admin.coa.form',['coa'=>$f])
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            @endforeach
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCOA" aria-labelledby="offcanvasCOALabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasCOALabel">Form COA</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('coa.store') }}" method="post">
                @csrf
                @include('admin.coa.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    {{-- <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('coa.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
            { data: 'coa_id', name: 'coa_id' },
            { data: 'kode', name: 'kode' },
            { data: 'nama', name: 'nama' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script> --}}
    <script>
        function updateActive(e,id,tipe){
            let data = {update_status:1};
            if(tipe=='is_active'){
                data.is_active = 1;
            }
            if(tipe=='is_cont'){
                data.is_cont = 1;
            }
            if(tipe=='is_nopol'){
                data.is_nopol = 1;
            }
            if(tipe=='is_nojob'){
                data.is_nojob = 1;
            }
            if(tipe=='is_invoice'){
                data.is_invoice = 1;
            }
            if(tipe=='is_invoice_agen'){
                data.is_invoice_agen = 1;
            }
             if(tipe=='is_invoice_trucking'){
                data.is_invoice_trucking = 1;
            }
            if(tipe=='is_invoice_vendor'){
                data.is_invoice_vendor = 1;
            }
            if(tipe=='is_invoice_external'){
                data.is_invoice_external = 1;
            }
            if(tipe=='is_nobg'){
                data.is_nobg = 1;
            }
            if(tipe=='is_nobupot'){
                data.is_nobupot = 1;
            }
            if(tipe=='is_tglbupot'){
                data.is_tglbupot = 1;
            }
            data.response = true;
            $.ajax({
                type: "PUT",
                url: "{{ url('admin/coa') }}"+"/"+id,
                data: data,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    alert('Data Berhasil disimpan!');
                }
            });
        }

        function isNumber(value) {
            return typeof value == 'number';
        }

        let time;
        function updateRas(e,id){
            let value = e.value;
            value = parseInt(value);
            if(isNumber(value)){
                clearTimeout(time);
                time = setTimeout(() => {
                    let data = {coa_ras:value};
                    $.ajax({
                        type: "PUT",
                        url: "{{ url('admin/coa') }}"+"/"+id,
                        data: data,
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function (response) {
                            alert('Data Berhasil disimpan!');
                        }
                    });
                }, 2000);
            }else{
                alert('Harap input angka');
            }
        }
    </script>
@endsection
