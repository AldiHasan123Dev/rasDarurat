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
            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -80px;
            }
        }
        #table td, #table th{
            border: 1px solid black;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="d-flex" style="gap:5px">
            <a href="{{ route('order.index') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
            <button onclick="window.print()" class="btn btn-sm btn-success mb-3">Print</button>
        </div>
        <div class="card p-3" id="print">
            <div class="header d-flex" style="gap:5px; width:100%">
                <img src="{{ asset('assets/img/ras.png') }}" alt="logo" style="height: 50px; width: 30%" class="img-fluid">
                <div style="width: 40%; margin-left:35px">
                    <table style="font-size:.7rem">
                        <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDERA</td></tr>
                        <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                        <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                    </table>
                </div>
                <div style="width:30%; ">
                    <table style="font-size: .7rem; font-weight:bold">
                        <tr><td>PACKING LIST</td></tr>
                        <tr class="border-top border-dark"><td>{{ $order->job }}</td></tr>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-6">
                    <table style="font-size: .7rem">
                        <tr>
                            <td style="width: 150px">Nama Customer</td>
                            <td>: {{ $order->tarif->customer->nama }}</td>
                        </tr>
                        <tr>
                            <td>Nama Kapal</td>
                            <td>: {{ $order->tarif->jadwal_kapal->kapal->nama }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6">
                    <table style="font-size: .7rem">
                        <tr>
                            <td style="width: 150px">TD</td>
                            <td>: {{ date('d/m/Y',strtotime($order->tarif->jadwal_kapal->td))}}</td>
                        </tr>
                        <tr>
                            <td>Nama Kapal</td>
                            <td>: {{ $order->tarif->tujuan_lokasi->nama }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-12 mt-2">
                    <table class="table table-bordered" id="tables" style="font-size: .7rem">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th>Cont / Seal</th>
                                <th>Jenis Barang</th>
                                <th>Koli</th>
                                <th>Tgl Masuk</th>
                                <th>Pengirim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('bttb.index',['order_id'=>$item->id]) }}">{{ $item->container }} / {{ $item->seal }}</a></td>
                                    <td>{{ $item->barang->nama }}</td>
                                    <td>{{ $item->tarif->satuanInfo->nama }}</td>
                                    <td>{{ date('d/m/Y') }}</td>
                                    <td>{{ $item->pengirim->nama }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
