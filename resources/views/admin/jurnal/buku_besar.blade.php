@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.dataTables.min.css">
    <style>
        /* ----------- PRINT MODE ONLY ----------- */
        /* ----------- PRINT MODE ----------- */
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');

            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
            }

            #print,
            #print * {
                visibility: visible;
                font-size: .7rem !important;
            }

            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -70px;
                color: #000;
            }

            #table td,
            #table th {
                border: 1px solid black !important;
            }
        }

        /* ----------- NORMAL SCREEN MODE ----------- */
        .table-responsive {
            overflow-x: auto;
            position: relative;
        }

        table th,
        table td {
            white-space: nowrap;
            font-size: .7rem;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        /* Freeze Base */
        .sticky-col {
            position: sticky;
            background: #f8f9fa !important;
            z-index: 4;
        }

        th.sticky-col {
            z-index: 6 !important;
            /* agar header di atas td */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        td.sticky-col {
            z-index: 5;
        }


        /* Saat sel di klik */
        td.clicked,
        th.clicked {
            background-color: #d1ecf1 !important;
            /* warna biru muda */
        }

        /* Atau saat diseleksi */
        td::selection,
        th::selection {
            background: #b3d7ff;
            color: #000;
        }

        /* Kolom yang di-freeze */
        .left-col {
            left: 0px;
            text-align: center;
            min-width: 90px;
        }

        .second-col {
            left: 90px;
            text-align: center;
            min-width: 120px;
        }

        .third-col {
            left: 210px;
            text-align: center;
            min-width: 80px;
        }

        .fourth-col {
            left: 290px;
            text-align: center;
            min-width: 140px;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-success my-2" data-bs-toggle="modal"
                        data-bs-target="#modalExport">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
                <div id="print">
                    {{-- <livewire:buku-besar :month="request('month')"/> --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th style="width:200px">Akun :</th>
                                                <th>
                                                    <form action="{{ route('jurnal.buku_besar') }}" method="get">
                                                        <input type="hidden" name="month" value="{{ $month }}">
                                                        <input type="hidden" name="year" value="{{ $year }}">
                                                        <input type="hidden" name="coa_id" value="{{ $coa_id }}">
                                                        <select class="form-control px-3 py-1" name="coa_id"
                                                            onchange="submit()" style="font-size:.8rem">
                                                            @foreach ($coas as $item)
                                                                <option {{ $coa_id == $item->id ? 'selected' : '' }}
                                                                    value="{{ $item->id }}">{{ $item->kode }} -
                                                                    {{ $item->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th style="width:200px">Tahun :</th>
                                                <th>
                                                    <form
                                                        action="{{ route('jurnal.buku_besar', ['month' => $month, 'coa_id' => $coa_id, 'year' => $year]) }}"
                                                        method="get">
                                                        <input type="hidden" name="month" value="{{ $month }}">
                                                        <input type="hidden" name="year" value="{{ $year }}">
                                                        <input type="hidden" name="coa_id" value="{{ $coa_id }}">
                                                        <select class="form-control px-3 py-1" name="year"
                                                            onchange="submit()" style="font-size:.8rem">
                                                            <option {{ $year == '2023' ? 'selected' : '' }} value="2023">2023
                                                            </option>
                                                            <option {{ $year == '2024' ? 'selected' : '' }} value="2024">2024
                                                            </option>
                                                            <option {{ $year == '2025' ? 'selected' : '' }} value="2025">2025
                                                            </option>
                                                            <option {{ $year == '2026' ? 'selected' : '' }} value="2026">2026
                                                            </option>
                                                            <option {{ $year == '2027' ? 'selected' : '' }} value="2027">2027
                                                            </option>
                                                            <option {{ $year == '2028' ? 'selected' : '' }} value="2028">2028
                                                            </option>
                                                            <option {{ $year == '2029' ? 'selected' : '' }} value="2029">2029
                                                            </option>
                                                            <option {{ $year == '2030' ? 'selected' : '' }} value="2030">2030
                                                            </option>
                                                        </select>
                                                    </form>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm mt-2" style="font-size: .7rem; white-space:nowrap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            @foreach ($months as $item)
                                                <th>{{ $item }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>Saldo Awal</b></td>
                                            @foreach ($saldo['saldo_awal'] as $idx => $item)
                                                <td>{{ number_format($item, 2, '.', ',') }}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><b>Debit</b></td>
                                            @foreach ($saldo['debit'] as $idx => $item)
                                                <td>{{ number_format($item, 2, '.', ',') }}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><b>Credit</b></td>
                                            @foreach ($saldo['credit'] as $idx => $item)
                                                <td>{{ number_format($item, 2, '.', ',') }}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td><b>Saldo Akhir</b></td>
                                            @foreach ($saldo['saldo_akhir'] as $idx => $item)
                                                <td>{{ number_format($item, 2, '.', ',') }}</td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="d-flex gap-2">
                                <b class="mt-2">Bulan: </b>
                                @foreach ($months as $idx => $item)
                                    <a href="{{ route('jurnal.buku_besar', ['month' => sprintf('%02d', $idx + 1), 'coa_id' => $coa_id]) }}"
                                        wire:click="changeMonth({{ $idx + 1 }})"
                                        class="{{ $idx + 1 == (int) $month ? 'bg-light-success' : '' }} text-center text-dark"
                                        style="border: solid 1px gray; width:50px; text-decoration:none">{{ $item }}</a>
                                @endforeach
                            </div>
                            {{-- <div class="my-3">
                            <label for="search">Search</label>
                            <input type="text" wire:model="search" class="form-control" placeholder="Cari berdasarkan nomor jurnal/keterangan/akun/job/tanggal">
                        </div> --}}
                            <div class="table-responsive mt-3">
                                <table data-rtc-resizable-table="table.{{ $month }}"
                                    class="table data table-bordered table-sm mt-3 data-table" style="font-size: .7rem;">
                                    <thead>
                                        <tr>
                                            <th class="sticky-col left-col text-center">Tanggal</th>
                                            <th class="sticky-col text-center">No. Jurnal</th>
                                            <th class="sticky-col text-center">No. Akun</th>
                                            <th class="sticky-col text-center">Akun</th>
                                            @if ($coa->is_cont)
                                                <th data-rtc-resizable="cont">No. Cont</th>
                                            @endif
                                            @if ($coa->is_nopol)
                                                <th data-rtc-resizable="nopol">Nopol</th>
                                            @endif
                                            @if ($coa->is_nojob)
                                                <th data-rtc-resizable="job">No. Job</th>
                                            @endif
                                            @if ($coa->is_invoice)
                                                <th data-rtc-resizable="invoice">Invoice</th>
                                            @endif
                                            @if ($coa->is_invoice_agen)
                                                <th data-rtc-resizable="invoice">Invoice Agen</th>
                                            @endif
                                            @if ($coa->is_invoice_vendor)
                                                <th data-rtc-resizable="invoice">Invoice Vendor</th>
                                            @endif
                                            @if ($coa->is_invoice_external)
                                                <th data-rtc-resizable="invoice">Invoice External</th>
                                            @endif
                                            @if ($coa->is_invoice_trucking)
                                                <th data-rtc-resizable="invoice">Invoice Trucking</th>
                                            @endif
                                            <th data-rtc-resizable="nama">Keterangan</th>
                                            <th data-rtc-resizable="debit">Debit</th>
                                            <th data-rtc-resizable="credit">Credit</th>
                                            @if ($coa->is_nobg)
                                                <th data-rtc-resizable="bg">No. BG</th>
                                            @endif
                                            @if ($coa->is_nobupot)
                                                <th data-rtc-resizable="nobupot">No. Bupot PPh 23</th>
                                            @endif
                                            @if ($coa->is_tglbupot)
                                                <th data-rtc-resizable="tglbupot">Tgl Bupot PPh 23</th>
                                            @endif
                                            <th data-rtc-resizable="tglbupot">Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            @php
                                                if ($tipe == 'D') {
                                                    if ($item->debit > 0) {
                                                        $saldo_awal += $item->debit;
                                                    } else {
                                                        $saldo_awal -= $item->credit;
                                                    }
                                                } else {
                                                    if ($item->debit > 0) {
                                                        $saldo_awal -= $item->debit;
                                                    } else {
                                                        $saldo_awal += $item->credit;
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td class="sticky-col left-col">
                                                    {{ date('d/m/y', strtotime($item->created_at)) }}</td>
                                                <td class="sticky-col second-col">{{ $item->nomor }}</td>
                                                <td class="sticky-col third-col">{{ $item->coa->kode }}</td>
                                                <td class="sticky-col fourth-col">{{ $item->coa->nama }}</td>
                                                @if ($coa->is_cont)
                                                    <td>{{ $item->container ?? '-' }}</td>
                                                @endif
                                                @if ($coa->is_nopol)
                                                    <td>{{ $item->nopol ?? '-' }}</td>
                                                @endif
                                                @if ($coa->is_nojob)
                                                    <td style="left: 30px">
                                                        {{ $item->order ? $item->order->job . '-' . sprintf('%02d', $item->order->no_job) : '-' }}
                                                    </td>
                                                @endif
                                                @if ($coa->is_invoice)
                                                    <td>{{ $item->invoice ?? '-' }}</td>
                                                @endif
                                                @if ($coa->is_invoice_agen)
                                                    <td>{{ $item->invoice_agen ?? '-' }}</td>
                                                @endif
                                                @if ($coa->is_invoice_vendor)
                                                    <td>{{ $item->invoice_vendor ?? '-' }}</td>
                                                @endif
                                                @if ($coa->is_invoice_external)
                                                    <td>{{ $item->invoice_external ?? '-' }}</td>
                                                @endif
                                                @if ($coa->is_invoice_trucking)
                                                    <td>{{ $item->invoice_trucking ?? '-' }}</td>
                                                @endif
                                                <td>{{ $item->nama }}</td>
                                                <td>{{ number_format($item->debit, 2, ',', '.') }}</td>
                                                <td>{{ number_format($item->credit, 2, ',', '.') }}</td>
                                                @if ($coa->is_nobg)
                                                    <td>{{ $item->no_bg }}</td>
                                                @endif
                                                @if ($coa->is_nobupot)
                                                    <td>{{ $item->order ? ($item->order->transaksi ? $item->order->transaksi->no_bupot : '') : '-' }}
                                                    </td>
                                                @endif
                                                @if ($coa->is_tglbupot)
                                                    <td>{{ $item->order ? ($item->order->transaksi ? $item->order->transaksi->tgl_bupot : '') : '-' }}
                                                    </td>
                                                @endif
                                                <td>{{ number_format($saldo_awal, 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

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

    <!-- Modal Export -->
    <div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('jurnal.exportJurnalBatch') }}" method="POST">
                @csrf

                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Jurnal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Pilih kelompok COA yang akan di export</label>

                            <select name="coaGroup" id="coa_select" class="form-control select2">
                                @foreach ($coasCode as $item)
                                    <option {{ $coa_id == $item ? 'selected' : '' }} value="{{ $item }}">
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Export
                        </button>
                    </div>

                </div>
            </form>
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

            $('#modalExport').on('shown.bs.modal', function() {

                $('#coa_select').select2({
                    dropdownParent: $('#modalExport'),
                    width: '100%',
                    placeholder: 'Pilih COA'
                });

            });

            // load();

            $('table.data').dataTable({
                aLengthMenu: [
                    [25, 50, 100, 200, -1],
                    [25, 50, 100, 200, "All"]
                ],
                iDisplayLength: 25
            });
        </script>
    @endpush
@endsection
