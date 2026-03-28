@extends('layouts.admin')

<style>
    .dashboard-title {
        font-size: 20px;
        font-weight: 600;
        margin: 20px 40px;
    }

    .grid-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        padding: 0 40px;
    }

    .grid-item {
        width: 100%;
    }

    .table-dashboard {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        background: #fff;
    }

    .table-dashboard th {
        background: #f3f4f6;
        text-align: left;
        padding: 8px;
        border: 1px solid #e5e7eb;
    }

    .table-dashboard td {
        padding: 8px;
        border: 1px solid #e5e7eb;
    }

    .table-dashboard tbody tr:hover {
        background: #f9fafb;
    }

    .table-total {
        font-weight: bold;
        background: #f3f4f6;
    }

    .text-right {
        text-align: right;
        font-family: monospace;
    }

    .saldo-minus {
        color: #dc2626;
    }

    @media(max-width:768px) {

        .grid-container {
            grid-template-columns: 1fr;
        }

    }

    .dashboard-wrapper {
        background: #ffffff;
        margin: 20px;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }
</style>


@section('content')
    @php

        $totalSaldoA = $coa1->sum(fn($item) => $totals[$item->id]['selisih'] ?? 0);
        $totalSaldoB = $coa2->sum(fn($item) => $totals[$item->id]['selisih'] ?? 0);
        $totalSaldoC = $coa3->sum(fn($item) => $totals1[$item->id]['selisih'] ?? 0);
        $totalSaldoD = $coa4->sum(fn($item) => $totals1[$item->id]['selisih'] ?? 0);

        $totalDebitA = $coa1->sum(fn($item) => $totals[$item->id]['debit'] ?? 0);
        $totalCreditA = $coa1->sum(fn($item) => $totals[$item->id]['credit'] ?? 0);

        $totalDebitB = $coa2->sum(fn($item) => $totals[$item->id]['debit'] ?? 0);
        $totalCreditB = $coa2->sum(fn($item) => $totals[$item->id]['credit'] ?? 0);

        $totalDebitC = $coa3->sum(fn($item) => $totals1[$item->id]['debit'] ?? 0);
        $totalCreditC = $coa3->sum(fn($item) => $totals1[$item->id]['credit'] ?? 0);

        $totalDebitD = $coa4->sum(fn($item) => $totals1[$item->id]['debit'] ?? 0);
        $totalCreditD = $coa4->sum(fn($item) => $totals1[$item->id]['credit'] ?? 0);

    @endphp


    <div class="dashboard-title">
        Dashboard Monitor
    </div>


    <div class="grid-container">


        {{-- ================= COA 1 ================= --}}
        <div class="grid-item">

            <table class="table-dashboard">

                <thead>
                    <tr>
                        <th>No Akun</th>
                        <th>Nama Akun</th>
                        <th class="text-right">Saldo</th>
                    </tr>
                </thead>

                <tbody>
@php
    $coa74 = $coa1->firstWhere('id', 74);
    $coa75 = $coa1->firstWhere('id', 75);
@endphp

@foreach ($coa1 as $item)
    @if (in_array($item->id, [74, 75]))
        @continue
    @endif

    @php
        $total = $totals[$item->id] ?? ['debit' => 0, 'credit' => 0, 'selisih' => 0];
        $total74 = $totals[74] ?? ['debit' => 0, 'credit' => 0, 'selisih' => 0];
        $total75 = $totals[75] ?? ['debit' => 0, 'credit' => 0, 'selisih' => 0];
    @endphp

    <tr>
        <td>
            @if ($item->id == 46 && $coa74)
                {{ $item->kode }}<br>
                <span class="saldo-minus">{{ $coa74->kode }}</span>
            @elseif ($item->id == 47 && $coa75)
                {{ $item->kode }}<br>
                <span class="saldo-minus">{{ $coa75->kode }}</span>
            @else
                {{ $item->kode }}
            @endif
        </td>

        <td>
            @if ($item->id == 46 && $coa74)
                {{ $item->nama }}<br>
                <span class="saldo-minus">{{ $coa74->nama }}  (Belum Teridentifikasi)</span>
            @elseif ($item->id == 47 && $coa75)
                {{ $item->nama }}<br>
                <span class="saldo-minus">{{ $coa75->nama }} (Belum Teridentifikasi)</span>
            @else
                {{ $item->nama }}
            @endif
        </td>

        <td class="text-right">
            @if ($item->id == 46 && $coa74)
                <span>
                    {{ number_format($total['selisih'], 2, ',', '.') }}
                </span><br>
                <span class="saldo-minus">
                    {{ number_format($total74['selisih'], 2, ',', '.') }}
                </span>

            @elseif ($item->id == 47 && $coa75)
                <span>
                    {{ number_format($total['selisih'], 2, ',', '.') }}
                </span><br>
                <span class="saldo-minus">
                    {{ number_format($total75['selisih'], 2, ',', '.') }}
                </span>

            @else
                <span class="{{ $total['selisih'] < 0 ? 'saldo-minus' : '' }}">
                    {{ number_format($total['selisih'], 2, ',', '.') }}
                </span>
            @endif
        </td>
    </tr>
