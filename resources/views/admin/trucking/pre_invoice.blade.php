@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <style>
        .select2.select2-container.select2-container--default {
            width: 100% !important;
        }

        tr td {
            padding: 2px 10px;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2">
                <div class="d-flex gap-2 justify-content-between">
                    <p>List Pre Invoice Trucking (R1)</p>
                    <form action="{{ route('trucking.cetak.invoice') }}" method="post">
                        <input type="hidden" name="tipe" value="R1">
                        <input type="hidden" name="order_id" id="order_id1">
                        <button class="py-2 px-3 btn btn-success" onclick="return confirm('are you sure?')"
                            id="generate-invoice"><i class="fas fa-print"></i> Cetak Invoice</button>
                        @csrf
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" style="font-size: .7rem; white-space:nowrap">
                        <thead>
                            <tr>
                                <th style="width: 150px">Customer</th>
                                <th style="width: 30px">#</th>
                                <th>Tanggal Muat</th>
                                <th>Tanggal Totalan</th>
                                <th>Trucking</th>
                                <th>JOB</th>
                                <th>Container / Seal</th>
                                <th>Tipe</th>
                                <th>Nopol</th>
                                <th>Tujuan</th>
                                <th>Tarif</th>
                                <th>Add Cost</th>
                                <th>PPH 21 (3%)</th>
                                <th>PPH 23 (2%)</th>
                                <th>Total (Tarif - PPH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data1 as $cus => $orders)
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($orders as $order)
                                    @php
                                        if ($order->customer_id == 2 && $order->kendaraan->milik == 'R1') {
                                            $total += $order->tarif->tarif - (round($order->pph_21) + round($order->pph_23));
                                        }
                                        if ($order->customer_id != 2) {
                                            $total += $order->tarif->tarif - (round($order->pph_21) + round($order->pph_23));
                                        }
                                    @endphp
                                    <tr>
                                        @if ($loop->first)
                                            <td style="vertical-align: middle; text-align:center"
                                                rowspan="{{ $orders->count() }}">{{ $cus }}</td>
                                        @endif
                                        <td class="text-center"><input type="checkbox" name="order_id1"
                                                value="{{ $order->id }}"></td>
                                        <td class="text-center">{{ date('d/m/y', strtotime($order->tgl_muat)) }}</td>
                                        <td class="text-center">{{ date('d/m/y', strtotime($order->tgl_total)) }}</td>
                                        @if ($order->order)
                                            @if (
                                                ($order->customer_id == 2 && $order->order->trucking != 'XPDC') ||
                                                    ($order->customer_id != 2 && $order->order->trucking == 'XPDC'))
                                                <td class="bg-light-danger">{{ $order->order->trucking ?? '-' }}</td>
                                                <td class="bg-light-danger">
                                                    {{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                </td>
                                            @else
                                                <td>{{ $order->order->trucking ?? '-' }}</td>
                                                <td>{{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                </td>
                                            @endif
                                        @else
                                            <td>-</td>
                                            <td>-</td>
                                        @endif
                                        <td>{{ $order->container }} / {{ $order->seal }}</td>
                                        <td>{{ $order->tipe }}'</td>
                                        <td>{{ $order->kendaraan->nopol }} | {{ $order->kendaraan->milik }}</td>
                                        <td>{{ $order->tarif->tujuan->tujuanInfo->nama ?? '-' }}</td>
                                        <td>{{ number_format($order->tarif->tarif) }}</td>
                                        <td>{{ number_format($order->tagihans->sum('jumlah')) }}</td>
                                        <td>{{ number_format(round($order->pph_21)) }}</td>
                                        <td>{{ number_format(round($order->pph_23)) }}</td>
                                        @if ($order->customer_id == 2 && $order->kendaraan->milik == 'R2')
                                            <td>0 </td>
                                        @else
                                            <td>{{ number_format($order->tarif->tarif + $order->tagihans->sum('jumlah') - (round($order->pph_21) + round($order->pph_23))) }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                <tr class="border-bottom border-dark">
                                    <td colspan="5" class="text-center"><b>TOTAL</b></td>
                                    <td colspan="8" class="border border-dark"><b>Rp. {{ number_format($total) }}</b>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">Tidak Ada Data!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header p-2">
                <div class="d-flex gap-2 justify-content-between">
                    <p>List Pre Invoice Trucking (R2)</p>
                    <form action="{{ route('trucking.cetak.invoice') }}" method="post">
                        <input type="hidden" name="tipe" value="R2">
                        <input type="hidden" name="order_id" id="order_id2">
                        <button class="py-2 px-3 btn btn-success" onclick="return confirm('are you sure?')"
                            id="generate-invoice"><i class="fas fa-print"></i> Cetak Invoice</button>
                        @csrf
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" style="font-size: .7rem; white-space:nowrap">
                        <thead>
                            <tr>
                                <th style="width: 150px">Customer</th>
                                <th style="width: 30px">#</th>
                                <th>Tanggal Muat</th>
                                <th>Tanggal Totalan</th>
                                <th>Trucking</th>
                                <th>JOB</th>
                                <th>Container / Seal</th>
                                <th>Tipe</th>
                                <th>Nopol</th>
                                <th>Tujuan</th>
                                <th>Tarif</th>
                                <th>Add Cost</th>
                                <th>PPH 21 (3%)</th>
                                <th>PPH 23 (2%)</th>
                                <th>Total (Tarif - PPH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data2 as $cus => $orders)
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($orders as $order)
                                    @php
                                        if ($order->customer_id == 2 && $order->kendaraan->milik == 'R1') {
                                            $total += $order->tarif->tarif - (round($order->pph_21) + round($order->pph_23));
                                        }
                                        if ($order->customer_id != 2) {
                                            $total += $order->tarif->tarif - (round($order->pph_21) + round($order->pph_23));
                                        }
                                    @endphp
                                    <tr class="{{ $order->kendaraan->milik=='vendor'?'bg-light-info':''}}">
                                        @if ($loop->first)
                                            <td style="vertical-align: middle; text-align:center"
                                                rowspan="{{ $orders->count() }}">{{ $cus }}</td>
                                        @endif
                                        <td class="text-center"><input type="checkbox" name="order_id2"
                                                value="{{ $order->id }}"></td>
                                        <td class="text-center">{{ date('d/m/y', strtotime($order->tgl_muat)) }}</td>
                                        <td class="text-center">{{ date('d/m/y', strtotime($order->tgl_total)) }}</td>
                                        @if ($order->order)
                                            @if (
                                                ($order->customer_id == 2 && $order->order->trucking != 'XPDC') ||
                                                    ($order->customer_id != 2 && $order->order->trucking == 'XPDC'))
                                                <td class="bg-light-danger">{{ $order->order->trucking ?? '-' }}</td>
                                                <td class="bg-light-danger">
                                                    {{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                </td>
                                            @else
                                                <td>{{ $order->order->trucking ?? '-' }}</td>
                                                <td>{{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                </td>
                                            @endif
                                        @else
                                            <td>-</td>
                                            <td>-</td>
                                        @endif
                                        <td>{{ $order->container }} / {{ $order->seal }}</td>
                                        <td>{{ $order->tipe }}'</td>
                                        <td>{{ $order->kendaraan->nopol }} | {{ $order->kendaraan->milik }}</td>
                                        <td>{{ $order->tarif->tujuan->tujuanInfo->nama ?? '-' }}</td>
                                        <td>{{ number_format($order->tarif->tarif) }}</td>
                                        <td>{{ number_format($order->tagihans->sum('jumlah')) }}</td>
                                        <td>{{ number_format(round($order->pph_21)) }}</td>
                                        <td>{{ number_format(round($order->pph_23)) }}</td>
                                        @if ($order->customer_id == 2 && $order->kendaraan->milik == 'R2')
                                            <td>0 </td>
                                        @else
                                            <td>{{ number_format($order->tarif->tarif + $order->tagihans->sum('jumlah') - (round($order->pph_21) + round($order->pph_23))) }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                <tr class="border-bottom border-dark">
                                    <td colspan="5" class="text-center"><b>TOTAL</b></td>
                                    <td colspan="8" class="border border-dark"><b>Rp. {{ number_format($total) }}</b>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">Tidak Ada Data!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- <div class="card mt-4">
            <div class="card-header p-2">
                <div class="d-flex gap-2 justify-content-between">
                    <p>List Pre Invoice Trucking Vendor</p>
                    <form action="{{ route('trucking.cetak.invoice') }}" method="post">
                        <input type="hidden" name="tipe" value="VENDOR">
                        <input type="hidden" name="order_id" id="order_id_vendor">
                        <button class="py-2 px-3 btn btn-success" onclick="return confirm('are you sure?')"
                            id="generate-invoice-vendor"><i class="fas fa-print"></i> Cetak Invoice</button>
                        @csrf
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" style="font-size: .7rem; white-space:nowrap">
                        <thead>
                            <tr>
                                <th style="width: 150px">Customer</th>
                                <th style="width: 30px">#</th>
                                <th>Tanggal Muat</th>
                                <th>Tanggal Totalan</th>
                                <th>Trucking</th>
                                <th>JOB</th>
                                <th>Container / Seal</th>
                                <th>Tipe</th>
                                <th>Nopol</th>
                                <th>Tujuan</th>
                                <th>Tarif</th>
                                <th>Add Cost</th>
                                <th>PPH 21 (3%)</th>
                                <th>PPH 23 (2%)</th>
                                <th>Total (Tarif - PPH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data3 as $cus => $orders)
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($orders as $order)
                                    @php
                                        if ($order->customer_id == 2 && $order->kendaraan->milik == 'R1') {
                                            $total += $order->tarif->tarif - (round($order->pph_21) + round($order->pph_23));
                                        }
                                        if ($order->customer_id != 2) {
                                            $total += $order->tarif->tarif - (round($order->pph_21) + round($order->pph_23));
                                        }
                                    @endphp
                                    <tr>
                                        @if ($loop->first)
                                            <td style="vertical-align: middle; text-align:center"
                                                rowspan="{{ $orders->count() }}">{{ $cus }}</td>
                                        @endif
                                        <td class="text-center"><input type="checkbox" name="order_id_vendor"
                                                value="{{ $order->id }}"></td>
                                        <td class="text-center">{{ date('d/m/y', strtotime($order->tgl_muat)) }}</td>
                                        <td class="text-center">-</td>
                                        @if ($order->order)
                                            @if (
                                                ($order->customer_id == 2 && $order->order->trucking != 'XPDC') ||
                                                    ($order->customer_id != 2 && $order->order->trucking == 'XPDC'))
                                                <td class="bg-light-danger">{{ $order->order->trucking ?? '-' }}</td>
                                                <td class="bg-light-danger">
                                                    {{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                </td>
                                            @else
                                                <td>{{ $order->order->trucking ?? '-' }}</td>
                                                <td>{{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}
                                                </td>
                                            @endif
                                        @else
                                            <td>-</td>
                                            <td>-</td>
                                        @endif
                                        <td>{{ $order->container }} / {{ $order->seal }}</td>
                                        <td>{{ $order->tipe }}'</td>
                                        <td>{{ $order->kendaraan->nopol }} | {{ $order->kendaraan->milik }}</td>
                                        <td>{{ $order->tarif->tujuan->tujuanInfo->nama ?? '-' }}</td>
                                        <td>{{ number_format($order->tarif->tarif) }}</td>
                                        <td>{{ number_format($order->tagihans->sum('jumlah')) }}</td>
                                        <td>{{ number_format(round($order->pph_21)) }}</td>
                                        <td>{{ number_format(round($order->pph_23)) }}</td>
                                        @if ($order->customer_id == 2 && $order->kendaraan->milik == 'R2')
                                            <td>0 </td>
                                        @else
                                            <td>{{ number_format($order->tarif->tarif + $order->tagihans->sum('jumlah') - (round($order->pph_21) + round($order->pph_23))) }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                <tr class="border-bottom border-dark">
                                    <td colspan="5" class="text-center"><b>TOTAL</b></td>
                                    <td colspan="8" class="border border-dark"><b>Rp. {{ number_format($total) }}</b>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">Tidak Ada Data!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div> --}}
    </div>

@endsection

@section('script')
    <script>
        let id1 = [];
        let id2 = [];
        let id3 = [];

        $('input:checkbox[name=order_id1]').change(function(e) {
            check1()
        });
        $('input:checkbox[name=order_id2]').change(function(e) {
            check2()
        });
        $('input:checkbox[name=order_id_vendor]').change(function(e) {
            check3()
        });

        function check1() {
            id1 = [];
            $("input:checkbox[name=order_id1]:checked").each(function() {
                id1.push($(this).val());
            });
            $('#order_id1').val(id1);
        }

        function check2() {
            id2 = [];
            $("input:checkbox[name=order_id2]:checked").each(function() {
                id2.push($(this).val());
            });
            $('#order_id2').val(id2);
        }

        function check3() {
            id3 = [];
            $("input:checkbox[name=order_id_vendor]:checked").each(function() {
                id3.push($(this).val());
            });
            $('#order_id_vendor').val(id3);
        }
    </script>
@endsection
