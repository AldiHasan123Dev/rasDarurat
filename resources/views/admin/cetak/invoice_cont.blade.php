@extends('layouts.admin')
@section('style')
    <style>
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');
            @page {
                size: 210mm 297mm;
                margin: 1cm 0cm 0cm 0cm;
            }
            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
            }

            .first-page{
                width: 100%;
                height: 100%;
                position: relative;
                top: -193px;
            }
            #print, #print * {
                visibility: visible;
                font-size: .65rem !important;
            }
            #print {
                width: 100%;
                height: 100%;
                position: relative;
                left: 0;
                top: 0px;
                /* top: -120px; */
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
            .page-break {
                page-break-after: always;
                overflow:hidden;
            }
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

        function tanggal($date){
            $tanggal = date("d", strtotime($date));
            $bulan = date("m", strtotime($date));
            $tahun = date("Y", strtotime($date));

            $nama_bulan = "";
            switch ($bulan) {
            case "01":
                $nama_bulan = "Januari";
                break;
            case "02":
                $nama_bulan = "Februari";
                break;
            case "03":
                $nama_bulan = "Maret";
                break;
            case "04":
                $nama_bulan = "April";
                break;
            case "05":
                $nama_bulan = "Mei";
                break;
            case "06":
                $nama_bulan = "Juni";
                break;
            case "07":
                $nama_bulan = "Juli";
                break;
            case "08":
                $nama_bulan = "Agustus";
                break;
            case "09":
                $nama_bulan = "September";
                break;
            case "10":
                $nama_bulan = "Oktober";
                break;
            case "11":
                $nama_bulan = "November";
                break;
            case "12":
                $nama_bulan = "Desember";
                break;
            }
            return $tanggal . " " . $nama_bulan . " " . $tahun;
        }
