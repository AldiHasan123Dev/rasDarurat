@extends('layouts.admin')
@section('style')
    <style>
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');

            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
                -webkit-print-color-adjust: exact;
            }

            .first-page {
                width: 100%;
                height: 100%;
                position: absolute;
                top: -180px;
            }

            .first-page2 {
                width: 100%;
                height: 100%;
                position: absolute;
                top: -190px;
            }

            #print,
            #print * {
                visibility: visible;
                font-size: .7rem !important;
            }

            #print {
                width: 100%;
                position: absolute;
                left: 0px;
                top: 0px;
                padding: 0px;
            }

            #table td,
            #table th {
                border: 1px solid rgb(255, 0, 0);
                padding: 0px !important;
            }

            #print {
                color: #000;
            }

            .page-break {
                page-break-after: always;
                overflow: hidden;
            }

            .bg-red{
                background-color: red !important;
                color: #fff !important;
            }
        }

        #print{
            padding: 10px;
            background-color: #fff;
        }

        .bg-red{
            background-color: red !important;
            color: #fff !important;
        }

        tr.heading td {
            border: 1px solid rgb(255, 0, 0);
            text-align: center;
        }

        tr th{
            border: 1px solid rgb(255, 0, 0);
            text-align: center;
        }

        .table tr td {
            vertical-align: middle;
            padding: 3px 3px;
            border: 1px solid rgb(255, 0, 0);
        }
        .table tbody tr td:first-child{
            padding-left: 10px !important;
        }

        .vertical{
            text-align:center;
            white-space:nowrap;
            transform-origin:50% 50%;
            transform: rotate(-90deg);
        }
    </style>
