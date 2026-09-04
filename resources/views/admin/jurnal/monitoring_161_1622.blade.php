@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <link rel="stylesheet"
    href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <style>
        ```css

        /* Container utama */
        .container {
            max-width: 100%;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            margin-left: 0;
            margin-right: 0;
        }

        /* Card */
        .card {
            max-width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        .card-body {
            padding: 1rem !important;
        }

        /* Judul section */
        .section-title {
            font-weight: bold;
            font-size: 0.95rem;
            margin: 12px 0 6px;
        }

        /* Wrapper tabel */
        .table-wrapper {
            background: #f9f9f9;
            padding: 6px;
            border-radius: 6px;
            margin-top: 6px;
            margin-bottom: 10px;
        }

        /* TABEL */
        .table-monitoring {
            width: 100%;
            margin-bottom: 0 !important;
            font-size: 11px !important;
        }

        /* Header & isi tabel */
        .table-monitoring th,
        .table-monitoring td {
            padding: 3px 6px !important;
            vertical-align: middle !important;
            line-height: 1.3 !important;
        }

        /* Header */
        .table-monitoring thead th {
            font-size: 11px !important;
            font-weight: 600;
            padding: 4px 6px !important;
        }

        /* Footer */
        .table-monitoring tfoot th {
            font-size: 11px !important;
            padding: 4px 6px !important;
        }

        /* Angka kanan */
        .table-monitoring .text-right {
            text-align: right;
        }
    </style>
@endsection

@section('content')

<div class="container">

    {{-- ================= COA 1.6.1 ================= --}}
    <div class="card mb-3">
        <div class="card-body">

            <div class="section-title">
                Cek Jurnal COA 1.6.1
            </div>

            <div class="table-wrapper">

                <table id="table161"
                    class="table table-bordered table-striped table-hover table-monitoring">

                    <thead class="thead-dark">
                        <tr>
                            <th width="40">No</th>
                            <th>Periode</th>
                            <th class="text-right">Total Debit</th>
                            <th class="text-right">Total Credit</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($periode161 as $index => $item)

                            @php
                                $debit = (float) $item['total_debit'];
                                $credit = (float) $item['total_credit'];
                            @endphp

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ $item['periode'] }}
                                </td>

                                <td class="text-right">
                                    {{ number_format($debit, 0, ',', '.') }}
                                </td>

                                <td class="text-right">
                                    {{ number_format($credit, 0, ',', '.') }}
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                    @if ($periode161->count() > 0)

                        @php
                            $grandDebit = $periode161->sum(function ($item) {
                                return (float) $item['total_debit'];
                            });

                            $grandCredit = $periode161->sum(function ($item) {
                                return (float) $item['total_credit'];
                            });
                        @endphp

                        <tfoot>
                            <tr class="font-weight-bold">
                                <th colspan="2" class="text-right">
                                    TOTAL
                                </th>

                                <th class="text-right">
                                    {{ number_format($grandDebit, 0, ',', '.') }}
                                </th>

                                <th class="text-right">
                                    {{ number_format($grandCredit, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>

                    @endif

                </table>

            </div>

        </div>
    </div>


    {{-- ================= COA 1.6.2.2 ================= --}}
    <div class="card mb-3">
        <div class="card-body">

            <div class="section-title">
                Cek Jurnal COA 1.6.2.2
            </div>

            <div class="table-wrapper">

                <table id="table1622"
                    class="table table-bordered table-striped table-hover table-monitoring">

                    <thead class="thead-dark">
                        <tr>
                            <th width="40">No</th>
                            <th>Periode</th>
                            <th class="text-right">Total Debit</th>
                            <th class="text-right">Total Credit</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($periode1622 as $index => $item)

                            @php
                                $debit = (float) $item['total_debit'];
                                $credit = (float) $item['total_credit'];
                            @endphp

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ $item['periode'] }}
                                </td>

                                <td class="text-right">
                                    {{ number_format($debit, 0, ',', '.') }}
                                </td>

                                <td class="text-right">
                                    {{ number_format($credit, 0, ',', '.') }}
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                    @if ($periode1622->count() > 0)

                        @php
                            $grandDebit = $periode1622->sum(function ($item) {
                                return (float) $item['total_debit'];
                            });

                            $grandCredit = $periode1622->sum(function ($item) {
                                return (float) $item['total_credit'];
                            });
                        @endphp

                        <tfoot>
                            <tr class="font-weight-bold">
                                <th colspan="2" class="text-right">
                                    TOTAL
                                </th>

                                <th class="text-right">
                                    {{ number_format($grandDebit, 0, ',', '.') }}
                                </th>

                                <th class="text-right">
                                    {{ number_format($grandCredit, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>

                    @endif

                </table>

            </div>

        </div>
    </div>

</div>

@endsection


@section('script')

<script type="text/ecmascript"
    src="{{ asset('assets/js/grid.locale-en.js') }}">
</script>

<script type="text/ecmascript"
    src="{{ asset('assets/js/jquery.jqGrid.min.js') }}">
</script>

<script src="{{ asset('assets/js/resize-column.js') }}"></script>


{{-- DataTables --}}
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>


<script>
    $(document).ready(function() {

        $('#table161').DataTable({
            pageLength: 12,
            lengthMenu: [
                [12, 25, 50, 100, -1],
                [12, 25, 50, 100, "Semua"]
            ],
            ordering: true,
            searching: true,
            info: true,
            paging: true,

            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "›",
                    previous: "‹"
                }
            },

            columnDefs: [
                {
                    targets: [0],
                    orderable: false
                },
                {
                    targets: [2, 3],
                    className: 'text-right'
                }
            ]
        });


        $('#table1622').DataTable({
            pageLength: 12,
            lengthMenu: [
                [12, 25, 50, 100, -1],
                [12, 25, 50, 100, "Semua"]
            ],
            ordering: true,
            searching: true,
            info: true,
            paging: true,

            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "›",
                    previous: "‹"
                }
            },

            columnDefs: [
                {
                    targets: [0],
                    orderable: false
                },
                {
                    targets: [2, 3],
                    className: 'text-right'
                }
            ]
        });

    });
</script>

@endsection