@endphp
    <div class="container">
        <div class="card p-3 shadow">
            @if (is_null($order->invoice))
                @if (count($validate)>0)
                    <ul class="alert alert-danger text-white py-1">
                        @foreach ($validate as $text)
                            <li><strong>{{ $text }}</strong></li>
                        @endforeach
                    </ul>
                @else
                <div class="d-flex" style="gap:5px">
                    <a href="{{ route('keuangan.pre_invoice') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
                    <form action="{{ route('keuangan.generateInvoice',$order) }}" method="post">
                        @csrf
                        <input type="hidden" name="sub_total" value="{{ $invoice['sub_total'] }}">
                        <input type="hidden" name="ppn" value="{{ $invoice['ppn'] }}">
                        <input type="hidden" name="asuransi" value="{{ $invoice['asuransi_total'] }}">
                        <input type="hidden" name="total" value="{{ $invoice['total'] }}">
                        <input type="hidden" name="pembayar_id" value="{{ $order->tarif->customer_id }}">
                        <input type="hidden" name="job" value="{{ $order->job }}">
                        <input type="hidden" name="keterangan" value="{{ $invoice['keterangan'] }}">
                        <input type="hidden" name="tujuan" value="{{ $order->tarif->tujuan_lokasi->nama }}">
                        <input type="hidden" name="tagihan" value="{{ $cas->sum('jumlah') }}">
                        <input type="hidden" name="admin" value="{{ $invoice['admin'] }}">
                        <input type="hidden" name="pph" value="{{ $invoice['pph'] }}">
                        <button type="submit" name="tipe_invoice" value="cont" onclick="return confirm('Apa anda yakin?')" class="btn btn-sm btn-success mb-3">Submit Invoice</button>
                    </form>
                </div>
                @endif
            @else
            <script>
                window.print();
            </script>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-sm btn-success mb-3 w-75">Print</button>
                    @if ($order->tarif->customer->all_in==1)
                    <button class="btn btn-sm btn-primary mb-3 w-25" data-bs-toggle="modal" data-bs-target="#exampleModal">Edit Tarif</button>
                    @endif
                </div>
            @endif
        </div>
        <div class="card p-3 mt-3">
            <div id="print">

                <div class="invoice-box first-page">
                    <x-header-cop>
                        <div style="width:30%; ">
                            <table style="width: 100%; font-size: .7rem; font-weight:bold; border: 2px solid black">
                                <tr><td class="text-center" style="line-spacing: 1rem">INVOICE</td></tr>
                            </table>
                        </div>
                    </x-header-cop>
                    <div class="row mt-3">
                        <div class="col-6">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 120px">No. Invoice</td>
                                    <td>: {{ $order->invoice ?? '-' }}</td>
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
                                    <td style="vertical-align: top">:
                                        {{ $nama_barang }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 60px">Customer</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ $order->tarif->customer->nama }} </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top">Alamat</td>
                                    <td style="vertical-align: top">:</td>
                                    <td>{{ $order->tarif->customer->alamat }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $order->tarif->customer->kota }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <table class="mt-2 w-100 tables" style="font-size: .7rem">
                        <thead>
                            <tr class="heading">
                                <td>No</td>
                                <td>Uraian</td>
                                <td>Koli</td>
                                <td>Jumlah</td>
                                <td>Tipe Tarif</td>
                                <td>X</td>
                                <td>Tarif</td>
                                <td>Sub Total</td>
                            </tr>
                        </thead>
                        @foreach ($invoice['items'] as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item['keterangan'] }}</td>
                                <td class="text-center">{{ $item['koli'] }} Koli</td>
                                <td class="text-center">{{ $item['jumlah'] }} </td>
                                <td class="text-center">{{ $item['si'] }}</td>
                                <td class="text-center">X</td>
                                <td>
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($item['tarif']) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($item['sub_total']) }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if ($order->tarif->kondisi==1||$order->tarif->kondisi==6)
                        <tr>
                            <td class="text-center">2.</td>
                            <td>JASA EKSPEDISI</td>
                            <td class="text-center">{{ $invoice['doc_count'] }} Doc</td>
                            <td class="text-center">{{ $invoice['doc_count'] }} </td>
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
                                    <span>{{ number_format($invoice['doc_total']) }}</span>
                                </div>
                            </td>
                        </tr>
                        @endif
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
                                    <span>{{ number_format($invoice['sub_total']) }}</span>
                                </div>
                            </td>
                        </tr>
                        @if ($ppn>0)
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3" style="border: 1px solid black;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span>PPN 1,1%:</span>
                                </div>
                            </td>
                            <td colspan="1" style="border: 1px solid black;">
                                <div  class="price d-flex justify-content-between px-2"> 
                                    <span>Rp</span>
                                    <span>{{ number_format((int) $invoice['ppn']) }}</span>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @if ($invoice['asuransi_total']>0)
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3" style="border: 1px solid black">Asuransi {{ $invoice['asuransi'] }}</td>
                            <td style="border: 1px solid black">
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($invoice['asuransi_total']) }}</span>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @foreach ($cas as $tagihan)
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3" style="border: 1px solid black">{{ $tagihan->nama }}</td>
                            <td style="border: 1px solid black">
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($tagihan->jumlah) }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="fw-bold" colspan="7" style="border: 1px solid black; text-align:right">TOTAL</td>
                            <td class="fw-bold" style="border: 1px solid black">
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format(ceil($invoice['total'])) }}</span>
                                </div>
                            </td>
                        </tr>
                        @if ($pph>0)
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3" style="border: 1px solid black">PPh (dengan Kode Objek Pajak
                                24-104-56)</td>
                            <td style="border: 1px solid black">
                                <div class="price d-flex justify-content-between px-2">
                                    <span>Rp</span>
                                    <span>{{ number_format($invoice['pph']) }}</span>
                                </div>
                            </td>
                        </tr>
                        @endif

                    </table>

                    <div class="row mt-3">
                        <div class="col-12">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 100px">Terbilang</td>
                                    <td>: {{ strtoupper(terbilang(ceil($invoice['total']))) }} RUPIAH</td>
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
                                    <td>: {{ $no_rek }}</td>
                                </tr>
                                <tr>
                                    <td>Atas Nama</td>
                                    <td>: {{ $bank_name }}</td>
                                </tr>
                                <tr>
                                    <td>Bank</td>
                                    <td>: {{ $bank }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-5">
                            <div class="text-center" style="font-size: .7rem">
                                <p>Surabaya, {{ is_null($order->invoice_date)?'-':tanggal($order->invoice_date) }}</p>
                                <div style="height: 2.3cm"></div>
                                ({{$invoice_name}})
                            </div>
                        </div>
                    </div>
                </div>

                <p class="page-break"></p>

                    @foreach ($allin['items'] as $idx => $o)
                    <div class="invoice-box">
                        <x-header-cop>
                            <div style="width:30%; ">
                                <table style="width: 100%; font-size: .7rem; font-weight:bold; border: 2px solid black">
                                    <tr><td class="text-center" style="line-spacing: 1rem">INVOICE</td></tr>
                                </table>
                            </div>
                        </x-header-cop>
                        <div class="row mt-3">
                            <div class="col-6">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 120px">No. Invoice</td>
                                        <td>: {{ $o['invoice'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kapal</td>
                                        <td>: {{ $o['kapal'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pelabuhan Tujuan</td>
                                        <td>: {{ $o['tujuan'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Buat Pembayaran (Jenis)</td>
                                        <td style="vertical-align: top">:
                                            {{ $o['barang'] }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 60px">Customer</td>
                                        <td style="width:5px">:</td>
                                        <td>{{ $o['customer'] }} </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Alamat</td>
                                        <td style="vertical-align: top">:</td>
                                        <td>{{ $o['alamat'] }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $o['kota'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <table class="mt-2 w-100 tables" style="font-size: .7rem">
                            <thead>
                                <tr class="heading">
                                    <td>No</td>
                                    <td>Uraian</td>
                                    <td>Koli</td>
                                    <td>Jumlah</td>
                                    <td>Tipe Tarif</td>
                                    <td>X</td>
                                    <td>Tarif</td>
                                    <td>Sub Total</td>
                                </tr>
                            </thead>
                            <tr>
                                <td class="text-center">1</td>
                                <td>{{ $o['keterangan'] }}</td>
                                <td class="text-center">{{ $o['koli'] }} Koli</td>
                                <td class="text-center">1 </td>
                                <td class="text-center">{{ $o['si'] }}</td>
                                <td class="text-center">X</td>
                                <td>
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span><input type="text" readonly class="text-right text-end" id="tarif-{{ $idx }}" style="border:none" value="{{ number_format(ceil($o['tarif'])) }}"></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span><input type="text" name="sub_total[]" readonly class="text-right text-end" id="sub-total-{{ $idx }}" style="border:none" value="{{ number_format(ceil($o['sub_total'])) }}"></span>
                                    </div>
                                </td>
                            </tr>
                            <tr style="height: 20px !important">
                                <td colspan="4"></td>
                                <td colspan="4" style="border-bottom: 1px solid black"></td>
                            </tr>
                            @if ($o['asuransi_total']>0||$cas->sum('jumlah')>0)
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="3" style="border: 1px solid black">Sub Total</td>
                                <td style="border: 1px solid black">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span id="subtotal-{{ $idx }}">{{ number_format(ceil($o['sub_total'])) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @if ($o['asuransi_total']>0)
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="3" style="border: 1px solid black">Asuransi {{ $o['asuransi'] }}</td>
                                <td style="border: 1px solid black">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($o['asuransi_total']) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            {{-- @foreach ($o->tagihan as $tagihan)
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="3" style="border: 1px solid black">{{ $tagihan->nama }}</td>
                                <td style="border: 1px solid black">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($tagihan->jumlah) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach --}}
                            <tr>
                                <td class="fw-bold" colspan="7" style="border: 1px solid black; text-align:right">TOTAL</td>
                                <td class="fw-bold" style="border: 1px solid black">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span id="total-{{ $idx }}">{{ number_format(ceil($o['total'])) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td class="fw-bold" colspan="7" style="border: 1px solid black; text-align:right">TOTAL</td>
                                <td class="fw-bold" style="border: 1px solid black">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span id="total-{{ $idx }}">{{ number_format(ceil($o['total'])) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endif

                        </table>

                        <div class="row mt-3">
                            <div class="col-12">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 100px">Terbilang</td>
                                        <td id="terbilang-{{ $idx }}">: {{ strtoupper(terbilang(ceil($o['total']))) }} RUPIAH</td>
                                    </tr>
                                    <tr>
                                        <td>Container</td>
                                        <td>: {{ $o['container'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Group Job</td>
                                        <td>: {{ $o['job'] }}</td>
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
                                        <td>: {{ $no_rek }}</td>
                                    </tr>
                                    <tr>
                                        <td>Atas Nama</td>
                                        <td>: {{ $bank_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Bank</td>
                                        <td>: {{ $bank }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-5">
                                <div class="text-center" style="font-size: .7rem">
                                    <p>Surabaya, {{ is_null($order->invoice_date)?'-':tanggal($order->invoice_date) }}</p>
                                    <div style="height: 2.3cm"></div>
                                    ({{$invoice_name}})
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="page-break"></div>
                    @endforeach
            </div>
        </div>
    </div>

    @if ($order->tarif->customer->all_in==1)
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Tarif All In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="mt-2 w-100 table" style="font-size: .7rem">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Uraian</td>
                                <td>Tipe Tarif</td>
                                <td>Tarif</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allin['items'] as $idx => $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item['keterangan'] }}</td>
                                <td class="text-center">{{ $item['si'] }}</td>
                                <td>
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span><input type="number" class="text-right text-end" onkeyup="editTarif(this,{{ $idx }},{{ $item['asuransi_total'] }})" style="border:none" value="{{ ceil($item['tarif']) }}"></span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('script')
    @if ($order->tarif->customer->all_in==1)
        <script>
            function editTarif(e,idx,asuransi){
                var val = parseInt(e.value);
                var total = val + asuransi;
                var terbilang_nominal = terbilang(total);
                $('#tarif-'+idx).val(val.toLocaleString('en-US'));
                $('#sub-total-'+idx).val(val.toLocaleString('en-US'));
                $('#subtotal-'+idx).html(val.toLocaleString('en-US'));
                $('#total-'+idx).html(total.toLocaleString('en-US'));
                $('#terbilang-'+idx).html(': '+terbilang_nominal.toUpperCase()+' RUPIAH');
                // hitung();
            }

            function hitung() {
                var sub_total = 0;
                var values = $("input[name='sub_total[]']").map(function(){return $(this).val();}).get();
                $.each(values, function (indexInArray, item) {
                    var price = parseInt(item.replaceAll(',',''));
                    sub_total += price;
                });
                var subtotal = parseInt(sub_total);
                var total = parseInt(asuransi) + sub_total;
                var terbilang_nominal = terbilang(total);
                $('.subtotal').html(subtotal.toLocaleString('en-US'));
                $('.total').html(total.toLocaleString('en-US'));
                $('.terbilang').html(': '+terbilang_nominal.toUpperCase()+' RUPIAH');
            }

            function terbilang(n) {
                let bilangan = [    '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh',    'sebelas'  ];
                if (n < 12) {
                    return bilangan[n];
                } else if (n < 20) {
                    return bilangan[n % 10] + ' belas';
                } else if (n < 100) {
                    return (bilangan[Math.floor(n / 10)] + ' puluh ' + bilangan[n % 10]).trim();
                } else if (n < 200) {
                    return 'seratus ' + terbilang(n % 100);
                } else if (n < 1000) {
                    return (bilangan[Math.floor(n / 100)] + ' ratus ' + terbilang(n % 100)).trim();
                } else if (n < 2000) {
                    return 'seribu ' + terbilang(n % 1000);
                } else if (n < 1000000) {
                    return (terbilang(Math.floor(n / 1000)) + ' ribu ' + terbilang(n % 1000)).trim();
                } else if (n < 1000000000) {
                    return (terbilang(Math.floor(n / 1000000)) + ' juta ' + terbilang(n % 1000000)).trim();
                } else if (n < 1000000000000) {
                    return (terbilang(Math.floor(n / 1000000000)) + ' milyar ' + terbilang(n % 1000000000)).trim();
                } else if (n < 1000000000000000) {
                    return (terbilang(Math.floor(n / 1000000000000)) + ' trilyun ' + terbilang(n % 1000000000000)).trim();
                } else {
                    return 'nilai terlalu besar';
                }
            }

        </script>
    @endif
@endsection