@endsection
@section('content')
    <div id="print">
        <table class="w-100 table">
            <thead>
                <tr>
                    <th style="width: 100px; color:red" colspan="2">Dibayar Kepada</th>
                    <th rowspan="2" style="vertical-align: middle; color:red">BUKTI BANK BAYAR</th>
                    <th style="color: red">Nomor</th>
                    <th style="width: 250px"></th>
                </tr>
                <tr>
                    <th colspan="2">{{ strtoupper($jadwal_kapal->pelayaran->nama) }}</th>
                    <th style="color: red">Tanggal</th>
                    <th style="width: 250px"></th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: red" class="bg-red text-white">
                    <td style="background: white" rowspan="{{ ($jobs->count() * 6) + 2 }}"></td>
                    {{-- <td rowspan="{{ ($jobs->count() * 6) + 2 }}" style="background-color:white;transform: rotate(180deg);white-space: nowrap; writing-mode: vertical-rl; ms-writing-mode: tb-rl; -webkit-writing-mode: vertical-rl; color:red">KEPERLUAN INTERN</td> --}}
                    <td class="text-center">PERKIRAAN</td>
                    <td class="text-center" colspan="2">URAIAN</td>
                    <td class="text-center">JUMLAH</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center" colspan="2">{{ strtoupper($jadwal_kapal->pelayaran->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }}</td>
                    <td></td>
                </tr>
                @foreach ($jobs as $list)
                    @foreach ($list->groupBy('opp') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">OPP(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}) ({{ implode(',',$item->sortBy('order.no_job')->pluck('order.no_job')->toArray()) }})
                                </td>
                                <td class="text-end">{{ number_format($item->first()->opp * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">OPP(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                                <td class="text-end">{{ number_format($item->first()->opp,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->groupBy('thc') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">THC LoLo SBY</td>
                                <td class="text-end">{{ number_format($item->first()->thc * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">THC Lolo SBY</td>
                                <td class="text-end">{{ number_format($item->first()->thc,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->groupBy('apbs') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">APBS</td>
                                <td class="text-end">{{ number_format($item->first()->apbs * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">APBS</td>
                                <td class="text-end">{{ number_format($item->first()->apbs,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->groupBy('cleaning') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">Cleaning</td>
                                <td class="text-end">{{ number_format($item->first()->cleaning * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">Cleaning</td>
                                <td class="text-end">{{ number_format($item->first()->cleaning,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->groupBy('lss') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">LSS (1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}) ({{ implode(',',$item->sortBy('order.no_job')->pluck('order.no_job')->toArray()) }})
                                </td>
                                <td class="text-end">{{ number_format($item->first()->lss * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">LSS (1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                                <td class="text-end">{{ number_format($item->first()->lss,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr style="height:30px">
                        <td></td>
                        <td colspan="2"></td>
                        <td></td>
                    </tr>
                @endforeach
                {{-- @foreach ($opp as $list)
                    @foreach ($list->groupBy('order.job') as $item)
                        <tr>
                            <td></td>
                            <td colspan="2">OPP(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                            <td class="text-end">{{ number_format($item->first()->opp,2,',','.') }}</td>
                        </tr>
                    @endforeach
                @endforeach --}}
                <tr style="border: 2px solid red">
                    <td style="color:red">Ch/ BG. No :</td>
                    <td>{{ $hp->no_bg_opp }}</td>
                    <td colspan="2"></td>
                    <td class="text-end fw-bold">{{ number_format($hp->nominal_bg_opp,2,',','.') }}</td>
                </tr>
            </tbody>
        </table>
        @if ($hp->no_bg_opt)
        <p class="page-break"></p>
        <table class="w-100 table">
            <thead>
                <tr>
                    <th style="width: 100px; color:red" colspan="2">Dibayar Kepada</th>
                    <th rowspan="2" style="vertical-align: middle; color:red">BUKTI BANK BAYAR</th>
                    <th style="color: red">Nomor</th>
                    <th style="width: 250px"></th>
                </tr>
                <tr>
                    <th colspan="2">{{ strtoupper($jadwal_kapal->pelayaran->nama) }}</th>
                    <th style="color: red">Tanggal</th>
                    <th style="width: 250px"></th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: red" class="bg-red text-white">
                    <td style="background: white" rowspan="{{ ($jobs->count() * 3) + 2 }}"></td>
                    {{-- <td rowspan="{{ ($jobs->count() * 6) + 2 }}" style="background-color:white;transform: rotate(180deg);white-space: nowrap; writing-mode: vertical-rl; ms-writing-mode: tb-rl; -webkit-writing-mode: vertical-rl; color:red">KEPERLUAN INTERN</td> --}}
                    <td class="text-center">PERKIRAAN</td>
                    <td class="text-center" colspan="2">URAIAN</td>
                    <td class="text-center">JUMLAH</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center" colspan="2">{{ strtoupper($jadwal_kapal->pelayaran->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }}</td>
                    <td></td>
                </tr>
                @foreach ($jobs as $list)
                    @foreach ($list->groupBy('opt') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">OPT(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}) ({{ implode(',',$item->sortBy('order.no_job')->pluck('order.no_job')->toArray()) }})
                                </td>
                                <td class="text-end">{{ number_format($item->first()->opt * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">OPT(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                                <td class="text-end">{{ number_format($item->first()->opt,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->groupBy('opt_stamp') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->opt_stamp * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->opt_stamp,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr style="height:30px">
                        <td></td>
                        <td colspan="2"></td>
                        <td></td>
                    </tr>
                @endforeach
                {{-- @foreach ($opp as $list)
                    @foreach ($list->groupBy('order.job') as $item)
                        <tr>
                            <td></td>
                            <td colspan="2">OPP(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                            <td class="text-end">{{ number_format($item->first()->opp,2,',','.') }}</td>
                        </tr>
                    @endforeach
                @endforeach --}}
                <tr style="border: 2px solid red">
                    <td style="color:red">Ch/ BG. No :</td>
                    <td>{{ $hp->no_bg_opt }}</td>
                    <td colspan="2"></td>
                    <td class="text-end fw-bold">{{ number_format($hp->nominal_bg_opt,2,',','.') }}</td>
                </tr>
            </tbody>
        </table>
        @endif
        @if ($hp->no_bg_ut)
        <p class="page-break"></p>
        <table class="w-100 table">
            <thead>
                <tr>
                    <th style="width: 100px; color:red" colspan="2">Dibayar Kepada</th>
                    <th rowspan="2" style="vertical-align: middle; color:red">BUKTI BANK BAYAR</th>
                    <th style="color: red">Nomor</th>
                    <th style="width: 250px"></th>
                </tr>
                <tr>
                    <th colspan="2">{{ strtoupper($jadwal_kapal->pelayaran->nama) }}</th>
                    <th style="color: red">Tanggal</th>
                    <th style="width: 250px"></th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: red" class="bg-red text-white">
                    <td style="background: white" rowspan="{{ ($jobs->count() * 4) + 2 }}"></td>
                    {{-- <td rowspan="{{ ($jobs->count() * 6) + 2 }}" style="background-color:white;transform: rotate(180deg);white-space: nowrap; writing-mode: vertical-rl; ms-writing-mode: tb-rl; -webkit-writing-mode: vertical-rl; color:red">KEPERLUAN INTERN</td> --}}
                    <td class="text-center">PERKIRAAN</td>
                    <td class="text-center" colspan="2">URAIAN</td>
                    <td class="text-center">JUMLAH</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center" colspan="2">{{ strtoupper($jadwal_kapal->pelayaran->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }}</td>
                    <td></td>
                </tr>
                @foreach ($jobs as $list)
                    @foreach ($list->groupBy('ut') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">UT (1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}) ({{ implode(',',$item->sortBy('order.no_job')->pluck('order.no_job')->toArray()) }})
                                </td>
                                <td class="text-end">{{ number_format($item->first()->ut * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">UT (1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                                <td class="text-end">{{ number_format($item->first()->ut,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->groupBy('ut_stamp') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->ut_stamp * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->ut_stamp,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->groupBy('bl') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">BL</td>
                                <td class="text-end">{{ number_format($item->first()->bl * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">BL</td>
                                <td class="text-end">{{ number_format($item->first()->bl,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr style="height:30px">
                        <td></td>
                        <td colspan="2"></td>
                        <td></td>
                    </tr>
                @endforeach
                {{-- @foreach ($opp as $list)
                    @foreach ($list->groupBy('order.job') as $item)
                        <tr>
                            <td></td>
                            <td colspan="2">OPP(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                            <td class="text-end">{{ number_format($item->first()->opp,2,',','.') }}</td>
                        </tr>
                    @endforeach
                @endforeach --}}
                <tr style="border: 2px solid red">
                    <td style="color:red">Ch/ BG. No :</td>
                    <td>{{ $hp->no_bg_ut }}</td>
                    <td colspan="2"></td>
                    <td class="text-end fw-bold">{{ number_format($hp->nominal_bg_ut,2,',','.') }}</td>
                </tr>
            </tbody>
        </table>
        @endif
    </div>
@endsection

@section('script')
    <script>
        window.print();
    </script>
@endsection
