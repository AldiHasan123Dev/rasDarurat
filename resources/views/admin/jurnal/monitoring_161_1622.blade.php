@extends('layouts.admin')

@section('style')

    {{-- DataTables --}}
    <link rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <style>

        .container {
            max-width: 100%;
            padding-left: 12px;
            padding-right: 12px;
            margin-left: 0;
            margin-right: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | CARD
        |--------------------------------------------------------------------------
        */

        .monitoring-card {
            border: 0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .monitoring-header {
            padding: 14px 18px;
            background: #fff;
            border-bottom: 1px solid #eee;
        }

        .monitoring-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        .monitoring-subtitle {
            margin-left: 34px;
            margin-top: 3px;
            font-size: 11px;
            color: #888;
        }

        .coa-icon {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            background: #f1f3f5;
            font-size: 14px;
        }

        .monitoring-body {
            padding: 15px;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        .table-monitoring {
            width: 100% !important;
            margin-bottom: 0 !important;
            font-size: 11px !important;
        }

        .table-monitoring th {
            font-size: 11px !important;
            font-weight: 600;
            padding: 7px 8px !important;
            vertical-align: middle !important;
            white-space: nowrap;
        }

        .table-monitoring td {
            padding: 6px 8px !important;
            vertical-align: middle !important;
        }

        .table-monitoring tbody tr {
            transition: background 0.15s ease;
        }

        .table-monitoring tbody tr:hover {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }


        /*
        |--------------------------------------------------------------------------
        | INVOICE BADGE
        |--------------------------------------------------------------------------
        */

        .invoice-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .invoice-badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 5px;
            background: #f1f3f5;
            color: #495057;
            font-size: 10px;
            font-weight: 500;
            white-space: nowrap;
        }

        .invoice-empty {
            color: #999;
            font-size: 10px;
            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | DATATABLE
        |--------------------------------------------------------------------------
        */

        .dataTables_wrapper {
            font-size: 11px;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }

        .dataTables_wrapper .dataTables_filter input {
            height: 30px;
            font-size: 11px;
            margin-left: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 4px 8px;
        }

        .dataTables_wrapper .dataTables_length select {
            height: 30px;
            font-size: 11px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 10px;
            color: #777;
        }

        .dataTables_wrapper .pagination {
            font-size: 10px;
        }

        .dataTables_wrapper .pagination .page-link {
            padding: 4px 8px;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 5px;
        }

    </style>

@endsection


@section('content')

<div class="container">


    {{-- ========================================================= --}}
    {{-- CARD COA 1.6.1                                           --}}
    {{-- ========================================================= --}}

    <div class="card monitoring-card">

        <div class="monitoring-header">

            <div class="monitoring-title">

                <span class="coa-icon">
                    <i class="fas fa-book"></i>
                </span>

                <span>
                    Cek Jurnal COA 1.6.1
                </span>

            </div>

            <div class="monitoring-subtitle">
                Monitoring jurnal berdasarkan periode invoice
            </div>

        </div>


        <div class="monitoring-body">

            <table id="table161"
                class="table table-bordered table-striped table-hover table-monitoring">

                <thead class="thead-dark">

                    <tr>

                        <th width="40">
                            No
                        </th>

                        <th width="150">
                            Periode
                        </th>

                        <th class="text-right" width="160">
                            Total Debit
                        </th>

                        <th class="text-right" width="160">
                            Total Credit
                        </th>

                        <th>
                            No Jurnal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach ($periode161 as $index => $item)

                        @php

                            $debit = (float) $item['total_debit'];

                            $credit = (float) $item['total_credit'];

                            $Jurnal161 = $NomorJurnal161->get(
                                $item['periode_key'],
                                collect()
                            );

                        @endphp


                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>
                                <strong>
                                    {{ $item['periode'] }}
                                </strong>
                            </td>


                            <td class="text-right">

                                {{ number_format(
                                    $debit,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td class="text-right">

                                {{ number_format(
                                    $credit,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td>

                                @if ($Jurnal161->count() > 0)

                                    <div class="invoice-list">

                                        @foreach ($Jurnal161 as $j161)

                                            <span class="invoice-badge">

                                                {{ $j161->nomor }}...

                                            </span>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="invoice-empty">
                                        Tidak ada invoice
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>


                @if ($periode161->count() > 0)

                    @php

                        $grandDebit = $periode161->sum(
                            function ($item) {
                                return (float) $item['total_debit'];
                            }
                        );

                        $grandCredit = $periode161->sum(
                            function ($item) {
                                return (float) $item['total_credit'];
                            }
                        );

                    @endphp


                    <tfoot>

                        <tr class="font-weight-bold">

                            <th colspan="2" class="text-right">
                                TOTAL
                            </th>

                            <th class="text-right">

                                {{ number_format(
                                    $grandDebit,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </th>

                            <th class="text-right">

                                {{ number_format(
                                    $grandCredit,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </th>

                            <th></th>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- CARD COA 1.6.2.2                                         --}}
    {{-- ========================================================= --}}

    <div class="card monitoring-card">

        <div class="monitoring-header">

            <div class="monitoring-title">

                <span class="coa-icon">
                    <i class="fas fa-book"></i>
                </span>

                <span>
                    Cek Jurnal COA 1.6.2.2
                </span>

            </div>

            <div class="monitoring-subtitle">
                Monitoring jurnal berdasarkan periode invoice
            </div>

        </div>


        <div class="monitoring-body">

            <table id="table1622"
                class="table table-bordered table-striped table-hover table-monitoring">

                <thead class="thead-dark">

                    <tr>

                        <th width="40">
                            No
                        </th>

                        <th width="150">
                            Periode
                        </th>

                        <th class="text-right" width="160">
                            Total Debit
                        </th>

                        <th class="text-right" width="160">
                            Total Credit
                        </th>

                        <th>
                            No Jurnal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach ($periode1622 as $index => $item)

                        @php

                            $debit = (float) $item['total_debit'];

                            $credit = (float) $item['total_credit'];

                            $Jurnal1622 = $NomorJurnal1622->get(
                                $item['periode_key'],
                                collect()
                            );

                        @endphp


                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>
                                <strong>
                                    {{ $item['periode'] }}
                                </strong>
                            </td>


                            <td class="text-right">

                                {{ number_format(
                                    $debit,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td class="text-right">

                                {{ number_format(
                                    $credit,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            <td>

                                @if ($Jurnal1622->count() > 0)

                                    <div class="invoice-list">

                                        @foreach ($Jurnal1622 as $j1622)

                                            <span class="invoice-badge">

                                                {{ $j1622->nomor }}...

                                            </span>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="invoice-empty">
                                        Tidak ada invoice
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>


                @if ($periode1622->count() > 0)

                    @php

                        $grandDebit = $periode1622->sum(
                            function ($item) {
                                return (float) $item['total_debit'];
                            }
                        );

                        $grandCredit = $periode1622->sum(
                            function ($item) {
                                return (float) $item['total_credit'];
                            }
                        );

                    @endphp


                    <tfoot>

                        <tr class="font-weight-bold">

                            <th colspan="2" class="text-right">
                                TOTAL
                            </th>

                            <th class="text-right">

                                {{ number_format(
                                    $grandDebit,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </th>

                            <th class="text-right">

                                {{ number_format(
                                    $grandCredit,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </th>

                            <th></th>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>

</div>

@endsection



@section('script')

    {{-- DataTables --}}
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>


    <script>

        $(document).ready(function () {

            /*
            |--------------------------------------------------------------------------
            | DataTable COA 1.6.1
            |--------------------------------------------------------------------------
            */

            $('#table161').DataTable({

                pageLength: 12,

                lengthMenu: [
                    [12, 25, 50, 100, -1],
                    [12, 25, 50, 100, 'Semua']
                ],

                searching: true,

                info: true,

                paging: true,

                autoWidth: false,

                language: {

                    search: 'Cari:',

                    lengthMenu: 'Tampilkan _MENU_ data',

                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                    infoEmpty: 'Tidak ada data',

                    zeroRecords: 'Data tidak ditemukan',

                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: '›',
                        previous: '‹'
                    }

                },

                columnDefs: [

                    {
                        targets: 0,
                        orderable: false
                    },

                    {
                        targets: [2, 3],
                        className: 'text-right'
                    }

                ]

            });


            /*
            |--------------------------------------------------------------------------
            | DataTable COA 1.6.2.2
            |--------------------------------------------------------------------------
            */

            $('#table1622').DataTable({

                pageLength: 12,

                lengthMenu: [
                    [12, 25, 50, 100, -1],
                    [12, 25, 50, 100, 'Semua']
                ],

                ordering: true,

                searching: true,

                info: true,

                paging: true,

                autoWidth: false,

                language: {

                    search: 'Cari:',

                    lengthMenu: 'Tampilkan _MENU_ data',

                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                    infoEmpty: 'Tidak ada data',

                    zeroRecords: 'Data tidak ditemukan',

                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: '›',
                        previous: '‹'
                    }

                },

                columnDefs: [

                    {
                        targets: 0,
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