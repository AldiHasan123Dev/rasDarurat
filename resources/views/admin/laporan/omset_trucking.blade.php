@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/selectize.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/selectize.bootstrap5.css') }}">
    <style>
        @media print {
            @page {
                size: landscape
            }

            body * {
                visibility: hidden;
            }

            body {
                width: 100%;
            }

            #print,
            #print * {
                visibility: visible;
                font-family: 'Open Sans', sans-serif;
                font-size: .7rem !important;
                color: black !important;
            }

            #print {
                position: absolute;
                top: -80px;
            }

            tr th,
            tr {
                border: 1px solid black;
            }
        }

        thead {
            position: sticky;
            z-index: 12;
            top: 0px;
            background: white;
        }

        th,
        td {
            white-space: nowrap;
        }

        #table {
        table-layout: fixed; /* wajib biar width dari columnDefs kepakai */
        }

        .text-wrap {
        white-space: normal !important;
        word-wrap: break-word;
        }
        .text-end {
            text-align: right !important;
        }



        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }

        #table th,
        #table td {
            vertical-align: middle;
            height: 20px;
            padding: 0 5px !important;
            border: 1px solid black;
            color: black;
        }

        .dataTables_scroll {
            overflow: auto;
            height: 400px;
        }

        thead input {
            width: 100%;
            padding: 0px;
            box-sizing: border-box;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex gap-3">
                            {{-- <button type="button" class="btn btn-sm btn-success" onclick="window.print()"><i class="fas fa-print"></i> PRINT</button> --}}
                            <!-- Form untuk XPDC -->
                            @if ($tipe === 'xpdc')
                                <form action="{{ route('jurnal.balik.trucking') }}" method="post"
                                    style="display: inline-block;">
                                    @csrf
                                    <input type="hidden" name="jurnal_id" value="{{ json_encode($jurnal_id) }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <input type="hidden" name="tipe" value="xpdc">
                                    <button type="submit" class="btn btn-sm btn-warning" onclick="syncJurnalBalik()">JURNAL
                                        BALIK TRUCK XPDC</button>
                                </form>
                            @else
                                <!-- Form untuk EXTERNAL -->
                                <form action="{{ route('jurnal.balik.trucking.ext') }}" method="post"
                                    style="display: inline-block;">
                                    @csrf
                                    <input type="hidden" name="jurnal_id" value="{{ json_encode($jurnal_id) }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <input type="hidden" name="tipe" value="ext">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="syncJurnalBalik()">JURNAL
                                        BALIK TRUCK EXTERNAL</button>
                                </form>
                            @endif

                        </div>
                        <form action="{{ url()->current() }}" method="get">
                            <div class="d-flex gap-3">
                                <select name="month" id="month" class="form-select" style="width: 150px"
                                    onchange="submit()">
                                    @foreach ($months as $idx => $item)
                                        <option value="{{ $idx + 1 }}" {{ $idx + 1 == $month ? 'selected' : '' }}>
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                                <select name="year" id="year" class="form-select" style="width: 150px"
                                    onchange="submit()">
                                    <option {{ $year == '2023' ? 'selected' : '' }} value="2023">2023</option>
                                    <option {{ $year == '2024' ? 'selected' : '' }} value="2024">2024</option>
                                    <option {{ $year == '2025' ? 'selected' : '' }} value="2025">2025</option>
                                    <option {{ $year == '2026' ? 'selected' : '' }} value="2026">2026</option>
                                    <option {{ $year == '2027' ? 'selected' : '' }} value="2027">2027</option>
                                </select>
                                <div>
                                    <label for="radio1">
                                        <input type="radio" name="tipe" id="radio1" value="xpdc"
                                            {{ $tipe == 'xpdc' ? 'checked' : '' }} onchange="submit()"> Trucking XPDC
                                    </label>
                                    <label for="radio2">
                                        <input type="radio" name="tipe" id="radio2" value="ext"
                                            {{ $tipe == 'ext' ? 'checked' : '' }} onchange="submit()"> Trucking External
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="print">
                        <div class="mt-3">
                            <div class="table-responsive" style="height: 400px">
                                <table class="table table-sm table-bordered mt-3" id="table" style="font-size: .7rem">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nomor</th>
                                            <th>Cont/Seal</th>
                                            <th>Job</th>
                                            <th>Keterangan</th>
                                            @if ($tipe == 'xpdc')
                                                <th class="text-end">1.6.2.2</th>
                                                <th class="text-end">2.1.5.2.2</th>
                                                <th class="text-end">Total</th>
                                            @else
                                                <th class="text-end">6.2.1</th>
                                                <th class="text-end">2.1.5.2.1</th>
                                                <th class="text-end">5.1.2</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total_per_coa = [];
                                        @endphp

                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach ($data as $orders)
                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach ($orders as $order)
                                                @php
                                                    $coa_ids = $tipe == 'xpdc' ? [61, 81] : [98, 80, 87];

                                                    $jurnals = $order
                                                        ->jurnals()
                                                        ->whereIn('coa_id', $coa_ids)
                                                        ->where('debit', '>', 0)
                                                        ->get();
                                                    foreach ($jurnals as $jurnal) {
                                                        $total_per_coa[$jurnal->coa_id] =
                                                            ($total_per_coa[$jurnal->coa_id] ?? 0) + $jurnal->debit;
                                                    }

                                                    if (in_array(87, $coa_ids)) {
                                                        $jurnals = $order
                                                            ->jurnals()
                                                            ->where('coa_id', 87)
                                                            ->where('credit', '>', 0)
                                                            ->get();

                                                        foreach ($jurnals as $jurnal) {
                                                            $total_per_coa[$jurnal->coa_id] =
                                                                ($total_per_coa[$jurnal->coa_id] ?? 0) +
                                                                $jurnal->credit;
                                                        }
                                                    }
                                                @endphp


                                                @php
                                                    $total +=
                                                        $order
                                                            ->jurnals()
                                                            ->where('nama', 'like', 'sangu sopir%')
                                                            ->where('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])
                                                            ->where('debit', '>', 0)
                                                            ->first()->debit ?? 0;
                                                    $total +=
                                                        $order
                                                            ->jurnals()
                                                            ->where(function ($q) {
                                                                $q->whereRaw('LOWER(nama) LIKE ?', [
                                                                    'biaya kuli%',
                                                                ])->orWhereRaw('LOWER(nama) LIKE ?', ['sangu kuli%']);
                                                            })
                                                            ->where('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])
                                                            ->where('debit', '>', 0)
                                                            ->first()->debit ?? 0;
                                                    $total +=
                                                        $order
                                                            ->jurnals()
                                                            ->where('nama', 'like', 'simpanan sangu sopir%')
                                                            ->where('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])
                                                            ->where('debit', '>', 0)
                                                            ->first()->debit ?? 0;
                                                    if ($tipe == 'ext') {
                                                        $total +=
                                                            $order
                                                                ->jurnals()
                                                                ->where('nama', 'like', 'biaya operasional trucking%')
                                                                ->where('coa_id', [98, 80, 87])
                                                                ->where('debit', '>', 0)
                                                                ->first()->debit ?? 0;
                                                    }
                                                @endphp

                                                <tr class="{{ $no % 2 == 0 ? 'table-primary' : '' }}">
                                                    <td>
                                                        {!! is_null(
                                                            $order->jurnals()->where('nama', 'like', 'sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                        )
                                                            ? '-'
                                                            : implode(
                                                                '<br>',
                                                                $order->jurnals()->where('nama', 'like', 'sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('created_at')->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->format('d/m/y'))->toArray(),
                                                            ) !!}
                                                    </td>

                                                    </td>
                                                    <td>
                                                        {!! is_null(
                                                            $order->jurnals()->where('nama', 'like', 'sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                        )
                                                            ? '-'
                                                            : implode(
                                                                '<br>',
                                                                $order->jurnals()->where('nama', 'like', 'sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('nomor')->toArray(),
                                                            ) !!}
                                                    </td>

                                                    </td>
                                                    <td>{{ $order->container }}/{{ $order->seal }}</td>
                                                    <td>
                                                        @if ($order->order)
                                                            {{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $order->jurnals()->where('nama', 'like', 'sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first()->nama ?? 'Sangu Sopir -' }}
                                                    </td>
                                                    <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'sangu sopir%')->where('coa_id', $tipe == 'xpdc' ? 61 : 98)->where('debit', '>', 0)->first()->debit ?? 0) }}
                                                    </td>
                                                    <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'sangu sopir%')->where('coa_id', $tipe == 'xpdc' ? 81 : 80)->where('debit', '>', 0)->first()->debit ?? 0) }}
                                                    </td>
                                                    @if ($tipe == 'ext')
                                                        <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'sangu sopir%')->where('coa_id', 87)->where('debit', '>', 0)->first()->debit ?? 0) }}
                                                        </td>
                                                    @else
                                                        <td  class="text-end">0</td>
                                                    @endif
                                                </tr>
                                                <tr class="{{ $no % 2 == 0 ? 'table-primary' : '' }}">
                                                    <td>
                                                        {!! is_null(
                                                            $order->jurnals()->where(function ($q) {
                                                                    $q->whereRaw('LOWER(nama) LIKE ?', ['biaya kuli%'])->orWhereRaw('LOWER(nama) LIKE ?', ['sangu kuli%']);
                                                                })->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                        )
                                                            ? '-'
                                                            : implode(
                                                                '<br>',
                                                                $order->jurnals()->where(function ($q) {
                                                                        $q->whereRaw('LOWER(nama) LIKE ?', ['biaya kuli%'])->orWhereRaw('LOWER(nama) LIKE ?', ['sangu kuli%']);
                                                                    })->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('created_at')->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->format('d/m/y'))->toArray(),
                                                            ) !!}
                                                    </td>

                                                    <td>
                                                        {!! is_null(
                                                            $order->jurnals()->where(function ($q) {
                                                                    $q->whereRaw('LOWER(nama) LIKE ?', ['biaya kuli%'])->orWhereRaw('LOWER(nama) LIKE ?', ['sangu kuli%']);
                                                                })->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                        )
                                                            ? '-'
                                                            : implode(
                                                                '<br>',
                                                                $order->jurnals()->where(function ($q) {
                                                                        $q->whereRaw('LOWER(nama) LIKE ?', ['biaya kuli%'])->orWhereRaw('LOWER(nama) LIKE ?', ['sangu kuli%']);
                                                                    })->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('nomor')->toArray(),
                                                            ) !!}
                                                    </td>

                                                    <td>{{ $order->container }}/{{ $order->seal }}</td>
                                                    <td>
                                                        @if ($order->order)
                                                            {{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $order->jurnals()->where(function ($q) {
                                                            $q->whereRaw('LOWER(nama) LIKE ?', ['biaya kuli%'])->orWhereRaw('LOWER(nama) LIKE ?', ['sangu kuli%']);
                                                        })->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first()->nama ?? 'Biaya Kuli -' }}
                                                    </td>
                                                    <td class="text-end">{{ number_format(
                                                        $order->jurnals()->where(function ($q) {
                                                                $q->whereRaw('LOWER(nama) LIKE ?', ['biaya kuli%'])->orWhereRaw('LOWER(nama) LIKE ?', ['sangu kuli%']);
                                                            })->where('coa_id', $tipe == 'xpdc' ? 61 : 98)->where('debit', '>', 0)->first()->debit ?? 0,
                                                    ) }}
                                                    </td>
                                                    <td class="text-end">{{ number_format(
                                                        $order->jurnals()->where(function ($q) {
                                                                $q->whereRaw('LOWER(nama) LIKE ?', ['biaya kuli%'])->orWhereRaw('LOWER(nama) LIKE ?', ['sangu kuli%']);
                                                            })->where('coa_id', $tipe == 'xpdc' ? 81 : 80)->where('debit', '>', 0)->first()->debit ?? 0,
                                                    ) }}
                                                    </td>
                                                    @if ($tipe == 'ext')
                                                        <td class="text-end">{{ number_format(
                                                            $order->jurnals()->where(function ($q) {
                                                                    $q->whereRaw('LOWER(nama) LIKE ?', ['biaya kuli%'])->orWhereRaw('LOWER(nama) LIKE ?', ['sangu kuli%']);
                                                                })->where('coa_id', 87)->where('debit', '>', 0)->first()->debit ?? 0,
                                                        ) }}
                                                        </td>
                                                    @else
                                                        <td  class="text-end">0</td>
                                                    @endif
                                                </tr>
                                                <tr class="{{ $no % 2 == 0 ? 'table-primary' : '' }}">
                                                    <td>
                                                        {!! is_null(
                                                            $order->jurnals()->where('nama', 'like', 'simpanan sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                        )
                                                            ? '-'
                                                            : implode(
                                                                '<br>',
                                                                $order->jurnals()->where('nama', 'like', 'simpanan sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('created_at')->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->format('d/m/y'))->toArray(),
                                                            ) !!}
                                                    </td>

                                                    </td>
                                                    <td>
                                                        {!! is_null(
                                                            $order->jurnals()->where('nama', 'like', 'simpanan sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                        )
                                                            ? '-'
                                                            : implode(
                                                                '<br>',
                                                                $order->jurnals()->where('nama', 'like', 'simpanan sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('nomor')->toArray(),
                                                            ) !!}
                                                    </td>

                                                    </td>
                                                    <td>{{ $order->container }}/{{ $order->seal }}</td>
                                                    <td>
                                                        @if ($order->order)
                                                            {{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $order->jurnals()->where('nama', 'like', 'simpanan sangu sopir%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first()->nama ?? 'Simpanan Sangu Sopir -' }}
                                                    </td>
                                                    <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'simpanan sangu sopir%')->where('coa_id', $tipe == 'xpdc' ? 61 : 98)->where('debit', '>', 0)->first()->debit ?? 0) }}
                                                    </td>
                                                    <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'simpanan sangu sopir%')->where('coa_id', $tipe == 'xpdc' ? 81 : 80)->where('debit', '>', 0)->first()->debit ?? 0) }}
                                                    </td>
                                                    @if ($tipe == 'ext')
                                                        <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'simpanan sangu sopir%')->where('coa_id', 87)->where('debit', '>', 0)->first()->debit ?? 0) }}
                                                        </td>
                                                    @else
                                                        <td  class="text-end">{{ $tipe == 'ext' ? '0' : number_format($total)}}</td>
                                                    @endif
                                                </tr>
                                                @if ($tipe == 'ext')
                                                    <tr class="{{ $no % 2 == 0 ? 'table-primary' : '' }}">
                                                        <td>
                                                            {!! is_null(
                                                                $order->jurnals()->where('nama', 'like', 'biaya operasional trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                            )
                                                                ? '-'
                                                                : implode(
                                                                    '<br>',
                                                                    $order->jurnals()->where('nama', 'like', 'biaya operasional trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('created_at')->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->format('d/m/y'))->toArray(),
                                                                ) !!}
                                                        </td>
                                                        <td>
                                                            {!! is_null(
                                                                $order->jurnals()->where('nama', 'like', 'biaya operasional trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                            )
                                                                ? '-'
                                                                : implode(
                                                                    '<br>',
                                                                    $order->jurnals()->where('nama', 'like', 'biaya operasional trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('nomor')->toArray(),
                                                                ) !!}
                                                        </td>
                                                        </td>
                                                        <td>{{ $order->container }}/{{ $order->seal }}</td>
                                                        <td>
                                                            @if ($order->order)
                                                                {{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $order->jurnals()->where('nama', 'like', 'biaya operasional trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first()->nama ?? 'Biaya Oprasional Trucking -' }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'biaya operasional trucking%')->where('coa_id', $tipe == 'xpdc' ? 61 : 98)->where('debit', '>', 0)->first()->debit ?? 0) }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'biaya operasional trucking%')->where('coa_id', $tipe == 'xpdc' ? 81 : 80)->where('debit', '>', 0)->first()->debit ?? 0) }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'biaya operasional trucking%')->where('coa_id', 87)->where('debit', '>', 0)->first()->debit ?? 0) }}
                                                        </td>
                                                    </tr>
                                                    <tr class="table-danger">
                                                        <td>
                                                            {!! is_null(
                                                                $order->jurnals()->where('nama', 'like', 'pendapatan trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                            )
                                                                ? '-'
                                                                : implode(
                                                                    '<br>',
                                                                    $order->jurnals()->where('nama', 'like', 'pendapatan trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('created_at')->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->format('d/m/y'))->toArray(),
                                                                ) !!}
                                                        </td>

                                                        <td>
                                                            {!! is_null(
                                                                $order->jurnals()->where('nama', 'like', 'pendapatan trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->first(),
                                                            )
                                                                ? '-'
                                                                : implode(
                                                                    '<br>',
                                                                    $order->jurnals()->where('nama', 'like', 'pendapatan trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('debit', '>', 0)->pluck('nomor')->toArray(),
                                                                ) !!}
                                                        </td>

                                                        </td>
                                                        <td>{{ $order->container }}/{{ $order->seal }}</td>
                                                        <td>
                                                            @if ($order->order)
                                                                {{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $order->jurnals()->where('nama', 'like', 'pendapatan trucking%')->whereIn('coa_id', $tipe == 'xpdc' ? [61, 81] : [98, 80, 87])->where('credit', '>', 0)->first()->nama ?? 'Pendapatan Trucking -' }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'pendapatan trucking%')->where('coa_id', $tipe == 'xpdc' ? 61 : 98)->where('credit', '>', 0)->first()->credit ?? 0) }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'pendapatan trucking%')->where('coa_id', $tipe == 'xpdc' ? 81 : 80)->where('credit', '>', 0)->first()->credit ?? 0) }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->jurnals()->where('nama', 'like', 'pendapatan trucking%')->where('coa_id', 87)->where('credit', '>', 0)->first()->credit ?? 0) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-2">
        <div class="d-flex gap-3 mt-2 justify-content-center">
            @if ($tipe == 'xpdc')
                <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                    <li class="list-group-item fw-bold">Total COA 1.6.2.2</li>
                    <li class="list-group-item fw-bold">{{ number_format($total_per_coa[61] ?? 0) }}</li>
                </ul>
                <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                    <li class="list-group-item fw-bold">Total COA 2.1.5.2.2</li>
                    <li class="list-group-item fw-bold">{{ number_format($total_per_coa[81] ?? 0) }}</li>
                </ul>
            @else
                <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                    <li class="list-group-item fw-bold">Total COA 6.2.1</li>
                    <li class="list-group-item fw-bold">{{ number_format($total_per_coa[98] ?? 0) }}</li>
                </ul>
                <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                    <li class="list-group-item fw-bold">Total COA 2.1.5.2.1</li>
                    <li class="list-group-item fw-bold">{{ number_format($total_per_coa[80] ?? 0) }}</li>
                </ul>
                <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                    <li class="list-group-item fw-bold">Total COA 5.1.2</li>
                    <li class="list-group-item fw-bold">{{ number_format($total_per_coa[87] ?? 0) }}</li>
                </ul>
            @endif
        </div>

        @if ($tipe === 'ext')
            
        <div class="container my-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-2 px-3 text-white"
                    style="background: linear-gradient(90deg, #007bff, #0056b3);">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-bar-chart-fill me-2"></i>Cek COA 6.2.1 at periode
                    </h6>
                </div>

                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover align-middle mb-0">
                              <thead class="table text-center small" style="background-color: #515a62">
                                <tr>
                                    <th style="width: 25%; color: white">Periode</th>
                                    <th  style="width: 25%; color: white">No Jurnal (D)</th>
                                    <th  style="width: 25%; color: white">Total Debit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapPerBulan as $row)
                                    <tr class="small">
                                        <td class="text-center fw-medium">
                                            {{ \Carbon\Carbon::parse($row['periode'] . '-01')->isoFormat('MMMM Y') }}
                                        </td>
                                        <td class="text-end text-primary">
                                              @php
                                                  $items = $row['list_jurnal_d']->toArray();
                                                  $chunks = array_chunk($items, 10); // pecah jadi per 10 item
                                              @endphp
  
                                              @foreach ($chunks as $chunk)
                                                  {!! implode(', ', $chunk) !!}<br>
                                              @endforeach
                                         </td>
                                        <td class="text-end text-primary">
                                            {{ number_format($row['total_debit'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-2">Data tidak tersedia</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


                <div class="container my-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-2 px-3 text-white"
                    style="background: linear-gradient(90deg, #007bff, #0056b3);">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-bar-chart-fill me-2"></i>Cek COA 6.2.1 yang tidak memiliki pendapatan (kosongan)
                    </h6>
                </div>
                        <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover align-middle mb-0">
                              <thead class="table text-center small" style="background-color: #515a62">
                                <tr>
                                    <th style="width: 25%; color: white">Periode</th>
                                    <th  style="width: 25%; color: white">No Jurnal (D)</th>
                                    <th  style="width: 25%; color: white">Total Debit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapPerBulan1 as $row)
                                    <tr class="small">
                                        <td class="text-center fw-medium">
                                            {{ \Carbon\Carbon::parse($row['periode'] . '-01')->isoFormat('MMMM Y') }}
                                        </td>
                                        <td class="text-end text-primary">
                                              @php
                                                  $items = $row['list_jurnal_d']->toArray();
                                                  $chunks = array_chunk($items, 10); // pecah jadi per 10 item
                                              @endphp
  
                                              @foreach ($chunks as $chunk)
                                                  {!! implode(', ', $chunk) !!}<br>
                                              @endforeach
                                         </td>
                                        <td class="text-end text-primary">
                                            {{ number_format($row['total_debit'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-2">Data tidak tersedia</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <div class="container my-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-2 px-3 text-white"
                   style="background: linear-gradient(90deg, #007bff, #0056b3);">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-bar-chart-fill me-2"></i>Cek COA 6.2.1 yang tidak memiliki pendapatan di bulan ini
                    </h6>
                </div>

                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover align-middle mb-0">
                              <thead class="table text-center small" style="background-color: #515a62">
                                <tr>
                                    <th style="width: 25%; color: white">Periode</th>
                                    <th  style="width: 25%; color: white">No Jurnal (D)</th>
                                    <th  style="width: 25%; color: white">Total Debit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapTesPerBulan as $row)
                                    <tr class="small">
                                        <td class="text-center fw-medium">
                                            {{ \Carbon\Carbon::parse($row['periode'] . '-01')->isoFormat('MMMM Y') }}
                                        </td>
                                        <td class="text-end text-primary">
                                              @php
                                                  $items = $row['list_jurnal_d']->toArray();
                                                  $chunks = array_chunk($items, 1); // pecah jadi per 10 item
                                              @endphp
  
                                              @foreach ($chunks as $chunk)
                                                  {!! implode(', ', $chunk) !!}<br>
                                              @endforeach
                                         </td>
                                        <td class="text-end text-primary">
                                            {{ number_format($row['total_debit'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-2">Data tidak tersedia</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <div class="container my-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-2 px-3 text-white"
                    style="background: linear-gradient(90deg, #007bff, #0056b3);">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-bar-chart-fill me-2"></i>Cek Jurnal yang harusnya pakai COA 6.2.1
                    </h6>
                </div>

                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover align-middle mb-0">
                            <thead class="table text-center small" style="background-color: #515a62">
                                <tr>
                                    <th style="width: 25%; color: white">Periode</th>
                                    <th  style="width: 25%; color: white">No Jurnal (D)</th>
                                    <th  style="width: 25%; color: white">Total Debit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapPerBulan2 as $row)
                                    <tr class="small">
                                        <td class="text-center fw-medium">
                                            {{ \Carbon\Carbon::parse($row['periode'] . '-01')->isoFormat('MMMM Y') }}
                                        </td>
                                        <td class="text-end text-primary">
                                              @php
                                                  $items = $row['list_jurnal_d']->toArray();
                                                  $chunks = array_chunk($items, 1); // pecah jadi per 10 item
                                              @endphp
  
                                              @foreach ($chunks as $chunk)
                                                  {!! implode(', ', $chunk) !!}<br>
                                              @endforeach
                                         </td>
                                        <td class="text-end text-primary">
                                            {{ number_format($row['total_debit'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-2">Data tidak tersedia</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        

    @else
            <div class="container my-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-2 px-3 text-white"
                    style="background: linear-gradient(90deg, #007bff, #0056b3);">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-bar-chart-fill me-2"></i>Sangu yang bukan 1.6.2.2 atau 2.1.5.2.2
                    </h6>
                </div>

                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover align-middle mb-0">
                            <thead class="table text-center small" style="background-color: #515a62">
                                <tr>
                                    <th style="width: 25%; color: white">Periode</th>
                                    <th  style="width: 25%; color: white">No Jurnal (D)</th>
                                    <th  style="width: 25%; color: white">Total Debit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapPerBulan as $row)
                                    <tr class="small">
                                        <td class="text-center fw-medium">
                                            {{ \Carbon\Carbon::parse($row['periode'] . '-01')->isoFormat('MMMM Y') }}
                                        </td>
                                        <td class="text-end text-primary">
                                              @php
                                                  $items = $row['list_jurnal_d']->toArray();
                                                  $chunks = array_chunk($items, 10); // pecah jadi per 10 item
                                              @endphp
  
                                              @foreach ($chunks as $chunk)
                                                  {!! implode(', ', $chunk) !!}<br>
                                              @endforeach
                                         </td>
                                        <td class="text-end text-primary">
                                            {{ number_format($row['total_debit'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-2">Data tidak tersedia</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
     @endif
</div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/selectize.js') }}"></script>
    <script>
        let table = $('#table').DataTable({
            // fixedColumns: {
            //     left: 3,
            //     right: 0
            // },
            autoWidth: false,
            paging: false,
            ordering: false,
            scrollCollapse: true,
            fixedHeader: true,
            // select: true,
            // scrollX:true,
            // scrollY: 400,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel'
            }, ],
            search: {
                return: true
            },
    columnDefs: [
         {
             className: "text-end",    // Bootstrap: text-end = rata kanan
        width: "100px"    
        },
        {
            targets: 4, // kolom ke-5 = "Keterangan"
            width: "300px",
            className: "text-wrap"
        },
        {
            targets: 3, // kolom ke-5 = "Keterangan"
            width: "100px",
             className: "text-wrap"
        },
        {
            targets: 2, // kolom ke-5 = "Keterangan"
            width: "150px",
             className: "text-wrap"
        },
         {
            targets: 1, // kolom ke-5 = "Keterangan"
            width: "100px",
             className: "text-wrap"
        },
        {
            targets: 0, // kolom ke-5 = "Keterangan"
            width: "50px"
        }
    ]
        });
        // table.column( 0 ).visible( false );
        // table.column( 1 ).visible( false );
        // table.column( 40 ).visible( false );
        jQuery('.dataTable').wrap('<div class="dataTables_scroll" />');

        $('.select2').select2({
            dropdownParent: $('#modal-jurnal')
        });
    </script>
@endsection

{{-- initComplete: function () {
    this.api()
        .columns()
        .every(function () {
            let column = this;
            let title = column.header().textContent;

            // Create input element
            let input = document.createElement('input');
            input.placeholder = title;
            column.header().replaceChildren(input);

            // Event listener for user input
            input.addEventListener('keyup', () => {
                if (column.search() !== this.value) {
                    column.search(input.value).draw();
                }
            });
        });
} --}}
