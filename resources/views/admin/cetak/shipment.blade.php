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
        <div class="d-flex" style="gap:5px">
            <a href="{{ route('jadwalkapal.index') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
            <div class="d-flex">
                <div class="mr-3">
                    <input type="text" id="attn" class="form-control" style="font-size:.7rem" placeholder="Attention">
                </div>
                <button onclick="window.print()" class="btn btn-sm btn-success ml-2 mb-3">Print</button>
            </div>
        </div>
        <div class="card p-3">
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
                                <td>: <span id="d-attn"></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 mt-2">
                        <p class="text-center"><u>SI {{ strtoupper($jadwal_kapal->kapal->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }} {{ strtoupper($tujuan->nama) }} TD {{ date('d F Y', strtotime($jadwal_kapal->td)) }}</u></p>
                        <table class="table nowrap" style="font-size: .7rem; border: 1px solid black">
                            <thead>
                                <tr>
                                    <th class="text-center" style="vertical-align: middle" colspan="2">Pembagian BL</th>
                                    {{-- <th class="text-center" style="vertical-align: middle" rowspan="2">No.</th> --}}
                                    <th class="text-center" style="vertical-align: middle" rowspan="2">No. Container / Seal</th>
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
                                            <td class="text-center">{{ $item->bttb->sum('qty') }}</td>
                                            <td class="text-center">{{ $item->barang->nama }}</td>
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
                                        <td class="text-center">{{ $item->bttb->sum('qty') }}</td>
                                        <td class="text-center">{{ $item->barang->nama }}</td>
                                        <td class="text-center">{{ $item->tarif->stuffing??'-' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('#attn').keyup(function (e) {
            var val = $(this).val();
            $('#d-attn').html(val);
        });
    </script>
@endsection
