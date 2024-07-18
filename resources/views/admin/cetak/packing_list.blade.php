@extends('layouts.admin')
@section('style')
    <style>
        .select2.select2-container.select2-container--default{
            width: 100% !important;
        }
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
                font-size: .8rem !important;
            }
            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -130px;
            }
            #table td, #table th{
                border: 1px solid black;
            }
            #print {
                color: #000;
            }
            .table tr td{
                padding: 0px 2px;
                border-left: 1px solid black !important;
                border-right: 1px solid black !important;
                border-bottom: none;
                border-top: none;
            }
            .table>tbody>tr>td:first-child{
                padding: 0px 2px !important;
            }
        }
        .table tr td{
            vertical-align: middle;
            padding: 3px 3px;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="d-flex" style="gap:5px">
                <a href="{{ route('order.index') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
                <button onclick="window.print()" class="btn btn-sm btn-success mb-3">Print</button>
            </div>
            <form action="{{ route('cetak.packingList') }}" method="get" class="d-flex" style="gap:5px">
                <select name="customer_id" class="select2" id="customer_id" style="width:180px">
                    @foreach ($customers as $cus)
                        <option {{ $customer_id==$cus->id?'selected':'' }} value="{{ $cus->id }}">{{ $cus->nama }}</option>
                    @endforeach
                </select>
                <select name="kapal_id" class="select2" id="kapal_id" style="width:180px">
                    @foreach ($kapal as $item)
                        <option {{ $kapal_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
                <input type="text" placeholder="voyage" name="voyage" style="width:180px"/>
                <select name="tujuan_id" class="select2" id="tujuan_id" style="width:180px">
                    @foreach ($tujuan as $item)
                        <option {{ $tujuan_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary mb-3">Filter</button>
            </form>
        </div>
        <div class="card p-3">
            @if ($order)
            <div id="print">
                <x-header-cop>
                    <div style="width:30%; ">
                        <table style="font-size: .7rem; font-weight:bold">
                            <tr><td>PACKING LIST</td></tr>
                            <tr class="border-top border-dark"><td>{{ $order->job }}</td></tr>
                        </table>
                    </div>
                </x-header-cop>
                <div class="row mt-2">
                    <div class="col-7">
                        <table style="font-size: .7rem">
                            <tr>
                                <td style="width: 150px">Nama Customer</td>
                                <td>: {{ $order->tarif->customer->nama }}</td>
                            </tr>
                            <tr>
                                <td>Nama Kapal</td>
                                <td>: {{ $order->jadwal_kapal->kapal->nama }} Voy. {{ $order->jadwal_kapal->voyage }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-5">
                        <table style="font-size: .7rem">
                            @if (is_null($order->jadwal_kapal->td))
                            <tr>
                                <td style="width: 150px">ETD</td>
                                <td>: {{ is_null($order->jadwal_kapal->etd) ? '-' : date('d/m/Y',strtotime($order->jadwal_kapal->etd))}}</td>
                            </tr>
                            @else
                                <tr>
                                    <td style="width: 150px">TD</td>
                                    <td>: {{ date('d/m/Y',strtotime($order->jadwal_kapal->td))}}</td>
                                </tr>
                            @endif
                            <tr>
                                <td>Tujuan</td>
                                <td>: {{ $order->tarif->tujuan_lokasi->nama }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 mt-2">
                        <table class="table border-dark border-y" style="font-size: .7rem">
                            <thead>
                                <tr class="border border-dark">
                                    <th class="text-center border-x border-dark">No.</th>
                                    <th class="text-center border-x border-dark">Cont / Seal</th>
                                    <th class="text-center border-x border-dark">Jenis Barang</th>
                                    <th colspan="2" class="text-center border-x border-dark">Koli</th>
                                    <th class="text-center border-x border-dark">Tgl Masuk</th>
                                    <th class="text-center border-x border-dark">Pengirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $jumlah = 0;
                                    $jumlah_vol = 0;
                                    $no = 1;
                                @endphp
                                @foreach ($data as $bttb)
                                    @foreach ($bttb->bttb as $item)
                                    <tr>
                                        @if ($loop->first)
                                        <td style="vertical-align: text-top" rowspan="{{ $bttb->bttb->count() + 1 }}" class="text-center border border-dark">{{ $no }}</td>
                                        <td class="border border-dark text-center" style="vertical-align: text-top" rowspan="{{ $bttb->bttb->count() + 1 }}">{{ $item->order->container }} / {{ $item->order->seal }}</td>
                                        @endif
                                        <td class="border-x border-dark"> {{ $item->barang->nama }}</td>
                                        <td class="text-center border-x border-dark" style="width: 40px">{{ $item->qty }}</td>
                                        <td class="text-center border-x border-dark" style="width: 40px">{{ $item->satuan->nama ?? '-' }}</td>
                                        <td class="border-x border-dark text-center">{{ date('d/m/Y', strtotime($item->tgl_masuk)) }}</td>
                                        <td class="border-x border-dark text-center">{{ $item->pengirim->nama }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td class="text-center border border-dark">jumlah</td>
                                        <td class="text-center border border-dark" colspan="2">{{ $bttb->bttb->sum('qty') }}</td>
                                        <td class="text-center border border-dark" colspan="2"></td>
                                    </tr>
                                    @php
                                        $jumlah += $bttb->bttb->sum('qty');
                                        $jumlah_vol += $bttb->bttb->sum('vol');
                                        $no++;
                                    @endphp
                                @endforeach
                            </tbody>
                            {{-- <tfoot class="border border-dark">
                                <tr>
                                    <td class="border border-dark"></td>
                                    <td class="border border-dark"></td>
                                    <td class="border border-dark text-center">Jumlah</td>
                                    <td class="border border-dark text-center" colspan="2">{{ $jumlah }}</td>
                                    <td class="border border-dark"></td>
                                    <td class="border border-dark"></td>
                                </tr>
                            </tfoot> --}}
                        </table>
                        <div class="mt-5">
                            <b>Keterangan:</b>
                            <table class="border border-dark w-100" style="font-size: .7rem">
                                @foreach ($data->groupBy('tarif_id') as $or)
                                    <tr>
                                        <td style="width: 200px" class="border border-dark">{{ $or->first()->tarif->kondisiInfo->nama }}</td>
                                        <td class="border border-dark"> :
                                            @foreach ($or as $ord)
                                                {{ $ord->container }} {{ $loop->last?'':',' }}
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @else
                <div class="alert alert-warning text-center">
                    <strong>Empty!</strong>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('script')
    @if (request('print'))
        <script>
            window.print();
        </script>
    @endif
    <script>
        $('#customer_id').select2();
        $('#kapal_id').select2();
        $('#tujuan_id').select2();
    </script>
@endsection
