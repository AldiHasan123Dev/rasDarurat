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
            <a href="{{ route('jadwalkapal.index') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
            <button onclick="window.print()" class="btn btn-sm btn-success mb-3">Print</button>
        </div>
        <div class="cards p-3">
            <div id="print" style="width: 100%">
                <div class="header d-flex" style="gap:5px; width:100%">
                    <img src="{{ asset('assets/img/ras.png') }}" alt="logo" style="height: 50px; width: 30%" class="img-fluid">
                    <div style="width: 40%; margin-left:35px">
                        <table style="font-size:.7rem">
                            <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDERA</td></tr>
                            <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                            <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                        </table>
                    </div>
                    {{-- <div style="width:30%; ">
                        <table style="font-size: .7rem; font-weight:bold">
                            <tr><td>BTTB</td></tr>
                            <tr class="border-top border-dark"><td>BUKTI TANDA TERIMA BARANG</td></tr>
                        </table>
                    </div> --}}
                </div>
                <hr>
                <div class="row">
                    <div class="col-4">
                        <table style="font-size: .7rem">
                            <tr>
                                <td style="width: 60px">To</td>
                                <td>: {{ $jadwal_kapal->pelayaran->nama }} </td>
                            </tr>
                            <tr>
                                <td>Attn.</td>
                                <td>: {{ $jadwal_kapal->pelayaran->pic }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 mt-2">
                        <p class="text-center"><u>SI {{ strtoupper($jadwal_kapal->kapal->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }} ETD {{ date('d F Y', strtotime($jadwal_kapal->etd)) }}</u></p>
                        <table class="table table-bordered nowrap" style="font-size: .7rem">
                            <thead>
                                <tr>
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">No.</th>
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">No. Container / Seal</th>
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">Koli</th>
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">Barang</th>
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">Stuffing</th>
                                    <th class="text-center" style="vertical-align: middle" colspan="2">Pembagian BL</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Pengirim</th>
                                    <th class="text-center">Penerima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td><a href="{{ route('bttb.index',['order_id'=>$item->id]) }}">{{ $item->container }} / {{ $item->seal }}</a></td>
                                        <td class="text-center">{{ $item->tarif->satuanInfo->nama }}</td>
                                        <td class="text-center">{{ $item->barang->nama }}</td>
                                        <td class="text-center">LUAR</td>
                                        <td class="text-center">{{ $item->pengirim->nama ?? '-'}}</td>
                                        <td class="text-center">{{ $item->penerima->nama ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
