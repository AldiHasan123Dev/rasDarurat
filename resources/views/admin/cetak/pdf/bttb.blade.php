@extends('layouts.iframe')
@section('style')
<link rel="stylesheet" href="{{ asset('assets/font/font.css') }}">
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
                -webkit-print-color-adjust: exact;
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
                font-size: .7rem !important;
                color: black !important;
            }
            #print {
                display: block;
                height: 100%;
                width: 100% !important;
                font-family: 'Open Sans', sans-serif;
                padding: 0;
                margin: 0;
                /* margin-top: 20px; */
            }
            .pagebreak {
                page-break-after: always;
                overflow:hidden;
            }
            .page-number{
                float:right;
                font-style: italic;
                position: relative;
                bottom: 0px;
                right: 0px;
            }
        }
    .table>:not(caption)>*>*{
        padding: 0px 5px !important;
    }
    .table tr td{
        border: none !important;
    }
    .page-number{
        float:right;
        font-style: italic;
        position: absolute;
        bottom: 0px;
        right: 0px;
    }
    .page{
        position: relative;
        height: 12cm;
        /* height: 5in !important; */
    }
</style>
@endsection
@section('content')
@php
$page = 1;
@endphp
<div id="print">
@if ($data->count()>=17)
    @foreach ($data->chunk(17) as $bttb)
        @if ($loop->last)
            @if ($bttb->count()>=12)
                @foreach ($bttb->chunk(12) as $bttb)
                    <div class="page" style="margin-top: 0px; margin-right:60px">
                        <x-header-cop>
                            <div style="width:22%; text-align:right">
                                <table style="font-size: .7rem; font-weight:bold; width:100%; margin-right:5px">
                                    <tr><td class="text-right" style="text-align: right">BTTB</td></tr>
                                    <tr class="border-top border-dark"><td>BUKTI TANDA TERIMA BARANG</td></tr>
                                </table>
                            </div>
                        </x-header-cop>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div class="s" style="width: 50%">
                                        <table style="font-size: .7rem; white-space:nowrap">
                                            <tr>
                                                <td style="width: 100px">No. BTTB</td>
                                                <td>: {{ $order->job }}.{{ sprintf('%02d',$order->no_job) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama Kapal</td>
                                                <td>: {{ $order->jadwal_kapal->kapal->nama }} Voy.{{ $order->jadwal_kapal->voyage }}</td>
                                            </tr>
                                            <tr>
                                                <td>No. Container</td>
                                                <td>: {{ $order->container }} - {{ $order->tarif->shipmentInfo->nama ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>No. Seal</td>
                                                <td>: {{ $order->seal }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="s">
                                        <table style="font-size: .7rem;">
                                            <tr>
                                                <td style="width: 100px">Penerima</td>
                                                <td>: {{ $order->penerima->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td>Alamat</td>
                                                <td class="col-alamat">: {{ $order->penerima->alamat }}</td>
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
                                </div>
                            </div>
                            <div class="col-12 mt-2" style="{{ $loop->iteration==ceil($data->count()/15)?'height:200px':'' }}">
                                <table class="table nowrap" style="font-size: .7rem !important; border-top: solid 2px black">
                                    <thead>
                                        <tr style="border: solid 2px black">
                                            <td class="text-center">No. Gudang</td>
                                            <td>Jenis Barang</td>
                                            <td class="text-center">Koli</td>
                                            @if (request('berat'))
                                            <td class="text-center">Berat</td>
                                            @endif
                                            <td class="text-center">Tgl Masuk</td>
                                            <td>Pengirim</td>
                                            <td>Keterangan</td>
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
                                                @if(request('berat'))
                                                <td class="text-center">{{ number_format($item->berat ?? 0) }} Kg</td>
                                                @endif
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
                                        @if ($loop->last)
                                        <tr style="border: 2px solid black">
                                            <td colspan="2">Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                            <td class="text-center">{{ $order->bttb->sum('qty') }}</td>
                                            @if (request('berat'))
                                            <td class="text-center">{{ number_format($order->bttb->sum('berat')) }}</td>
                                            @endif
                                            <td colspan="3" class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if ($loop->last)
                                {{-- <div class="col-12">
                                    <table class="w-100" style="border: 2px black solid; font-size: .7rem !important">
                                        <tr>
                                            <td>Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                            <td style="width: 100px"></td>
                                            <td>{{ $order->bttb->sum('qty') }}</td>
                                            <td class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                        </tr>
                                    </table>
                                </div> --}}
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
                                        <span class="text-center" style="font-size: .7rem">(....................................................)</span><br>
                                        <span class="text-center" style="font-size: .7rem">Stampel + TTD + Nama</span>
                                    </div>
                                </div>
                                <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                                    {{-- <p>Pengantar</p>
                                    <div style="margin-top: 95px">
                                        <span class="text-center">(....................................................)</span>
                                    </div> --}}
                                </div>
                                <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                                    <p> Surabaya, {{ date('d F Y', strtotime($order->jadwal_kapal->etd)) }}</p>
                                    <div style="margin-top: 90px">
                                        <span class="text-left">({{ Auth::user()->name }})</span>
                                    </div>
                                </div>
                            @endif

                        </div>
                        @if (ceil($data->count()/10)!=1)
                            <div class="page-number" style="font-size: .7rem"><i>Page {{ $page }} of <span class="off-page"></span></i></div>
                        @endif
                    </div>
                    @if (ceil($data->count()/12)!=1)
                        @if (!$loop->last)
                            <p class="pagebreak"></p>
                            @php
                                $page++;
                            @endphp
                        @endif
                    @endif
                @endforeach
            @else
                @foreach ($bttb->chunk(17) as $bttb)
                    <div class="page" style="margin-top: 0px; margin-right:60px">
                        <x-header-cop>
                            <div style="width:22%; text-align:right">
                                <table style="font-size: .7rem; font-weight:bold; width:100%; margin-right:5px">
                                    <tr><td class="text-right" style="text-align: right">BTTB</td></tr>
                                    <tr class="border-top border-dark"><td>BUKTI TANDA TERIMA BARANG</td></tr>
                                </table>
                            </div>
                        </x-header-cop>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div class="s" style="width: 50%">
                                        <table style="font-size: .7rem; white-space:nowrap">
                                            <tr>
                                                <td style="width: 100px">No. BTTB</td>
                                                <td>: {{ $order->job }}.{{ sprintf('%02d',$order->no_job) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nama Kapal</td>
                                                <td>: {{ $order->jadwal_kapal->kapal->nama }} Voy.{{ $order->jadwal_kapal->voyage }}</td>
                                            </tr>
                                            <tr>
                                                <td>No. Container</td>
                                                <td>: {{ $order->container }} - {{ $order->tarif->shipmentInfo->nama ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>No. Seal</td>
                                                <td>: {{ $order->seal }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="s">
                                        <table style="font-size: .7rem;">
                                            <tr>
                                                <td style="width: 100px">Penerima</td>
                                                <td>: {{ $order->penerima->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td>Alamat</td>
                                                <td class="col-alamat">: {{ $order->penerima->alamat }}</td>
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
                                </div>
                            </div>
                            <div class="col-12 mt-2" style="{{ $loop->iteration==ceil($data->count()/15)?'height:200px':'' }}">
                                <table class="table nowrap" style="font-size: .7rem !important; border-top: solid 2px black">
                                    <thead>
                                        <tr style="border-bottom: solid 2px black">
                                            <td class="text-center">No. Gudang</td>
                                            <td>Jenis Barang</td>
                                            <td class="text-center">Koli</td>
                                            @if (request('berat'))
                                            <td class="text-center">Berat</td>
                                            @endif
                                            <td class="text-center">Tgl Masuk</td>
                                            <td>Pengirim</td>
                                            <td>Keterangan</td>
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
                                                @if(request('berat'))
                                                <td class="text-center">{{ number_format($item->berat ?? 0) }} Kg</td>
                                                @endif
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
                                        @if ($loop->last)
                                        <tr style="border: 2px solid black">
                                            <td colspan="2">Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                            <td class="text-center">{{ $order->bttb->sum('qty') }}</td>
                                            @if (request('berat'))
                                            <td class="text-center">{{ number_format($order->bttb->sum('berat')) }}</td>
                                            @endif
                                            <td colspan="3" class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if ($loop->last)
                                {{-- <div class="col-12">
                                    <table class="w-100" style="border: 2px black solid; font-size: .7rem !important">
                                        <tr>
                                            <td>Kondisissss: {{ $order->tarif->kondisiInfo->nama }}</td>
                                            <td style="width: 100px"></td>
                                            <td>{{ $order->bttb->sum('qty') }}</td>
                                            <td class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                        </tr>
                                    </table>
                                </div> --}}
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
                                        <span class="text-center" style="font-size: .7rem">(....................................................)</span><br>
                                        <span class="text-center" style="font-size: .7rem">Stampel + TTD + Nama</span>
                                    </div>
                                </div>
                                <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                                    {{-- <p>Pengantar</p>
                                    <div style="margin-top: 95px">
                                        <span class="text-center">(....................................................)</span>
                                    </div> --}}
                                </div>
                                <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                                    <p> Surabaya, {{ date('d F Y', strtotime($order->jadwal_kapal->etd)) }}</p>
                                    <div style="margin-top: 90px">
                                        <span class="text-left">({{ Auth::user()->name }})</span>
                                    </div>
                                </div>
                            @endif

                        </div>
                        @if (ceil($data->count()/15)!=1)
                            <div class="page-number" style="font-size: .7rem"><i>Page {{ $page }} of <span class="off-page"></span></i></div>
                        @endif
                    </div>
                    @if (ceil($data->count()/15)!=1)
                        @if (!$loop->last)
                            <p class="pagebreak"></p>
                            @php
                                $page++;
                            @endphp
                        @endif
                    @endif
                @endforeach
            @endif
        @else
            <div class="page" style="margin-top: 0px; margin-right:60px">
                <x-header-cop>
                    <div style="width:22%; text-align:right">
                        <table style="font-size: .7rem; font-weight:bold; width:100%; margin-right:5px">
                            <tr><td class="text-right" style="text-align: right">BTTB</td></tr>
                            <tr class="border-top border-dark"><td>BUKTI TANDA TERIMA BARANG</td></tr>
                        </table>
                    </div>
                </x-header-cop>
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div class="s" style="width: 50%">
                                <table style="font-size: .7rem; white-space:nowrap">
                                    <tr>
                                        <td style="width: 100px">No. BTTB</td>
                                        <td>: {{ $order->job }}.{{ sprintf('%02d',$order->no_job) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Kapal</td>
                                        <td>: {{ $order->jadwal_kapal->kapal->nama }} Voy.{{ $order->jadwal_kapal->voyage }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Container</td>
                                        <td>: {{ $order->container }} - {{ $order->tarif->shipmentInfo->nama ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Seal</td>
                                        <td>: {{ $order->seal }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="s">
                                <table style="font-size: .7rem;">
                                    <tr>
                                        <td style="width: 100px">Penerima</td>
                                        <td>: {{ $order->penerima->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td class="col-alamat">: {{ $order->penerima->alamat }}</td>
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
                        </div>
                    </div>
                    <div class="col-12 mt-2" style="{{ $loop->iteration==ceil($data->count()/15)?'height:200px':'' }}">
                        <table class="table nowrap" style="font-size: .7rem !important; border-top: solid 2px black">
                            <thead>
                                <tr style="border-bottom: solid 2px black">
                                    <td class="text-center">No. Gudang</td>
                                    <td>Jenis Barang</td>
                                    <td class="text-center">Koli</td>
                                    @if (request('berat'))
                                    <td class="text-center">Berat</td>
                                    @endif
                                    <td class="text-center">Tgl Masuk</td>
                                    <td>Pengirim</td>
                                    <td>Keterangan</td>
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
                                        @if(request('berat'))
                                        <td class="text-center">{{ number_format($item->berat ?? 0) }} Kg</td>
                                        @endif
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
                                @if ($loop->last)
                                <tr style="border: 2px solid black">
                                    <td colspan="2">Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                    <td class="text-center">{{ $order->bttb->sum('qty') }}</td>
                                    @if (request('berat'))
                                    <td class="text-center">{{ number_format($order->bttb->sum('berat')) }}</td>
                                    @endif
                                    <td colspan="3" class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if ($loop->last)
                        {{-- <div class="col-12">
                            <table class="w-100" style="border: 2px black solid; font-size: .7rem !important">
                                <tr>
                                    <td>Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                    <td style="width: 100px"></td>
                                    <td>{{ $order->bttb->sum('qty') }}</td>
                                    <td class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                </tr>
                            </table>
                        </div> --}}
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
                            <div style="margin-top: 70px; margin-left:70px" style="font-size: .7rem">
                                <span class="text-center">(....................................................)</span><br>
                                <span class="text-center" style="font-size: .7rem">Stampel + TTD + Nama</span>
                            </div>
                        </div>
                        <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                            {{-- <p>Pengantar</p>
                            <div style="margin-top: 95px">
                                <span class="text-center">(....................................................)</span>
                            </div> --}}
                        </div>
                        <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                            <p> Surabaya, {{ date('d F Y', strtotime($order->jadwal_kapal->etd)) }}</p>
                            <div style="margin-top: 90px">
                                <span class="text-left">({{ Auth::user()->name }})</span>
                            </div>
                        </div>
                    @endif

                </div>
                @if (ceil($data->count()/15)!=1)
                    <div class="page-number" style="font-size: .7rem"><i>Page {{ $page }} of <span class="off-page"></span></i></div>
                @endif
            </div>
        @endif
        @if (ceil($data->count()/15)!=1)
            @if (!$loop->last)
                <p class="pagebreak"></p>
                @php
                    $page++;
                @endphp
            @endif
        @endif
    @endforeach
@else
    @if ($data->count()>10)
        @foreach ($data->chunk(10) as $bttb)
            <div class="page" style="margin-top: 0px; margin-right:60px">
                <x-header-cop>
                    <div style="width:22%; text-align:right">
                        <table style="font-size: .7rem; font-weight:bold; width:100%; margin-right:5px">
                            <tr><td class="text-right" style="text-align: right">BTTB</td></tr>
                            <tr class="border-top border-dark"><td>BUKTI TANDA TERIMA BARANG</td></tr>
                        </table>
                    </div>
                </x-header-cop>
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div class="s" style="width: 50%">
                                <table style="font-size: .7rem; white-space:nowrap">
                                    <tr>
                                        <td style="width: 100px">No. BTTB</td>
                                        <td>: {{ $order->job }}.{{ sprintf('%02d',$order->no_job) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Kapal</td>
                                        <td>: {{ $order->jadwal_kapal->kapal->nama }} Voy.{{ $order->jadwal_kapal->voyage }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Container</td>
                                        <td>: {{ $order->container }} - {{ $order->tarif->shipmentInfo->nama ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Seal</td>
                                        <td>: {{ $order->seal }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="s">
                                <table style="font-size: .7rem;">
                                    <tr>
                                        <td style="width: 100px">Penerima</td>
                                        <td>: {{ $order->penerima->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td class="col-alamat">: {{ $order->penerima->alamat }}</td>
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
                        </div>
                    </div>
                    <div class="col-12 mt-2" style="{{ $loop->iteration==ceil($data->count()/15)?'height:200px':'' }}">
                        <table class="table nowrap" style="font-size: .7rem !important; border-top: solid 2px black">
                            <thead>
                                <tr style="border-bottom: solid 2px black">
                                    <td class="text-center">No. Gudang</td>
                                    <td>Jenis Barang</td>
                                    <td class="text-center">Koli</td>
                                    @if (request('berat'))
                                    <td class="text-center">Berat</td>
                                    @endif
                                    <td class="text-center">Tgl Masuk</td>
                                    <td>Pengirim</td>
                                    <td>Keterangan</td>
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
                                        @if(request('berat'))
                                        <td class="text-center">{{ number_format($item->berat ?? 0) }} Kg</td>
                                        @endif
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
                                @if ($loop->last)
                                <tr style="border: 2px solid black">
                                    <td colspan="2">Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                    <td class="text-center">{{ $order->bttb->sum('qty') }}</td>
                                    @if (request('berat'))
                                    <td class="text-center">{{ number_format($order->bttb->sum('berat')) }}</td>
                                    @endif
                                    <td colspan="3" class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if ($loop->last)
                        {{-- <div class="col-12">
                            <table class="w-100" style="border: 2px black solid; font-size: .7rem !important">
                                <tr>
                                    <td>Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                    <td style="width: 100px"></td>
                                    <td>{{ $order->bttb->sum('qty') }}</td>
                                    <td class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                </tr>
                            </table>
                        </div> --}}
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
                            <div style="margin-top: 70px; margin-left:70px" style="font-size: .7rem">
                                <span class="text-center">(....................................................)</span><br>
                                <span class="text-center" style="font-size: .7rem">Stampel + TTD + Nama</span>
                            </div>
                        </div>
                        <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                            {{-- <p>Pengantar</p>
                            <div style="margin-top: 95px">
                                <span class="text-center">(....................................................)</span>
                            </div> --}}
                        </div>
                        <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                            <p> Surabaya, {{ date('d F Y', strtotime($order->jadwal_kapal->etd)) }}</p>
                            <div style="margin-top: 90px">
                                <span class="text-left">({{ Auth::user()->name }})</span>
                            </div>
                        </div>
                    @endif

                </div>
                @if (ceil($data->count()/10)!=1)
                    <div class="page-number" style="font-size: .7rem"><i>Page {{ $page }} of <span class="off-page"></span></i></div>
                @endif
            </div>
            @if (ceil($data->count()/10)!=1)
                @if (!$loop->last)
                    <p class="pagebreak"></p>
                    @php
                        $page++;
                    @endphp
                @endif
            @endif
        @endforeach
    @else
        @if ($data->count()==10)
            @foreach ($data->chunk(10) as $bttb)
            <div class="page" style="margin-top: 0px; margin-right:60px; height:100%; position: absolute;">
                <x-header-cop>
                    <div style="width:22%; text-align:right">
                        <table style="font-size: .7rem; font-weight:bold; width:100%; margin-right:5px">
                            <tr><td class="text-right" style="text-align: right">BTTB</td></tr>
                            <tr class="border-top border-dark"><td>BUKTI TANDA TERIMA BARANG</td></tr>
                        </table>
                    </div>
                </x-header-cop>
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div class="s" style="width: 50%">
                                <table style="font-size: .7rem; white-space:nowrap">
                                    <tr>
                                        <td style="width: 100px">No. BTTB</td>
                                        <td>: {{ $order->job }}.{{ sprintf('%02d',$order->no_job) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Kapal</td>
                                        <td>: {{ $order->jadwal_kapal->kapal->nama }} Voy.{{ $order->jadwal_kapal->voyage }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Container</td>
                                        <td>: {{ $order->container }} - {{ $order->tarif->shipmentInfo->nama ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Seal</td>
                                        <td>: {{ $order->seal }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="s">
                                <table style="font-size: .7rem;">
                                    <tr>
                                        <td style="width: 100px">Penerima</td>
                                        <td>: {{ $order->penerima->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td class="col-alamat">: {{ $order->penerima->alamat }}</td>
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
                        </div>
                    </div>
                    <div class="col-12 mt-2" style="{{ $loop->iteration==ceil($data->count()/15)?'height:200px':'' }}">
                        <table class="table nowrap" style="font-size: .7rem !important; border-top: solid 2px black">
                            <thead>
                                <tr style="border-bottom: solid 2px black">
                                    <td class="text-center">No. Gudang</td>
                                    <td>Jenis Barang</td>
                                    <td class="text-center">Koli</td>
                                    @if (request('berat'))
                                    <td class="text-center">Berat</td>
                                    @endif
                                    <td class="text-center">Tgl Masuk</td>
                                    <td>Pengirim</td>
                                    <td>Keterangan</td>
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
                                        @if(request('berat'))
                                        <td class="text-center">{{ number_format($item->berat ?? 0) }} Kg</td>
                                        @endif
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
                                @if ($loop->last)
                                <tr style="border: 2px solid black">
                                    <td colspan="2">Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                    <td class="text-center">{{ $order->bttb->sum('qty') }}</td>
                                    @if (request('berat'))
                                    <td class="text-center">{{ number_format($order->bttb->sum('berat')) }}</td>
                                    @endif
                                    <td colspan="3" class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-end mt-5" style="font-size: .7rem; position: relative; right:0px; bottom:0px"><i>Page 1 of <span>2</span></i></div>
            </div>
            <p class="pagebreak"></p>
            <div style="margin-top: 30px; margin-right:60px;">
                <div class="row">
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
                        <div style="margin-top: 70px; margin-left:70px" style="font-size: .7rem">
                            <span class="text-center">(....................................................)</span><br>
                            <span class="text-center" style="font-size: .7rem">Stampel + TTD + Nama</span>
                        </div>
                    </div>
                    <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                        {{-- <p>Pengantar</p>
                        <div style="margin-top: 95px">
                            <span class="text-center">(....................................................)</span>
                        </div> --}}
                    </div>
                    <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                        <p> Surabaya, {{ date('d F Y', strtotime($order->jadwal_kapal->etd)) }}</p>
                        <div style="margin-top: 90px">
                            <span class="text-left">({{ Auth::user()->name }})</span>
                        </div>
                    </div>
                </div>
                <div class="page-number" style="font-size: .7rem"><i>Page 2 of <span>2</span></i></div>
            </div>
        @endforeach
        @else
            @foreach ($data->chunk(17) as $bttb)
            <div class="page" style="margin-top: 0px; margin-right:60px; height:100%; position: absolute;">
                <x-header-cop>
                    <div style="width:22%; text-align:right">
                        <table style="font-size: .7rem; font-weight:bold; width:100%; margin-right:5px">
                            <tr><td class="text-right" style="text-align: right">BTTB</td></tr>
                            <tr class="border-top border-dark"><td>BUKTI TANDA TERIMA BARANG</td></tr>
                        </table>
                    </div>
                </x-header-cop>
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div class="s" style="width: 50%">
                                <table style="font-size: .7rem; white-space:nowrap">
                                    <tr>
                                        <td style="width: 100px">No. BTTB</td>
                                        <td>: {{ $order->job }}.{{ sprintf('%02d',$order->no_job) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Kapal</td>
                                        <td>: {{ $order->jadwal_kapal->kapal->nama }} Voy.{{ $order->jadwal_kapal->voyage }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Container</td>
                                        <td>: {{ $order->container }} - {{ $order->tarif->shipmentInfo->nama ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Seal</td>
                                        <td>: {{ $order->seal }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="s">
                                <table style="font-size: .7rem;">
                                    <tr>
                                        <td style="width: 100px">Penerima</td>
                                        <td>: {{ $order->penerima->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td class="col-alamat">: {{ $order->penerima->alamat }}</td>
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
                        </div>
                    </div>
                    <div class="col-12 mt-2" style="{{ $loop->iteration==ceil($data->count()/15)?'height:200px':'' }}">
                        <table class="table nowrap" style="font-size: .7rem !important; border-top: solid 2px black">
                            <thead>
                                <tr style="border-bottom: solid 2px black">
                                    <td class="text-center">No. Gudang</td>
                                    <td>Jenis Barang</td>
                                    <td class="text-center">Koli</td>
                                    @if (request('berat'))
                                    <td class="text-center">Berat</td>
                                    @endif
                                    <td class="text-center">Tgl Masuk</td>
                                    <td>Pengirim</td>
                                    <td>Keterangan</td>
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
                                        @if(request('berat'))
                                        <td class="text-center">{{ number_format($item->berat ?? 0) }} Kg</td>
                                        @endif
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
                                @if ($loop->last)
                                <tr style="border: 2px solid black">
                                    <td colspan="2">Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                    <td class="text-center">{{ $order->bttb->sum('qty') }}</td>
                                    @if (request('berat'))
                                    <td class="text-center">{{ number_format($order->bttb->sum('berat')) }}</td>
                                    @endif
                                    <td colspan="3" class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if ($loop->last)
                        {{-- <div class="col-12">
                            <table class="w-100" style="border: 2px black solid; font-size: .7rem !important">
                                <tr>
                                    <td>Kondisi: {{ $order->tarif->kondisiInfo->nama }}</td>
                                    <td style="width: 100px"></td>
                                    <td>{{ $order->bttb->sum('qty') }}</td>
                                    <td class="text-center">Pembayar: {{ $order->tarif->customer->nama }}</td>
                                </tr>
                            </table>
                        </div> --}}
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
                            <div style="margin-top: 40px; margin-left:70px" style="font-size: .7rem">
                                <span class="text-center">(....................................................)</span><br>
                                <span class="text-center" style="font-size: .7rem">Stampel + TTD + Nama</span>
                            </div>
                        </div>
                        <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                            {{-- <p>Pengantar</p>
                            <div style="margin-top: 95px">
                                <span class="text-center">(....................................................)</span>
                            </div> --}}
                        </div>
                        <div class="col-4 px-1 mt-2 text-center" style="font-size: .7rem">
                            <p> Surabaya, {{ date('d F Y', strtotime($order->jadwal_kapal->etd)) }}</p>
                            <div style="margin-top: 60px">
                                <span class="text-left">({{ Auth::user()->name }})</span>
                            </div>
                        </div>
                    @endif

                </div>
                {{-- <div class="page-number" style="font-size: .7rem"><i>Page {{ $page }} of <span class="off-page"></span></i></div> --}}
            </div>
            @if (ceil($data->count()/15)!=1)
                @if (!$loop->last)
                    <p class="pagebreak"></p>
                    @php
                        $page++;
                    @endphp
                @endif
            @endif
            @endforeach
        @endif
    @endif
@endif
</div>
@endsection

@section('script')
    @if (request('print'))
    <script>
        window.print();
    </script>
    @endif
    <script>
        $(function(){
            $('.off-page').html(@json($page));
        })
        $('#alamat').keyup(function (e) {
            $('.col-alamat').html(': '+$(this).val());
        });
    </script>
@endsection
