@extends('layouts.admin')
@section('style')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@800&display=swap');
    #print *{
        font-family: 'Open Sans', sans-serif;
    }
    @media print {
            @page {
                size: 8.5in 5.5in;
                margin: 0cm .5cm 0cm .5cm;
            }
            body * {
                visibility: hidden;
            }
            body{
                width: 100%;
            }
            #print .header{
                margin-top: 10px;
            }
            #print, #print * {
                visibility: visible;
                font-family: 'Open Sans', sans-serif;
                font-size: 1rem !important;
                color: black !important;
            }
            #print {
                height: 100%;
                width: 100% !important;
                position: absolute;
                left: 0;
                top: -80px;
                font-family: 'Open Sans', sans-serif;
                padding: 0;
                margin: 0;
            }
        }
    .table>:not(caption)>*>*{
        padding: 0px 5px !important;
    }
    .table tr td{
        border: none !important;
    }
</style>
@endsection
@section('content')
    <div class="container">
        <div class="d-flex" style="gap:5px">
            <a href="{{ route('order.index') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
            <button onclick="window.print()" class="btn btn-sm btn-success mb-3">Print</button>
        </div>
        <div class="card p-3">
            <div id="print">
                <div class="headers d-flex" style="gap:5px; width:100%">
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
                            <tr><td>BTTB</td></tr>
                            <tr class="border-top border-dark"><td>BUKTI TANDA TERIMA BARANG</td></tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <table style="font-size: .7rem">
                            <tr>
                                <td style="width: 200px">No. BTTB</td>
                                <td>: {{ $order->job }}.{{ sprintf('%02d',$order->no_job) }}</td>
                            </tr>
                            <tr>
                                <td>Nama Kapal</td>
                                <td>: {{ $order->jadwal_kapal->kapal->nama }}</td>
                            </tr>
                            <tr>
                                <td>No. Container</td>
                                <td>: {{ $order->container }}</td>
                            </tr>
                            <tr>
                                <td>No. Seal</td>
                                <td>: {{ $order->seal }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-6">
                        <table style="font-size: .7rem">
                            <tr>
                                <td style="width: 200px">Penerima</td>
                                <td>: {{ $order->penerima->nama }}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>: {{ $order->penerima->alamat }}</td>
                            </tr>
                            <tr>
                                <td>Kota</td>
                                <td>: {{ $order->penerima->kota }}</td>
                            </tr>
                            <tr>
                                <td>HP</td>
                                <td>: {{ $order->penerima->hp }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 mt-2">
                        <table class="table nowrap border-dark border-y" style="font-size: .7rem !important">
                            <thead>
                                <tr class="border-dark border-bottom">
                                    <th class="text-center">No. Gudang</th>
                                    <th>Jenis Barang</th>
                                    <th class="text-center">Koli</th>
                                    <th class="text-center">Tgl Masuk</th>
                                    <th>Pengirim</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td class="text-center">{{ $item->no_gudang }}</td>
                                        <td>{{ ucwords(strtolower($item->barang->nama)) }}</td>
                                        <td class="text-center">{{ $item->qty }} {{ $item->satuan->nama }}</td>
                                        <td class="text-center">{{ date('d/m/Y', strtotime($item->tgl_masuk)) }}</td>
                                        <td>{{ $item->pengirim->nama ?? '-' }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6 mt-2">
                        <table style="font-size: .7rem">
                            <tr>
                                <td style="width: 100px">Penerima</td>
                                <td>: {{ $order->penerima->nama }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td>: </td>
                            </tr>
                        </table>
                        <div style="margin-top: 70px; margin-left:70px">
                            <span class="text-center">({{ $order->penerima->nama }})</span>
                        </div>
                    </div>
                    <div class="col-6 mt-2">
                        <table style="font-size: .7rem">
                            <tr>
                                <td> Surabaya, {{ date('d F Y', strtotime($item->tgl_masuk)) }}</td>
                            </tr>
                        </table>
                        <div style="margin-top: 90px">
                            <span class="text-center">({{ $order->tarif->customer->marketing->name }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
