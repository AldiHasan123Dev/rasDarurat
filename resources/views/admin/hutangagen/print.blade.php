@extends('layouts.admin')
@section('style')
    <style>
        @media print {

            body * {
                visibility: hidden;
                color: #000;
                -webkit-print-color-adjust:exact !important;
                print-color-adjust:exact !important;
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
                font-size: 1rem !important;
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
                color: #ffffff !important;
            }
            .table tr td {
                padding: 0px 5px !important;
                color: #000;
                font-weight: 600;
            }
        }

        #print{
            padding: 10px;
            background-color: #fff;
        }

        .bg-red{
            background-color: red !important;
            color: #ffffff !important;
            font-weight: bold;
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
                    <th style="vertical-align: middle; color:red">BUKTI BANK KELUAR</th>
                    <th style="color: red">Nomor</th>
                    <th style="width: 120px"></th>
                </tr>
                <tr>
                    <th colspan="2">{{ strtoupper($order->agent->nama) }}</th>
                    <th style="color: red; border:none">{{ $hutang_agen->first()->jurnal }}</th>
                    <th style="color: red">Tanggal</th>
                    <th style="width: 120px"></th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: red" class="text-white">
                    <td style="background: white" id="rowspan-opp" rowspan="{{ 3 + $tagihan->count() + ($rows * 3) }}"></td>
                    <td class="bg-red text-center">PERKIRAAN</td>
                    <td class="bg-red text-center" colspan="2" style="width: 65%">URAIAN</td>
                    <td class="bg-red text-center">JUMLAH</td>
                </tr>
                @foreach ($hutang_agen->groupBy('tarif') as $tarif_group)
                    @foreach ($tarif_group->groupBy('order.job') as $job => $job_group)
                        <tr>
                            <td></td>
                            <td class="text-start" colspan="2">Biaya Dooring Job {{ $job }} ({{ implode(',',$job_group->pluck('order.no_job')->toArray()) }}) / {{ $hutang_agen->first()->order->tarif->customer->nama }}</td>
                            <td class="text-end">{{ number_format($job_group->first()->tarif * 2,2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-start" colspan="2">PPN (1,1%)</td>
                            <td class="text-end">{{ number_format($job_group->first()->ppn * 2,2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-start" style="color: red" colspan="2">Pot PPH (2%)</td>
                            <td class="text-end" style="color: red">- {{ number_format($job_group->first()->pph * 2,2,',','.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td></td>
                    <td colspan="2" style="color: white">BLANK AREA</td>
                    <td></td>
                </tr>
                @foreach ($tagihan as $item)
                <tr>
                    <td></td>
                    <td class="text-start" colspan="2">{{ $item->nama }}</td>
                    <td class="text-end">{{ number_format($item->jumlah,2,',','.') }}</td>
                </tr>
                @endforeach
                <tr style="border: 2px solid red">
                    <td style="color:red">TOTAL</td>
                    <td class="fw-bold" colspan="2"></td>
                    <td class="text-end fw-bold">{{ number_format($total,2,',','.') }}</td>
                </tr>
            </tbody>
        </table>
        <table class="w-100 table" style="position: relative; top:-10px">
            <thead>
                <tr>
                    <td class="text-start" style="color: red">TERBILANG :</td>
                    <td colspan="7" style="text-transform: uppercase; font-weight:bold">{{ $terbilang }} RUPIAH</td>
                </tr>
                <tr>
                    <td class="text-start" colspan="2"><u style="color: red">CATATAN :</u></td>
                    <td class="bg-red">Pembukuan</td>
                    <td class="bg-red">Mengetahui</td>
                    <td class="bg-red" colspan="2" style="width: 100px">Kasir</td>
                    <td class="bg-red">Penerima</td>
                </tr>
            </thead>
            <tbody>
                <tr style="border: none !important; height:25px">
                    <td colspan="2"></td>
                    <td></td>
                    <td></td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script>
        // window.print();
    </script>
@endsection