@endforeach

                </tbody>

                <tfoot>

                    <tr class="table-total">

                        <td colspan="2">TOTAL</td>

                        <td class="text-right">
                            {{ number_format($totalSaldoA, 2, ',', '.') }}
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>


        {{-- ================= COA 2 ================= --}}
        <div class="grid-item">

            <table class="table-dashboard">

                <thead>
                    <tr>
                        <th>No Akun</th>
                        <th>Nama Akun</th>
                        <th class="text-right">Saldo</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($coa2 as $item)
                        @php
                            $total = $totals[$item->id] ?? ['debit' => 0, 'credit' => 0, 'selisih' => 0];
                        @endphp

                        <tr>

                            <td>{{ $item->kode }}</td>

                            <td>{{ $item->nama }}</td>

                            <td class="text-right {{ $total['selisih'] < 0 ? 'saldo-minus' : '' }}">
                                {{ number_format($total['selisih'], 2, ',', '.') }}
                            </td>

                        </tr>
                    @endforeach

                </tbody>

                <tfoot>

                    <tr class="table-total">

                        <td colspan="2">TOTAL</td>

                        <td class="text-right">
                            {{ number_format($totalSaldoB, 2, ',', '.') }}
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>


        {{-- ================= COA 3 ================= --}}
        <div class="grid-item">

            <table class="table-dashboard">

                <thead>
                    <tr>
                        <th>No Akun</th>
                        <th>Nama Akun</th>
                        <th class="text-right">Saldo</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($coa3 as $item)
                        @php
                            $total = $totals1[$item->id] ?? ['debit' => 0, 'credit' => 0, 'selisih' => 0];
                        @endphp

                        <tr>

                            <td>{{ $item->kode }}</td>

                            <td>{{ $item->nama }}</td>

                            <td class="text-right {{ $total['selisih'] < 0 ? 'saldo-minus' : '' }}">
                                {{ number_format($total['selisih'], 2, ',', '.') }}
                            </td>

                        </tr>
                    @endforeach

                </tbody>

                <tfoot>

                    <tr class="table-total">

                        <td colspan="2">TOTAL</td>

                        <td class="text-right">
                            {{ number_format($totalSaldoC, 2, ',', '.') }}
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>


        {{-- ================= COA 4 ================= --}}
        <div class="grid-item">

            <table class="table-dashboard">

                <thead>
                    <tr>
                        <th>No Akun</th>
                        <th>Nama Akun</th>
                        <th class="text-right">Saldo</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($coa4 as $item)
                        @php
                            $total = $totals1[$item->id] ?? ['debit' => 0, 'credit' => 0, 'selisih' => 0];
                        @endphp

                        <tr>

                            <td>{{ $item->kode }}</td>

                            <td>{{ $item->nama }}</td>


                            <td class="text-right {{ $total['selisih'] < 0 ? 'saldo-minus' : '' }}">
                                {{ number_format($total['selisih'], 2, ',', '.') }}
                            </td>

                        </tr>
                    @endforeach

                </tbody>

                <tfoot>

                    <tr class="table-total">

                        <td colspan="2">TOTAL</td>

                        <td class="text-right">
                            {{ number_format($totalSaldoD, 2, ',', '.') }}
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>


    </div>
@endsection
