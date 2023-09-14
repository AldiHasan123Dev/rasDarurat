@extends('layouts.admin')
@section('style')
    <style>
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');

            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
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
                position: relative;
                left: 0;
                /* top: -20px; */
            }

            #table td,
            #table th {
                border: 1px solid black;
            }

            #print {
                color: #000;
            }

            .page-break {
                page-break-after: always;
                overflow: hidden;
            }
        }

        tr.heading td {
            border: 1px solid black;
            text-align: center;
        }

        .table tr td {
            vertical-align: middle;
            padding: 3px 3px;
            border: 1px solid black;
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
    <div class="container">
        <div class="card p-3 mt-3">
            <div id="print">
                <div class="invoice-box first-page">
                    <div class="header d-flex" style="gap:5px; width:100%">
                        <div style="width: 100%;">
                            <table style="font-size:1.2rem; width: 100%;">
                                <tr>
                                    <td class="fw-bold" style="text-align: center">BUKTI BANK KELUAR</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-8">
                            <table style="font-size: .8rem">
                                <tr>
                                    <td style="width: 170px">DIBAYARKAN KEPADA</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ $pelayaran->tarif_pelayaran->pelayaran->nama }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table style="font-size: .8rem">
                                <tr>
                                    <td style="width: 120px">NAMA</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ Auth::user()->name }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 120px">TANGGAL</td>
                                    <td style=" width:5px">:</td>
                                    <td>{{ date('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <table class="mt-2 table" style="font-size: .7rem; width:100%; border:1px solid black;">
                                <thead>
                                    <tr class="heading table-primary" style="height: 25px">
                                        <td colspan="3" class="text-center fw-bold text-uppercase">{{ $pelayaran->tarif_pelayaran->pelayaran->nama }}</td>
                                    </tr>
                                    <tr class="heading table-warning">
                                        <td class="fw-bold text-uppercase" style="width: 100px">ID JOB</td>
                                        <td class="fw-bold text-uppercase" style="width: 50%">Uraian</td>
                                        <td class="fw-bold text-uppercase" style="width: 50%">Jumlah</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $job)
                                        @foreach ($job as $item)
                                            <tr>
                                                <td rowspan="5" class="vertical">{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                                <td>OPP (1X{{ preg_replace("/[^0-9]/", "", $item->order->tarif->shipmentInfo->nama ) }}) {{ $item->order->tarif->customer->nama }} ({{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }})</td>
                                                <td><input type="text" style="width: 100%; padding:5px; border:1px solid gray" name="" id=""></td>
                                            </tr>
                                            <tr>
                                                <td>THC LoLo SBY</td>
                                                <td><input type="text" style="width: 100%; padding:5px; border:1px solid gray" name="" id=""></td>
                                            </tr>
                                            <tr>
                                                <td>APBS</td>
                                                <td><input type="text" style="width: 100%; padding:5px; border:1px solid gray" name="" id=""></td>
                                            </tr>
                                            <tr>
                                                <td>Cleaning</td>
                                                <td><input type="text" style="width: 100%; padding:5px; border:1px solid gray" name="" id=""></td>
                                            </tr>
                                            <tr>
                                                <td>LSS  (1X{{ preg_replace("/[^0-9]/", "", $item->order->tarif->shipmentInfo->nama ) }}) {{ $item->order->tarif->customer->nama }} ({{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }})</td>
                                                <td><input type="text" style="width: 100%; padding:5px; border:1px solid gray" name="" id=""></td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">NOMINAL BG</td>
                                        <td><input type="text" style="width: 100%; padding:5px; border:1px solid gray" name="" id=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">NO. BG</td>
                                        <td><input type="text" style="width: 100%; padding:5px; border:1px solid gray" name="" id=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">TANGGAL BG</td>
                                        <td><input type="date" style="width: 100%; padding:5px; border:1px solid gray" name="" id=""></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- <div class="row mt-3">
                        <div class="col-9">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 100px">Terbilang</td>
                                    <td>: {{ strtoupper(terbilang($total)) }} RUPIAH</td>
                                </tr>
                                <tr>
                                    <td>Keterangan</td>
                                    <td>: </td>
                                </tr>
                            </table>
                            <table style="font-size: .7rem" class="mt-2">
                            </table>
                        </div>
                        <div class="col-3">
                            <div class="text-center" style="font-size: .7rem">
                                <p>Surabaya,
                                    {{ is_null($order->tgl_invoice) ? '-' : tanggal($order->tgl_invoice) }}
                                </p>
                                <div style="height: 1.5cm"></div>
                                (<input type="text" value="Totok" class="text-center"
                                    style="border:none; width:130px" />)
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
