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
                font-size: .6rem !important;
            }
            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -180px;
            }
            .table tr td{
                border: #000 1px solid;
                padding: 0px 3px !important;
                vertical-align: middle;
            }
        }

        #print {
            color: #000;
            }
        .table tr td{
            border: #000 1px solid;
            padding: 0;
            vertical-align: middle;
        }
        .table tr th{
            border: #000 1px solid;
        }
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            border: none;
            text-indent: 1px;
            text-overflow: '';
            font-size: .7rem;
            padding: 5px 10px;
            background: none;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="card p-3">
            <div class="d-flex justify-content-between" style="gap:5px">
                <div class="d-flex" style="gap: 10px">
                    <div class="d-flex gap-3">
                        <div class="mr-3">
                            <input type="text" id="attn" class="form-control" style="font-size:.7rem" placeholder="Attention">
                        </div>
                        <button onclick="window.print()" class="btn btn-sm btn-success ml-2 mb-3">Print</button>
                        @if (request('jadwal_kapal_id'))
                            <form action="{{ route('order.export.si') }}" method="post">
                                @csrf
                                <input type="hidden" name="tujuan" value="{{ request('tujuan') }}">
                                <input type="hidden" name="jadwal_kapal_id" value="{{ request('jadwal_kapal_id') }}">
                                <input type="hidden" name="to" value="{{ $jadwal_kapal->pelayaran->nama }}">
                                <input type="hidden" name="attn" id="attn-input">
                                <input type="hidden" name="title" value="SI {{ strtoupper($jadwal_kapal->kapal->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }} {{ strtoupper($tujuan->nama) }} TD {{ date('d F Y', strtotime($jadwal_kapal->td)) }}">
                                <button type="submit" class="btn btn-sm btn-primary">Export Excel</button>
                            </form>
                        @endif
                    </div>
                </div>
                <button data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-sm btn-info">Cari SI</button>
            </div>
        </div>
        <div class="card p-3 mt-3">
            <div id="print" style="width: 100%">
                <x-header-cop></x-header-cop>
                <hr>
                <div class="row">
                    @if ($orders->count()>0)
                    <div class="col-4">
                        <table style="font-size: .7rem">
                            <tr>
                                <td style="width: 60px">To</td>
                                <td>: {{ $jadwal_kapal->pelayaran->nama }} </td>
                            </tr>
                            <tr>
                                <td>Attn.</td>
                                <td>: <span id="d-attn"></span></td>
                            </tr>
                        </table>
                    </div>
                    @endif
                    <div class="col-12 mt-2">
                        @if ($orders->count()>0)
                        <p class="text-center"><u>SI {{ strtoupper($jadwal_kapal->kapal->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }} {{ strtoupper($tujuan->nama) }} TD {{ date('d F Y', strtotime($jadwal_kapal->td)) }}</u></p>
                        @endif
                        <table class="table nowrap" style="font-size: .7rem; border: 1px solid black">
                            <thead>
                                <tr>
                                    <th class="text-center" style="vertical-align: middle" colspan="2">Pembagian BL</th>
                                    {{-- <th class="text-center" style="vertical-align: middle" rowspan="2">No.</th> --}}
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">Cont / Seal</th>
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">Koli</th>
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">Barang</th>
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">Stuffing</th>
                                </tr>
                                <tr>
                                    <th class="text-center" style="width: 200px">Penerima</th>
                                    <th class="text-center" style="width: 200px">Pengirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($orders->count()>0)
                                    @foreach ($orders->where('agen','AGEN')->groupBy('agen_id') as $data)
                                        @foreach ($data as $item)
                                            <tr>
                                                @if ($loop->first)
                                                <td class="text-center" rowspan="{{ $data->count() }}">{{ $item->agent->nama ?? '-' }}</td>
                                                <td class="text-center" rowspan="{{ $data->count() }}">
                                                    <select>
                                                        @foreach ($pengirim as $p)
                                                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @endif
                                                {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                                <td style="padding-left: 10px !important"> <a href="{{ route('bttb.index',['order_id'=>$item->id]) }}"> {{ $item->container }} / {{ $item->seal }}</a></td>
                                                <td class="text-center">{{ strtoupper(substr($item->tarif->shipmentInfo->nama,3,7)) }}</td>
                                                <td>{{ $item->barang->nama }}</td>
                                                <td class="text-center">{{ $item->tarif->stuffing??'-' }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    @foreach ($orders->where('agen','!=','AGEN')->groupBy('penerima_bl_id') as $data)
                                        @foreach ($data as $item)
                                            <tr>
                                                @if ($loop->first)
                                                <td class="text-center" rowspan="{{ $data->count() }}">{{ $item->penerima_bl->nama ?? '-' }}</td>
                                                <td class="text-center" rowspan="{{ $data->count() }}">
                                                    <select>
                                                        @foreach ($pengirim as $p)
                                                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @endif
                                                {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                                <td style="padding-left: 10px !important"> <a href="{{ route('bttb.index',['order_id'=>$item->id]) }}"> {{ $item->container }} / {{ $item->seal }}</a></td>
                                                <td class="text-center">{{ strtoupper(substr($item->tarif->shipmentInfo->nama,3,7)) }}</td>
                                                <td>{{ $item->barang->nama }}</td>
                                                <td class="text-center">{{ $item->tarif->stuffing??'-' }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('cetak.shipment') }}" method="GET" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Buat SI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="jadwal_kapal_id-si">Kapal</label><br>
                        <select name="jadwal_kapal_id" id="jadwal_kapal_id-si" class="form-control w-100">
                            @foreach ($jadwal_kapals as $kapal)
                                <option value="{{ $kapal->id }}">{{ $kapal->kapal->nama ?? '-' }} || Voy.{{ $kapal->voyage }} || {{ $kapal->pelayaran->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb">
                        <label for="tujuan-si">Tujuan</label><br>
                        <select name="tujuan" id="tujuan-si" class="form-control w-100">
                            @foreach ($data_lokasi as $lokasi)
                                <option value="{{ $lokasi->id }}">{{ $lokasi->nama ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Buat SI</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('#attn').keyup(function (e) {
            var val = $(this).val();
            $('#d-attn').html(val);
            $('#attn-input').val(val);
        });
        $("#jadwal_kapal_id-si").select2({
            dropdownParent: $('#exampleModal'),
        });
        $("#tujuan-si").select2({
            dropdownParent: $('#exampleModal'),
        });
    </script>
@endsection
