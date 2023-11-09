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
                <a href="{{ route('jurnal.kolektif.create') }}" class="py-2 px-3 btn-sm btn btn-primary">Tambah Jurnal Group JOB</a>
                <a href="{{ route('jurnal.balik.create') }}" class="py-2 px-3 btn-sm btn btn-warning">Tambah Jurnal Balik</a>
                <a href="{{ route('jurnal.manual') }}" class="py-2 px-3 btn-sm btn btn-light border-dark border">Jurnal Manual</a>
                <a href="{{ route('jurnal.merge') }}" class="py-2 px-3 btn-sm btn btn-secondary">Merge Jurnal</a>
                <a href="{{ route('jurnal.tampungan') }}" class="py-2 px-3 btn-sm btn btn-secondary">Jurnal Tampungan</a>
                <a href="{{ route('jurnal.totalan_sopir') }}" class="py-2 px-3 btn-sm btn btn-secondary">Jurnal Totalan Sopir</a>
                @if (Auth::user()->role_id==1)
                    <form action="{{ route('jurnal.import') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" id="file" onchange="submit()">
                    </form>
                    @endif
                <form action="{{ route('jurnal.exportMonth') }}" method="post">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button class="btn btn-sm btn-success" type="submit"><i class="fas fa-download"></i></button>
                </form>
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
                <livewire:list-jurnal :month="request('month')" :tipe="request('tipe')" :date="request('date')"/>
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
