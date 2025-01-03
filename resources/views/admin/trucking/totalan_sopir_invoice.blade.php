@extends('layouts.admin')
@section('style')
    <style>
        @media print {
            @page {size: landscape}
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');
            /* @page {
                size: 210mm 297mm;
                margin: 1cm 0cm 0cm 0cm;
            } */
            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
            }

            .first-page{
                width: 100%;
                height: 100%;
                position: absolute;
                top: -180px;
            }
            #print, #print * {
                visibility: visible;
                font-size: .6rem !important;
            }
            #print {
                width: 100%;
                position: relative;
                left: -260px;
                /* top: -20px; */
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
            .table-responsive{
                overflow: visible;
            }
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
            @if (request('mutasi'))
                @php
                    $no_1 = App\Models\Jurnal::where('tipe','BBK')->whereYear('created_at', date('Y'))->max('no') + 1;
                    $no1 = sprintf('%03d',$no_1).'/BBK-RAS/'.date('y');
                    $no_2 = App\Models\Jurnal::where('tipe','BKK')->whereYear('created_at', date('Y'))->max('no') + 1;
                    $no2 = sprintf('%03d',$no_2).'/BKK-RAS/'.date('y');
                @endphp
                <form action="{{ route('mutasi-totalan-sopir.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="invoice" value="{{ $invoice }}">
                    <div class="d-flex gap-3">
                        <div class="mb-2">
                            <label for="nomor">Nomor Jurnal</label>
                            <select name="nomor" id="nomor" class="form-select" required>
                                <option value="">-</option>
                                <option value="{{ $no1 }}">{{ $no1 }}</option>
                                <option value="{{ $no2 }}">{{ $no2 }}</option>
                            </select>
                        </div>
                        <div class="mb-2 mt-3">
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('are you sure?')">Generate Jurnal</button>
                            <a href="{{ route('mutasi-totalan-sopir.index') }}" class="btn btn-primary btn-sm">Kembali</a>
                        </div>
                    </div>
                </form>
            @else
                @if (is_null($order->invoice_sopir))
                <div class="d-flex" style="gap:5px">
                    <a href="{{ route('trucking.totalan_sopir') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
                    @if (empty($invoice))
                    <form action="{{ route('trucking.generate.total_sopir') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_trucking_id" value="{{ $order->id }}">
                        <input type="hidden" name="sopir_id" value="{{ $order->sopir_id }}">
                        <input type="hidden" name="total" value="{{ $orders->sum('total_sopir') }}">
                        <input type="hidden" name="order_id" value="{{ implode(',',$order_id) }}">
                        <button type="submit" onclick="return confirm('Apa anda yakin?')" class="btn btn-sm btn-success mb-3">Submit Invoice Sopir</button>
                    </form>
                    @endif
                </div>
                @else
                <script>
                    window.print();
                </script>
                <form action="{{ route('trucking.export.slip_sopir') }}" method="post">
                    @csrf
                    <div class="d-flex gap-3">
                        <button type="submit" name="invoice" value="{{ $invoice }}" class="btn btn-sm btn-primary mb-3">Export Excel</button>
                        <button type="button" onclick="window.print()" class="btn btn-sm btn-success mb-3">Print</button>
                    </div>
                </form>
                @endif
            @endif
        </div>
        <div class="card p-3 mt-3">
            <div id="print">
                <div class="invoice-box first-page">
                    <div class="row mt-3">
                        <div class="col-6">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 60px">Totalan</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ $order->sopir ? $order->sopir->nama : '-' }} </td>
                                </tr>
                                <tr>
                                    <td style="width: 120px">Tanggal</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ $order->tgl_total ? date('d/m/Y',strtotime($order->tgl_total)):'-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="mt-2 w-100 table table-sm nowrap" style="font-size: .7rem; white-space:nowrap">
                            <thead>
                                <tr class="heading">
                                    {{-- <td>No</td> --}}
                                    <td>TGL Muat</td>
                                    <td>Tipe</td>
                                    <td>No JOB</td>
                                    <td>No Container</td>
                                    <td>Nopol</td>
                                    <td>Customer</td>
                                    <td>Pembayar</td>
                                    <td>Tujuan</td>
                                    <td>Borongan Sopir</td>
                                    <td>Sangu Sopir</td>
                                    <td>Simpanan Sopir</td>
                                    <td>Borongan Kuli</td>
                                    <td>Sangu Kuli</td>
                                    <td>Simpanan Kuli</td>
                                    <td>TB/TL</td>
                                    <td>Stappel</td>
                                    <td>Lain-lain</td>
                                    <td>Sub Total</td>
                                </tr>
                            </thead>
                            @foreach ($orders as $item)
                                <tr>
                                    <td class="text-center">{{ date('d/m/y', strtotime($item->tgl_muat)) }}</td>
                                    <td>{{ $item->tipe }}'</td>
                                    @if ($item->order)
                                    <td>{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                    @else
                                    <td>-</td>
                                    @endif
                                    <td>{{ $item->container }} / {{ $item->seal }}</td>
                                    <td>{{ $item->kendaraan->nopol }}</td>
                                    <td>{{ $item->customer->nama }}</td>
                                    <td>{{ $item->order ? ($item->order->tarif->customer->nama??'-') : '-' }}</td>
                                    <td>{{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                    <td class="text-center">{{ number_format($item->borongan) }}</td>
                                    <td class="text-center">{{ number_format($item->sangu) }}</td>
                                    <td class="text-center">{{ number_format($item->simpanan) }}</td>
                                    <td class="text-center">{{ number_format($item->borongan_kuli) }}</td>
                                    <td class="text-center">{{ number_format($item->kuli) }}</td>
                                    <td class="text-center">{{ number_format($item->simpanan_kuli) }}</td>
                                    <td class="text-center">{{ number_format($item->tb_tl) }}</td>
                                    <td class="text-center">{{ number_format($item->stappel) }}</td>
                                    <td class="text-center">{{ number_format($item->lain_lain) }}</td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->total_sopir) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr style="height: 20px !important">
                                <td colspan="18" style="border-bottom: 1px solid black"></td>
                            </tr>
                            <tr class="border-bottom border-dark">
                                <td colspan="15"></td>
                                <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($orders->sum('total_sopir')) }}</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
