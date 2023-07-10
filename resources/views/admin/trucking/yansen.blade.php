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
            #print, #print * {
                visibility: visible;
            }
            th, td{
                font-size: .8rem !important;
            }
            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -60px;
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
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Nama Cust Pembayar</th>
                                        <th>No. Cont / Seal</th>
                                        <th>Tujuan</th>
                                        <th>Tipe</th>
                                        <th>Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data1 as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $item->customer->nama }}</td>
                                        <td>{{ $item->container }} / {{ $item->seal }}</td>
                                        <td>{{ $item->tarif->tujuan->tujuanInfo->nama ?? '-' }}</td>
                                        <td>{{ $item->tipe }}'</td>
                                        <td>Rp. {{ number_format($item->tarif->tarif) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">
                            <b>MUAT : {{ $data2->count() }}</b>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Nama Cust Pembayar</th>
                                        <th>No. Cont / Seal</th>
                                        <th>Tujuan</th>
                                        <th>Tipe</th>
                                        <th>Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data2 as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $item->customer->nama }}</td>
                                        <td>{{ $item->container }} / {{ $item->seal }}</td>
                                        <td>{{ $item->tarif->tujuan->tujuanInfo->nama ?? '-' }}</td>
                                        <td>{{ $item->tipe }}'</td>
                                        <td>Rp. {{ number_format($item->tarif->tarif) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
