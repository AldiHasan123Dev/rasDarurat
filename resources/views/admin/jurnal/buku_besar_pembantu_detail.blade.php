@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.dataTables.min.css">
    <style>
        .dataTables_filter {
            margin-bottom: 10px;
            /* Jarak bawah pada elemen searching */
        }

        .dataTables_info {
            margin-top: 10px;
            /* Jarak atas pada elemen info */
        }

        /* Tabel lebih rapi */
        .table.data th,
        .table.data td {
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #ddd;
        }

        .table.data th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
        }

        .table.data tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table.data tr:hover {
            background-color: #e6e6e6;
        }

        /* Elemen form lebih proporsional */
        input[type="text"],
        select {
            border: 1px solid #ddd;
            padding: 6px 10px;
            font-size: 0.6rem;
            border-radius: 4px;
        }
        th.text-center:nth-child(2), /* Tgl (D) */
        th.text-center:nth-child(4), /* Tgl (K) */
        td.text-center:nth-child(2), /* Tgl (D) */
        td.text-center:nth-child(4)  /* Tgl (K) */ {
            min-width: 70px; /* Sesuaikan ukuran sesuai kebutuhan */
        }
        th.text-center:nth-child(3), /* Nomor (D) */
        th.text-center:nth-child(5), /* Nomor (K) */
        td.text-center:nth-child(3), /* Nomor (D) */
        td.text-center:nth-child(5)  /* Nomor (K) */ {
            min-width: 90px; /* Atur ukuran sesuai kebutuhan */
        }



        /* Elemen untuk print */
        @media print {
            body * {
                visibility: hidden;
            }

            #print,
            #print * {
                visibility: visible;
                font-family: 'Dot Matrix', sans-serif;
            }
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div id="print">
                    <div class="row">
                        <div class="col-12 mt-3">
                            {{-- <div class="d-flex gap-2">
                            <b class="mt-2">Bulan: </b>
                            @foreach ($months as $idx => $item)
                                <a href="{{ route('jurnal.buku_besar',['month'=>sprintf('%02d',$idx+1),'coa_id'=>$coa_id]) }}" wire:click="changeMonth({{ $idx+1 }})" class="{{ $idx+1==(int)$month?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">{{ $item }}</a>
                            @endforeach
                        </div> --}}
                            {{-- <div class="my-3">
                            <label for="search">Search</label>
                            <input type="text" wire:model="search" class="form-control" placeholder="Cari berdasarkan nomor jurnal/keterangan/akun/job/tanggal">
                        </div> --}}
                            <div class="container">
                                <div class="card mt-4">
                                    <div class="card-header bg-primary text-white">
                                        <h3 class="mb-0 text-white">Rincian Buku Besar Pembantu</h3>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="mt-3 text-muted">Subjek: <span
                                                class="font-weight-bold">{{ $subjek }}</span></h5>
                                        <h5 class="mt-3">
                                            @if ($subjek === 'pelayaran')
                                                <span class="badge bg-info text-white">Pelayaran : {{ $customer }}</span>
                                            @elseif ($subjek == 'customer_trucking')
                                            <span class="badge bg-warning text-white">Customer Trucking :
                                                {{ $customer }}</span>
                                            @elseif ($subjek == 'agen')
                                            <span class="badge bg-danger text-white">Agen :
                                                    {{ $customer }}</span>
                                                    @elseif ($subjek == 'vendor')
                                            <span class="badge bg-primary text-white">Vendor Trucking :
                                                    {{ $customer }}</span>
                                                    @elseif ($subjek == 'lain-lain')
                                            <span class="badge bg-dark">Lain-lain :
                                                    {{ $customer }}</span>
                                            @else
                                                <span class="badge bg-success text-white">Customer :
                                                    {{ $customer }}</span>
                                            @endif
                                        </h5>

                                        <div class="table-responsive mt-3">
                                            <table class="table data table-sm table-bordered table-detail"
                                                style="font-size: .7rem">
                                                <thead>
                                                    <tr>
                                                        
                                                        <th class="text-center">Tgl (D)</th>
                                                        <th class="text-center">Nomor (D)</th>
                                                        <th class="text-center">Tgl (K)</th>
                                                        <th class="text-center">Nomor (K)</th>
                                                        <th class="text-center">
                                                            @if ($subjek == 'pelayaran')
                                                            No BG
                                                            @elseif ($subjek == 'customer_trucking')
                                                            Invoice Trucking
                                                            @elseif ($subjek == 'agen')
                                                            Agen
                                                            @elseif ($subjek == 'vendor')
                                                            Invoice Vendor
                                                            @elseif ($subjek == 'lain-lain')
                                                            Invoice External
                                                            @else
                                                            Invoice Xpdc
                                                            @endif
                                                        </th>
                                                        @if ($subjek == 'customer_xpdc' && $coa_id == 46 || $subjek == 'customer_trucking' && $coa_id == 47)
                                                        <th class="text-center">Pph</th>
                                                        @endif
                                                        <th class="text-center">Debit</th>
                                                        <th class="text-center">Credit</th>
                                                        <th class="text-center">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                    @endphp
                                                    @foreach ($groupedJurnal as $index => $j)
                                                        <tr>
                                                            <td class="text-center">{!! $j['tgl_d'] ?: '-' !!}</td>
                                                            <td class="text-center">
                                                                @if($j['nomor_d'])
                                                                   {!! $j['nomor_d'] ?: '-' !!}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            
                                                            <td class="text-center"> {!! $j['tgl_k'] ?: '-' !!}</td>
                                                            <td class="text-center"> {!! $j['nomor_k'] ?: '-' !!}</td>
                                                            <td class="text-center">
                                                                @if($subjek == 'pelayaran')
                                                                {{ $j['no_bg'] ?: '-' }}
                                                                @elseif ($subjek == 'customer_trucking')
                                                                {{ $j['invoice_trucking'] ?: '-' }}
                                                                @elseif ($subjek == 'agen')
                                                                {{ $j['invoice_agen'] ?: '-' }}
                                                                @elseif ($subjek == 'vendor')
                                                                {{ $j['invoice_vendor'] ?: '-' }}
                                                                @elseif ($subjek == 'lain-lain')
                                                                {{ $j['invoice_external'] ?: '-' }}
                                                                @else
                                                                {{ $j['invoice'] ?: '-' }}
                                                                @endif
                                                            </td>
                                                            @if (($subjek == 'customer_xpdc' && $coa_id == 46) || ($subjek == 'customer_trucking' && $coa_id == 47))
                                                            <td class="text-end">{{ $j['pph'] ? number_format($j['pph'], 2, ',', '.') : 0 }}</td>
                                                        @endif
                                                        
                                                    @php
                                                        $isRelevantSubjek = 
                                                            ($subjek == 'customer_xpdc' && $coa_id == 46) || 
                                                            ($subjek == 'customer_trucking' && $coa_id == 47) ||
                                                            ($subjek == 'agen' && $coa_id == 63) ||
                                                            ($subjek == 'vendor' && $coa_id == 131 ||
                                                            $subjek == 'lain-lain');

                                                        $selisih = abs($j['debit'] - $j['credit']) > 0;

                                                        // Aturan pembanding debit/credit berdasarkan coa_id
                                                        $validPerbandingan = false;
                                                        if ($coa_id == 63 || $coa_id == 131) {
                                                            $validPerbandingan = $j['credit'] > $j['debit'];
                                                        } else {
                                                            $validPerbandingan = $j['debit'] > $j['credit'];
                                                        }

                                                        $validPerbandingan1 = false;
                                                        if ($coa_id == 63 || $coa_id == 131) {
                                                            $validPerbandingan1 = $j['credit'] < $j['debit'];
                                                        } else {
                                                            $validPerbandingan1 = $j['debit'] < $j['credit'];
                                                        }

                                                        $highlight = $isRelevantSubjek && $selisih && $validPerbandingan;
                                                        $highlight1 = $validPerbandingan1;
                                                    @endphp

                                                        
                                                        <td class="text-end {{ $highlight ? 'bg-danger text-white' : '' }} {{ $highlight1 ? 'bg-success text-white' : '' }}">
                                                            {{ $j['debit'] ? number_format($j['debit'], 2, ',', '.') : 0 }}
                                                        </td>
                                                        <td class="text-end {{ $highlight ? 'bg-danger text-white' : '' }} {{ $highlight1 ? 'bg-success text-white' : '' }}">
                                                            {{ $j['credit'] ? number_format($j['credit'], 2, ',', '.') : 0 }}
                                                        </td>
                                                        <td class="text-start">
                                                            {!! $j['keterangan'] ?: '-' !!}
                                                        </td>                                                        
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        @if ($subjek =='customer_xpdc' && $coa_id == 46 || $subjek =='customer_trucking' && $coa_id == 47)     
                                                        <td class="text-end" colspan="6"><b>TOTAL</b></td>
                                                        <td class="text-end"><b
                                                                id="debit-total">{{ number_format($totalDebit, 2, ',', '.') }}</b>
                                                        @else
                                                        <td class="text-end" colspan="5"><b>TOTAL</b></td>
                                                        <td class="text-end"><b
                                                                id="debit-total">{{ number_format($totalDebit, 2, ',', '.') }}</b>
                                                        @endif
                                                        </td>
                                                        <td class="text-end"><b
                                                                id="credit-total">{{ number_format($totalCredit, 2, ',', '.') }}</b>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="d-flex justify-content-between">
                                                                <b class="text-start">SALDO : </b>
                                                                <b
                                                                    id="credit-total">{{ number_format($totalSaldo, 2, ',', '.') }}</b>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- {{ $data->links() }} --}}
                            {{-- @if ($data->hasMorePages())
                            <button wire:click.prevent="loadMore" class="btn btn-sm btn-primary w-100">Load more</button>
                        @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('assets/js/resize-column.js') }}"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js">
        </script>
        <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js">
        </script>
        <script>
            function load() {
                (function(window, ResizableTableColumns, undefined) {
                    var store = window.store && window.store.enabled ?
                        window.store :
                        null;

                    var els = document.querySelectorAll('table.data');
                    for (var index = 0; index < els.length; index++) {
                        var table = els[index];
                        if (table['rtc_data_object']) {
                            continue;
                        }

                        var options = {
                            store: store
                        };
                        if (table.querySelectorAll('thead > tr').length > 1) {
                            options.resizeFromBody = false;
                        }

                        new ResizableTableColumns(els[index], options);
                    }

                })(window, window.validide_resizableTableColumns.ResizableTableColumns, void(0));
            }

            // load();

            // $('table.data').dataTable({
            //     aLengthMenu: [
            //         [25, 50, 100, 200, -1],
            //         [25, 50, 100, 200, "All"]
            //     ],
            //     iDisplayLength: 25,
            //     fixedHeader: true,
            //     fixedColumns: {
            //         leftColumns: 2
            //     },
            // });
        </script>
      <script>
        $(document).ready(function() {
            @if ($subjek == 'vendor' && $coa_id == 131)
                var orderConfig = [[2, "desc"]];
                var columnDefsConfig = [{ "orderable": true, "targets": [2] }];
            @else
                var orderConfig = [[0, "desc"]];
                var columnDefsConfig = [{ "orderable": true, "targets": [0] }];
            @endif
    
            $('.table.data').DataTable({
                "ordering": true,       // Mengaktifkan fitur sorting
                "searching": true,      // Mengaktifkan fitur pencarian
                "paging": true,         // Mengaktifkan pagination
                "info": false,          // Menonaktifkan informasi jumlah data
                "autoWidth": false,
                "order": orderConfig,           // Konfigurasi urutan kolom sesuai kondisi
                "columnDefs": columnDefsConfig  // Konfigurasi columnDefs sesuai kondisi
            });
        });
    </script>    
    @endpush
@endsection
