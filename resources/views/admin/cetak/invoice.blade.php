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
                font-size: .6rem !important;
            }
            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -65px;
            }
            #table td, #table th{
                border: 1px solid black;
            }
            #print {
                color: #000;
            }
            /* .table tr td{
                padding: 0px 2px;
                border-left: 1px solid !important;
                border-right: 1px solid !important;
                border-bottom: none;
                border-top: none;
            }
            .table>tbody>tr>td:first-child{
                padding: 0px 2px !important;
            } */
        }
        tr.heading td{
            border: 1px solid black;
            text-align: center;
        }
        .table tr td{
            vertical-align: middle;
            padding: 3px 3px;
        }
    </style>
@endsection
@section('content')
@php
    function terbilang($angka) {
        $angka = (float)$angka;
        $bilangan = array(
                '',
                'satu',
                'dua',
                'tiga',
                'empat',
                'lima',
                'enam',
                'tujuh',
                'delapan',
                'sembilan',
                'sepuluh',
                'sebelas'
            );
            if ($angka < 12) {
                return $bilangan[$angka];
            } else if ($angka < 20) {
                return $bilangan[$angka - 10] . ' belas';
            } else if ($angka < 100) {
                $hasil_bagi = (int)($angka / 10);
                $hasil_mod = $angka % 10;
                return trim(sprintf('%s puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
            } else if ($angka < 200) {
                return 'seratus ' . terbilang($angka - 100);
            } else if ($angka < 1000) {
                $hasil_bagi = (int)($angka / 100);
                $hasil_mod = $angka % 100;
                return trim(sprintf('%s ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
            } else if ($angka < 2000) {
                return 'seribu ' . terbilang($angka - 1000);
            } else if ($angka < 1000000) {
                $hasil_bagi = (int)($angka / 1000);
                $hasil_mod = $angka % 1000;
                return trim(sprintf('%s ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
            } else if ($angka < 1000000000) {
                $hasil_bagi = (int)($angka / 1000000);
                $hasil_mod = $angka % 1000000;
                return trim(sprintf('%s juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
            } else if ($angka < 1000000000000) {
                $hasil_bagi = (int)($angka / 1000000000);
                $hasil_mod = fmod($angka, 1000000000);
                return trim(sprintf('%s miliar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
            } else {
                return 'Angka terlalu besar';
            }
        }

@endphp
    <div class="container">
        <div class="d-flex" style="gap:5px">
            <a href="{{ route('order.index') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
            <button onclick="window.print()" class="btn btn-sm btn-success mb-3">Print</button>
        </div>
        <div class="card p-3">
            <div id="print">
                <div class="invoice-box" id="print">
                    <div class="header d-flex" style="gap:5px; width:100%">
                        <img src="{{ asset('logo.png') }}" alt="logo" style="height: 50px; width: 30%" class="img-fluid">
                        <div style="width: 40%; margin-left:35px">
                            <table style="font-size:.7rem">
                                <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDERA</td></tr>
                                <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                                <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                            </table>
                        </div>
                        <div style="width:30%; ">
                            <table style="width: 100%; font-size: .7rem; font-weight:bold; border: 2px solid black">
                                <tr><td class="text-center" style="line-spacing: 1rem">INVOICE</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 120px">No. Invoice</td>
                                    <td>: {{ $order->tarif->customer->nama }}</td>
                                </tr>
                                <tr>
                                    <td>Kapal</td>
                                    <td>: {{ $order->jadwal_kapal->kapal->nama }} VOY. {{ $order->jadwal_kapal->voyage }}</td>
                                </tr>
                                <tr>
                                    <td>Pelabuhan Tujuan</td>
                                    <td>: {{ $order->tarif->tujuan_lokasi->nama }}</td>
                                </tr>
                                <tr>
                                    <td>Buat Pembayaran (Jenis)</td>
                                    <td>: {{ $order->barang->nama }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 60px">Customer</td>
                                    <td>: {{ $order->tarif->customer->nama }} </td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: {{ $order->tarif->customer->alamat }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <table class="mt-2 w-100 tables" style="font-size: .7rem">
                        <thead>
                            <tr class="heading">
                                <td>No</td>
                                <td>Uraian</td>
                                <td>{{ $nama }}</td>
                                <td>Jumlah</td>
                                <td>Tipe Tarif</td>
                                <td>X</td>
                                <td>Tarif</td>
                                <td>Sub Total</td>
                            </tr>
                        </thead>
                        <tr>
                            <td class="text-center">1.</td>
                            <td>{{ $order->tarif->kondisiInfo->nama }}, {{ $order->tarif->dari_lokasi->nama }} - {{ $order->tarif->tujuan_lokasi->nama }}</td>
                            <td class="text-center">{{ $kategori }}</td>
                            <td class="text-center">{{ $jumlah }} </td>
                            <td class="text-center">{{ $order->tarif->shipmentInfo->nama }}</td>
                            <td class="text-center">X</td>
                            <td>
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($tarif) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($price) }}</span>
                                </div>
                            </td>
                        </tr>
                        @if ($order->tarif->kondisi==1||$order->tarif->kondisi==6)
                        @php
                            $doc = $orders->count() * 500000;
                        @endphp
                        <tr>
                            <td class="text-center">2.</td>
                            <td>JASA EKSPEDISI</td>
                            <td class="text-center">{{ $orders->count() }} Doc</td>
                            <td class="text-center">{{ $orders->count() }} </td>
                            <td class="text-center">Doc</td>
                            <td class="text-center">X</td>
                            <td>
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>500.000</span>
                                </div>
                            </td>
                            <td>
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($doc) }}</span>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @php
                            $sub_total = $doc + $price;
                            $ppn = $sub_total * 0.011;
                            $total = $sub_total + $ppn;
                            if ($doc==0) {
                                $pph = $sub_total * 0.02;
                            }else{
                                $pph = $doc * 0.02;
                            }
                        @endphp
                        <tr style="height: 20px !important">
                            <td colspan="4"></td>
                            <td colspan="4" style="border-bottom: 1px solid black"></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3" style="border: 1px solid black">Sub Total</td>
                            <td style="border: 1px solid black">
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($sub_total) }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="border-bottom: 1px solid black"></td>
                            <td colspan="3" style="border: 1px solid black">PPn 1,1%</td>
                            <td style="border: 1px solid black">
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($ppn) }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" style="border: 1px solid black; text-align:right">TOTAL</td>
                            <td style="border: 1px solid black">
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($total) }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3" style="border: 1px solid black">PPh (dengan Kode Objek Pajak 24-104-56)</td>
                            <td style="border: 1px solid black">
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($pph) }}</span>
                                </div>
                            </td>
                        </tr>

                    </table>

                    <div class="row mt-3">
                        <div class="col-12">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 150px">Terbilang</td>
                                    <td>: {{ strtoupper(terbilang($total)) }} RUPIAH</td>
                                </tr>
                                <tr>
                                    <td>Container</td>
                                    <td>: {{ implode(', ',$orders->pluck('container')->toArray()) }}</td>
                                </tr>
                                <tr>
                                    <td>No. Group Job</td>
                                    <td>:
                                        @foreach ($orders as $item)
                                            {{ $item->job }}-{{ sprintf('%02d',$item->no_job) }}@if (!$loop->last), @endif
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-7">
                            <span>Pembayaran dapat dilakukan melalui:</span>
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 150px">Rekening No.</td>
                                    <td>: 1400 046 005 006</td>
                                </tr>
                                <tr>
                                    <td>Atas Nama</td>
                                    <td>: PT. RAHMAT ALAM ALAM SAMUDERA</td>
                                </tr>
                                <tr>
                                    <td>Bank</td>
                                    <td>: Mandiri Cabang Indrapura Surabaya</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-5">
                            <div class="text-center" style="font-size: .7rem">
                                <p>Surabaya, {{ date('d F Y') }}</p>
                                <br><br>
                                (LATIFAH)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
