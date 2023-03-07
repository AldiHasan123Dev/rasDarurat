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
                    <table class="table table-bordered" style="font-size: .7rem">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Cont / Seal</th>
                                <th>Jenis Barang</th>
                                <th>Koli</th>
                                <th>P</th>
                                <th>L</th>
                                <th>T</th>
                                <th>M3</th>
                                <th>Tgl Masuk</th>
                                <th>Pengirim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($data as $item)
                                @foreach ($item->bttb as $b)
                                    <tr>
                                        <td class="text-center">{{ $i}}</td>
                                        @if ($loop->first)
                                            <td colspan="{{ $item->bttb->count() }}">{{ $item->container }} / {{ $item->seal }}</td>
                                        @endif
                                        <td>{{ $b->barang->nama }}</td>
                                        <td class="text-center">{{ $b->qty }} {{ $b->satuan->nama }}</td>
                                        <td class="text-center">{{ $b->p }}</td>
                                        <td class="text-center">{{ $b->l }}</td>
                                        <td class="text-center">{{ $b->t }}</td>
                                        <td class="text-center">{{ $b->m3 }}</td>
                                        <td>{{ date('d/m/Y', strtotime($b->tgl_masuk)) }}</td>
                                        <td>{{ $b->pengirim->nama }}</td>
                                    </tr>
                                @endforeach
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
