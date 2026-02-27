@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <style>
        .table-responsie table{
            position: relative;
            overflow-y: scroll;
        }
        .table-responsive th{
            background-color: white !important;
            position: sticky !important;
            top: 0;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex flex-wrap" style="gap:10px">
                <a href="{{ route('jurnal.create') }}" class="py-2 px-3 btn-sm btn btn-success">Tambah Jurnal Ekspedisi</a>
                <a href="{{ route('jurnal.trucking') }}" class="py-2 px-3 btn-sm btn btn-info">Tambah Jurnal Trucking</a>
                <a href="{{ route('jurnal.trucking.bupot') }}" class="py-2 px-3 btn-sm btn btn-info">Tambah Jurnal Bupot Trucking</a>
                <a href="{{ route('jurnal.kolektif.create') }}" class="py-2 px-3 btn-sm btn btn-warning">Tambah Jurnal Group JOB</a>
                <a href="{{ route('jurnal.balik.create') }}" class="py-2 px-3 btn-sm btn btn-warning">Tambah Jurnal Balik</a>
                <a href="{{ route('jurnal.manual') }}" class="py-2 px-3 btn-sm btn btn-light border-dark border">Jurnal Manual</a>
                <a href="{{ route('jurnal.merge') }}" class="py-2 px-3 btn-sm btn btn-secondary">Merge Jurnal</a>
                <a href="{{ route('jurnal.tampungan') }}" class="py-2 px-3 btn-sm btn btn-secondary">Jurnal Tampungan</a>
                <a href="{{ route('jurnal.totalan_sopir') }}" class="py-2 px-3 btn-sm btn btn-secondary">Jurnal Totalan Sopir</a>
                <a href="{{ route('kunci.jurnal') }}" class="py-2 px-3 btn-sm btn btn-danger">Kunci Jurnal</a>
                 <a href="{{ route('monitoring-subjek-bb') }}" class="py-2 px-3 btn-sm btn btn-secondary">Monitoring BB Pembantu</a>
                @if (Auth::user()->role_id==1)
                    <form action="{{ route('jurnal.import') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" id="file" onchange="submit()">
                    </form>
                @endif
                {{-- <form action="{{ route('jurnal.sync.job') }}" method="post">
                    @csrf
                    <button class="btn btn-sm btn-info" type="submit">Sinkronisasi</button>
                </form>
                <button class="py-2 px-3 btn btn-sm btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-export"><i class="fas fa-download"></i></button> --}}
            </div>
            <div class="card-body">
                @if (count($unbalance)>0)
                <div class="card p-3 shadow my-2">
                    <b>Jurnal tidak balance</b>
                    <hr>
                    <table class="tables w-100 table-bordered" style="font-size: .7rem; padding:5px">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unbalance as $item)
                                <tr>
                                    <td>{{ $item->nomor }}</td>
                                    <td>{{ number_format($item->debit,2,',','.') }}</td>
                                    <td>{{ number_format($item->credit,2,',','.') }}</td>
                                    <td><a href="{{ route('jurnal.edit',['jurnal'=>$item->nomor]) }}" class="btn btn-sm px-3 py-1 btn-primary">Edit</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                <livewire:list-jurnal :month="request('month')" :tipe="request('tipe')" :date="request('date')" :is_sample="$is_sample"/>
                {{-- <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Kode</th>
                                <th>Account</th>
                                <th>Job</th>
                                <th>Keterangan</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div> --}}
            </div>
        </div>
    </div>


    {{-- <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasJurnal" aria-labelledby="offcanvasJurnalLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasJurnalLabel">Form Jurnal</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('jurnal.store') }}" method="post">
                @csrf
                @include('admin.jurnal.form')
            </form>
        </div>
    </div> --}}

    <!-- Modal Export-->
<div class="modal fade" id="modal-export" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{ route('jurnal.exportMonth') }}" method="POST" class="modal-dialog">
        @csrf
        <input type="hidden" name="is_sample" value="{{ $is_sample }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-2 col-12">
                        <label for="sample_false">
                            <input type="radio" name="is_sample" id="sample_false" value="real" {{ $is_sample=='real' ? 'checked' : '' }}> Jurnal Real
                        </label>
                        <label for="sample_true">
                            <input type="radio" name="is_sample" id="sample_true" value="sample" {{ $is_sample=='sample' ? 'checked' : '' }}> Jurnal Sample
                        </label>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="tipe" class="form-label">Tipe Jurnal</label>
                        <select name="tipe" id="tipe" class="form-select">
                            <option value="JNL" selected>JNL</option>
                            <option value="BBK">BBK</option>
                            <option value="BKK">BKK</option>
                            <option value="BKM">BKM</option>
                            <option value="BBM">BBM</option>
                            <option value="BBKT">BBKT</option>
                            <option value="BBMT">BBMT</option>
                        </select>
                    </div>
                    <div class="mb-2 col-6">
                        <label for="month" class="form-label">Bulan <small>(khusus tipe JNL)</small></label>
                        <select name="month" id="month" class="form-select">
                            <option value="" selected></option>
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="mb-2 col-6">
                        <label for="year" class="form-label">Tahun</label>
                        <select name="year" id="year" class="form-select">
                            <option value="2022" {{ date('Y')=='2022' ? 'selected' : '' }}>2022</option>
                            <option value="2023" {{ date('Y')=='2023' ? 'selected' : '' }}>2023</option>
                            <option value="2024" {{ date('Y')=='2024' ? 'selected' : '' }}>2024</option>
                            <option value="2025" {{ date('Y')=='2025' ? 'selected' : '' }}>2025</option>
                            <option value="2026" {{ date('Y')=='2026' ? 'selected' : '' }}>2026</option>
                            <option value="2027" {{ date('Y')=='2027' ? 'selected' : '' }}>2027</option>
                            <option value="2028" {{ date('Y')=='2028' ? 'selected' : '' }}>2028</option>
                            <option value="2029" {{ date('Y')=='2029' ? 'selected' : '' }}>2029</option>
                            <option value="2030" {{ date('Y')=='2030' ? 'selected' : '' }}>2030</option>
                        </select>
                    </div>
                    <div class="mb-2 col-6">
                        <label for="from" class="form-label">Dari Nomor</label>
                        <input type="number" name="from" id="from" class="form-control" min="1" value="1" onclick="this.select()">
                    </div>
                    <div class="mb-2 col-6">
                        <label for="to" class="form-label">Sampai Nomor</label>
                        <input type="number" name="to" id="to" class="form-control" min="1" value="100" onclick="this.select()">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Export Excel</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    {{-- <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ordering:false,
            ajax:{
                url: '{{ route('jurnal.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'nomor', name: 'nomor' },
                { data: 'code', name: 'code' },
                { data: 'coa_id', name: 'coa_id' },
                { data: 'order_id', name: 'order_id' },
                { data: 'nama', name: 'nama' },
                { data: 'debit', name: 'debit' },
                { data: 'credit', name: 'credit' },
                // { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script> --}}
@endsection
