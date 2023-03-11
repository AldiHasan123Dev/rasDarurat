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
                margin-top: 30px;
            }
            #print, #print * {
                visibility: visible;
                font-family: 'Open Sans', sans-serif;
                font-size: 1rem !important;
                color: black !important;
            }
            #print {
                display: block;
                height: 100%;
                width: 100% !important;
                font-family: 'Open Sans', sans-serif;
                padding: 0;
                margin: 0;
                margin-top: -110px;
            }
            .pagebreak {
                page-break-after: always;
                overflow:hidden;
            }
            .page-number{
                float:right;
                font-style: italic;
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
                @foreach ($data->chunk(15) as $bttb)
                    <div class="page" style="margin-top: 30px">
                        <div class="headers d-flex" style="gap:5px; width:100%">
                            <img src="{{ asset('ras.png') }}" alt="logo" style="height: 50px; width: 30%" class="img-fluid">
                            <div style="width: 40%; margin-left:35px">
                                <table style="font-size:.7rem">
                                    <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDERA</td></tr>
                                    <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                                    <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                                </table>
                            </div>
                            <div style="width:30%; ">
                                <table style="font-size: .7rem; font-weight:bold">
                                    <tr><td class="text-right" style="text-align: right">BTTB</td></tr>
                                    <tr class="border-top border-dark"><td>BUKTI TANDA TERIMA BARANG</td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 200px">No. BTTB</td>
                                        <td>: {{ $order->job }}.{{ sprintf('%02d',$order->no_job) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Kapal</td>
                                        <td>: {{ $order->jadwal_kapal->kapal->nama }} Voy.{{ $order->jadwal_kapal->voyage }}</td>
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
                            <div class="col-12 mt-2" style="{{ $loop->iteration==ceil($data->count()/15)?'height:200px':'' }}">
                                <table class="table nowrap" style="font-size: .7rem !important; border-top: solid 2px black">
                                    <thead>
                                        <tr style="border-bottom: solid 2px black">
                                            <th class="text-center">No. Gudang</th>
                                            <th>Jenis Barang</th>
                                            <th class="text-center">Koli</th>
                                            <th class="text-center">P</th>
                                            <th class="text-center">L</th>
                                            <th class="text-center">T</th>
                                            <th class="text-center">M3</th>
                                            <th class="text-center">Tgl Masuk</th>
                                            <th>Pengirim</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $tgl_masuk = null;
                                        @endphp
                                        @foreach ($bttb->groupBy('pengirim_id') as $b)
                                            @foreach ($b as $item)
                                            <tr>
                                                <td class="text-center">{{ $item->no_gudang }}</td>
                                                <td>{{ ucwords(strtolower($item->barang->nama)) }}</td>
                                                <td class="text-center">{{ $item->qty }} {{ $item->satuan->nama }}</td>
                                                <td class="text-center">{{ $item->p }}</td>
                                                <td class="text-center">{{ $item->l }}</td>
                                                <td class="text-center">{{ $item->t }}</td>
                                                <td class="text-center">{{ $item->vol }}</td>
                                                @if ($tgl_masuk==$item->tgl_masuk)
                                                <td class="text-center">-</td>
                                                @else
                                                <td class="text-center">{{ date('d/m/y', strtotime($item->tgl_masuk)) }}</td>
                                                @endif
                                                @if ($loop->first)
                                                <td>{{ ucfirst(strtolower($item->pengirim->nama)) ?? '-' }}</td>
                                                @else
                                                <td>-</td>
                                                @endif
                                                <td>{{ $item->keterangan }}</td>
                                            </tr>
                                            @php
                                                $tgl_masuk = $item->tgl_masuk;
                                            @endphp
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($loop->last)
                            <div class="col-12">
                                <table class="w-100" style="border: 2px black solid; font-size: .7rem !important">
                                    <tr>
                                        <td>Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                        <td>{{ $order->bttb->sum('qty') }}</td>
                                        <td class="text-center">{{ $order->tarif->customer->nama }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-4 px-1 mt-2">
                                <table style="font-size: .7rem;  margin-left:20px">
                                    <tr>
                                        <td style="width: 100px">Penerima</td>
                                        <td>: </td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal</td>
                                        <td>: </td>
                                    </tr>
                                </table>
                                <div style="margin-top: 70px; margin-left:70px">
                                    <span class="text-center">(....................................................)</span>
                                </div>
                            </div>
                            <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                                <p>Pengantar</p>
                                <div style="margin-top: 95px">
                                    <span class="text-center">(....................................................)</span>
                                </div>
                            </div>
                            <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                                <p> Surabaya, {{ date('d F Y', strtotime($item->tgl_masuk)) }}</p>
                                <div style="margin-top: 90px">
                                    <span class="text-left">({{ $order->tarif->customer->marketing->name }})</span>
                                </div>
                            </div>
                            <hr class="mt-4">
                            @endif
                        </div>
                        <div class="page-number" style="font-size: .7rem"><i>Page {{ $loop->iteration }} of {{ ceil($data->count()/15) }}</i></div>
                    </div>
                    <p class="pagebreak"></p>
                @endforeach
            </div>
        </div>
    </div>
@endsection
