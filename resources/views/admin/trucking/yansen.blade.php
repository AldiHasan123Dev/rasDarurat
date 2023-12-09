@extends('layouts.admin')
@section('style')
    <style>
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');
            @page {
                size: 210mm 297mm;
                margin: .5cm 0cm 0cm 0cm;
            }
            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
            }
            #print, #print * {
                visibility: visible;
            }
            th, td{
                font-size: .7rem !important;
            }
            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -60px;
                display: block;
            }
            #table td, #table th{
                border: 1px solid black;
            }
            #print {
                color: #000;
            }
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <form action="{{ route('trucking.invoice.yansen') }}" method="get">
                        <div class="row">
                            <div class="col-3 mb-2">
                                <label for="start">Tanggal Awal</label>
                                <input type="date" name="start" id="start" class="form-control" value="{{ $start }}">
                            </div>
                            <div class="col-3 mb-2">
                                <label for="end">Tanggal Akhir</label>
                                <input type="date" name="end" id="end" class="form-control" value="{{ $end }}">
                            </div>
                            <div class="col-3 mb-2">
                                <button class="btn btn-sm btn-primary mt-4" type="submit">Filter</button>
                                <button class="btn btn-sm btn-success mt-4 ml-2" onclick="window.print()" type="button">Print</button>
                            </div>
                        </div>
                    </form>
                    <div id="print">
                        <h4 class="text-center">LAPORAN TAGIHAN TRUCKING PERIODE {{ date('d/m/Y',strtotime($start)) }} - {{ date('d/m/Y',strtotime($end)) }}</h4>
                        <div class="mt-2">
                            <b>BONGKAR : {{ $data1->count() }}</b>
                            <table class="table table-sm w-100 table-bordered" style="white-space: nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Tgl Muat</th>
                                        <th>Nopol</th>
                                        <th>No. Cont</th>
                                        <th>Tujuan</th>
                                        <th>Tipe</th>
                                        <th>Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total1 = 0;
                                    @endphp
                                    @foreach ($data1 as $item)
                                    @php
                                        $tarif = $item->tarif->tarif + $item->tagihans->sum('jumlah');
                                        $total1 += $tarif;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ date('d/m/y', strtotime($item->tgl_muat)) }}</td>
                                        <td>{{ $item->kendaraan->nopol }}</td>
                                        <td>{{ $item->container }}</td>
                                        <td>{{ $item->tarif->tujuan->tujuanInfo->nama ?? '-' }}</td>
                                        <td>{{ $item->tipe }}'</td>
                                        <td>Rp. {{ number_format($tarif) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6"><b>Total</b></td>
                                        <td><b>Rp. {{ number_format($total1) }}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">
                            <b>MUAT : {{ $data2->count() }}</b>
                            <table class="table table-sm w-100 table-bordered" style="white-space: nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Tgl Muat</th>
                                        <th>Nopol</th>
                                        <th>No. Cont</th>
                                        <th>Tujuan</th>
                                        <th>Tipe</th>
                                        <th>Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total2 = 0;
                                    @endphp
                                    @foreach ($data2 as $item)
                                    @php
                                        $tarif = $item->tarif->tarif + $item->tagihans->sum('jumlah');
                                        $total2 += $tarif;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ date('d/m/y', strtotime($item->tgl_muat)) }}</td>
                                        <td>{{ $item->kendaraan->nopol }}</td>
                                        <td>{{ $item->container }}</td>
                                        <td>{{ $item->keterangan_lain ?? '-' }}</td>
                                        <td>{{ $item->tipe }}'</td>
                                        <td>Rp. {{ number_format($tarif) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6"><b>Total</b></td>
                                        <td><b>Rp. {{ number_format($total2) }}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
