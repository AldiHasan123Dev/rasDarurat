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
                position: absolute;
                top: -180px;
            }
            .first-page2{
                width: 100%;
                height: 100%;
                position: absolute;
                top: -190px;
            }
            #print, #print * {
                visibility: visible;
                font-size: .7rem !important;
            }
            #print {
                width: 100%;
                position: relative;
                left: 0;
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
            <div class="d-flex" style="gap:5px">
                @if ($null_job>0 && $order->customer_id==2)
                <div class="alert alert-danger text-center text-white w-100">
                    <strong>Ada order dengan JOB ksosong. Harap cek container dan seal!</strong>
                    <br>
                    <a href="{{ route('trucking.pre-invoice') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
                </div>
                @else
                    @if (empty($invoice))
                    <a href="{{ route('trucking.pre-invoice') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
                    <form action="{{ route('trucking.generate.invoice') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ implode(',',$order_id) }}">
                        <input type="hidden" name="order" value="{{ $order->id }}">
                        <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
                        <input type="hidden" name="tipe" value="{{ $tipe }}">
                        <input type="hidden" name="pph" id="_pph">
                        <input type="hidden" name="total" id="_total">
                        <input type="hidden" name="rit" id="_rit">
                        <input type="hidden" name="lain_lain" id="_lain_lain">
                        @if ($tipe=='R1' && $order->customer_id==2)
                            <input type="hidden" name="jurnal_otomatis" value="yes">
                        @endif
                        <button type="submit" onclick="return confirm('Apa anda yakin?')" class="btn btn-sm btn-success mb-3">Submit Invoice</button>
                        <select name="pengirim" id="pengirim">
                            @foreach ($pengirim as $peng)
                                <option value="{{ $peng->nama }}" selected>{{ $peng->nama }}</option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                @endif
                @if ($order->is_seal == 1)    
                <button type="button" class="btn btn-sm btn-danger mb-2" style="margin-left: 780px">Terdapat Seal Ditagihkan</button>
                @endif
            </div>
            @else
            <script>
                window.print();
            </script>
            <button onclick="window.print()" class="btn btn-sm btn-success mb-3">Print</button>
            @endif
        </div>
        <div class="card p-3 mt-3">
            <div id="print">
                @if ($order->customer_id==2)
                    @if ($tipe=='R1'||$tipe=='VENDOR')
                    <div class="invoice-box first-page2">
                        {{-- <div class="header d-flex" style="gap:5px; width:100%">
                            <img src="{{ asset('logo.png') }}" alt="logo" style="height: 60px; width: 30%" class="img-fluid">
                            <div style="width: 40%; margin-left:35px">
                                <table style="font-size:.7rem">
                                    <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDERA</td></tr>
                                    <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                                    <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                                    <tr><td>Email : info@ptras.id</td></tr>
                                </table>
                            </div>
                            <div style="width:30%; ">
                                <table style="width: 100%; font-size: .7rem; font-weight:bold; border: 2px solid black">
                                    <tr><td class="text-center" style="line-spacing: 1rem">INVOICE</td></tr>
                                </table>
                            </div>
                        </div> --}}
                        <div class="row mt-3">
                            <div class="col-6">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 120px">No. Invoice</td>
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->invoice ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Customer</td>
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->customer->nama }} </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Attn</td>
                                        <td style="width:5px">:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Alamat</td>
                                        <td style="vertical-align: top">:</td>
                                        <td>{{ $order->customer->alamat }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $order->tarif->customer->kota }}</td>
                                    </tr> --}}
                                </table>
                            </div>
                        </div>

                        <table class="mt-2 w-100 tables" style="font-size: .7rem">
                            <thead>
                                <tr class="heading">
                                    {{-- <td>No</td> --}}
                                    <td>Uraian</td>
                                    <td>Tipe</td>
                                    <td>Jumlah</td>
                                    <td>X</td>
                                    <td>Tarif</td>
                                    <td>Sub Total</td>
                                </tr>
                            </thead>
                            @php
                                $total = 0;
                                $lain_lain = 0;
                                $rit = 0;
                                $pph = 0;
                            @endphp
                            @foreach ($data as $item)
                            @php
                                $total += $item->first()->tarif_nominal * $item->count();
                                $lain_lain += $item->sum('lain_lain');
                                $rit += $item->count();
                                $pph += round($item->sum('pph_21'));
                            @endphp
                                <tr>
                                    {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                    <td>Ongkos Angkut Perak - {{ $item->first()->tarif->tujuan->tujuanInfo->nama }}</td>
                                    <td class="text-center">{{ $item->first()->tipe }}'</td>
                                    <td class="text-center">{{ $item->count() }} Rit </td>
                                    <td class="text-center">X</td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->first()->tarif_nominal) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->first()->tarif_nominal * $item->count()) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($data as $ord)
                                @foreach ($ord as $item)
                                    @if ($item->tagihans->sum('jumlah')>0)
                                    @php
                                        $total += $item->tagihans->sum('jumlah');
                                    @endphp
                                        @foreach ($item->tagihans as $tag)
                                        <tr>
                                            {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                            <td colspan="4">{{ $tag->nama }}</td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                            <tr style="height: 20px !important">
                                <td colspan="7" style="border-bottom: 1px solid black"></td>
                            </tr>
                            <tr class="border-bottom border-dark">
                                <td colspan="2"></td>
                                <td class="text-center">{{ $rit }} Rit</td>
                                <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($total) }}</span>
                                    </div>
                                </td>
                            </tr>
                            {{-- <tr class="border-bottom border-dark">
                                <td colspan="3"></td>
                                <td class="fw-bold text-center" colspan="2">PPH 21 (3%)</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($pph) }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom border-dark">
                                <td colspan="5"></td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($total - $pph) }}</span>
                                    </div>
                                </td>
                            </tr> --}}
                        </table>

                        <div class="row mt-3">
                            <div class="col-9">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 100px">Terbilang</td>
                                        <td>: {{ strtoupper(terbilang($total)) }} RUPIAH</td>
                                    </tr>
                                    <tr>
                                        <td>Keterangan</td>
                                        <td>: </td>
                                    </tr>
                                </table>
                                <table style="font-size: .7rem" class="mt-2">
                                    @foreach ($data as $orderss)
                                        @foreach ($orderss as $item)
                                        <tr>
                                            <td style="width: 150px">{{ $item->container }}/{{ $item->seal }}</td>
                                            <td style="width: 50px"> {{ $item->tipe }}'</td>
                                            <td style="width: 100px"> {{ $item->kendaraan->nopol }}</td>
                                            @if ($item->order)
                                            <td style="width: 100px"> {{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                            @else
                                            <td>-</td>
                                            @endif
                                            <td style="width: 150px"> {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                            @if ($item->order)
                                            <td style="width: 250px"> {{ $item->order->tarif->customer->nama ?? '-' }} <td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </table>
                            </div>
                            <div class="col-3">
                                <div class="text-center" style="font-size: .7rem">
                                    <p>Surabaya, {{ is_null($order->tgl_invoice)?'-':tanggal($order->tgl_invoice) }}</p>
                                    <div style="height: 2cm"></div>
                                    (<input type="text" value="Rara" class="text-center pengirim" style="border:none; width:130px"/>)
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-break"></div>
                    <div class="invoice-box">
                        {{-- <div class="header d-flex" style="gap:5px; width:100%">
                            <img src="{{ asset('logo.png') }}" alt="logo" style="height: 60px; width: 30%" class="img-fluid">
                            <div style="width: 40%; margin-left:35px">
                                <table style="font-size:.7rem">
                                    <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDERA</td></tr>
                                    <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                                    <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                                    <tr><td>Email : info@ptras.id</td></tr>
                                </table>
                            </div>
                            <div style="width:30%; ">
                                <table style="width: 100%; font-size: .7rem; font-weight:bold; border: 2px solid black">
                                    <tr><td class="text-center" style="line-spacing: 1rem">INVOICE</td></tr>
                                </table>
                            </div>
                        </div> --}}
                        <div class="row mt-3">
                            <div class="col-6">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 120px">No. Invoice</td>
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->invoice ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Customer</td>
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->customer->nama }} </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Attn</td>
                                        <td style="width:5px">:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Alamat</td>
                                        <td style="vertical-align: top">:</td>
                                        <td>{{ $order->customer->alamat }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $order->tarif->customer->kota }}</td>
                                    </tr> --}}
                                </table>
                            </div>
                        </div>

                        <table class="mt-2 w-100 tables" style="font-size: .7rem">
                            <thead>
                                <tr class="heading">
                                    {{-- <td>No</td> --}}
                                    <td>Uraian</td>
                                    <td>Tipe</td>
                                    <td>Jumlah</td>
                                    <td>X</td>
                                    <td>Tarif</td>
                                    <td>Sub Total</td>
                                </tr>
                            </thead>
                            @php
                                $total = 0;
                                $lain_lain = 0;
                                $rit = 0;
                                $pph = 0;
                            @endphp
                            @foreach ($data as $item)
                            @php
                                $total += round(($item->first()->tarif_nominal/0.97)) * $item->count();
                                $lain_lain += $item->sum('lain_lain');
                                $rit += $item->count();
                                $pph += round($item->sum('pph_21'));
                            @endphp
                                <tr>
                                    {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                    <td>Ongkos Angkut Perak - {{ $item->first()->tarif->tujuan->tujuanInfo->nama }}</td>
                                    <td class="text-center">{{ $item->first()->tipe }}'</td>
                                    <td class="text-center">{{ $item->count() }} Rit </td>
                                    <td class="text-center">X</td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format(round(($item->first()->tarif_nominal/0.97))) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format(round(($item->first()->tarif_nominal/0.97)) * $item->count()) }}</span>
                                        </div>
                                    </td>
                                </tr>

                            @endforeach
                            @foreach ($data as $ord)
                                @foreach ($ord as $item)
                                    @if ($item->tagihans->sum('jumlah')>0)
                                    @php
                                        $total += $item->tagihans->sum('jumlah');
                                    @endphp
                                        @foreach ($item->tagihans as $tag)
                                        <tr>
                                            {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                            <td colspan="4">{{ $tag->nama }}</td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                            <tr style="height: 20px !important">
                                <td colspan="7" style="border-bottom: 1px solid black"></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td class="text-center">{{ $rit }} Rit</td>
                                <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($total) }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom border-dark">
                                <td colspan="3"></td>
                                <td class="fw-bold text-center" colspan="2">PPH 21 (3%)</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($pph) }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom border-dark">
                                <td colspan="5"></td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($total - $pph) }}</span>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <div class="row mt-3">
                            <div class="col-9">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 100px">Terbilang</td>
                                        <td>: {{ strtoupper(terbilang($total)) }} RUPIAH</td>
                                    </tr>
                                    <tr>
                                        <td>Keterangan</td>
                                        <td>: </td>
                                    </tr>
                                </table>
                                <table style="font-size: .7rem" class="mt-2">
                                    @foreach ($data as $orderss)
                                        @foreach ($orderss as $item)
                                        <tr>
                                            <td style="width: 150px">{{ $item->container }}/{{ $item->seal }}</td>
                                            <td style="width: 50px"> {{ $item->tipe }}'</td>
                                            <td style="width: 100px"> {{ $item->kendaraan->nopol }}</td>
                                            @if ($item->order)
                                            <td style="width: 100px"> {{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                            @else
                                            <td>-</td>
                                            @endif
                                            <td style="width: 150px"> {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                            @if ($item->order)
                                            <td style="width: 250px"> {{ $item->order->tarif->customer->nama ?? '-' }} <td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </table>
                            </div>
                            <div class="col-3">
                                <div class="text-center" style="font-size: .7rem">
                                    <p>Surabaya, {{ is_null($order->tgl_invoice)?'-':tanggal($order->tgl_invoice) }}</p>
                                    <div style="height: 2cm"></div>
                                    (<input type="text" value="Rara" class="text-center pengirim" style="border:none; width:130px"/>)
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @else
                    @if ($tipe=='R1'||$tipe=='VENDOR')
                    @php
                        $pph = 0;
                    @endphp
                    <div class="invoice-box first-page">
                        {{-- @if ($order->kendaraan->milik=='vendor')
                        <div class="header d-flex" style="gap:5px; width:100%">
                            <img src="{{ asset('logo.png') }}" alt="logo" style="height: 60px; width: 30%" class="img-fluid">
                            <div style="width: 40%; margin-left:35px">
                                <table style="font-size:.7rem">
                                    <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDERA</td></tr>
                                    <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                                    <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                                    <tr><td>Email : info@ptras.id</td></tr>
                                </table>
                            </div>
                            <div style="width:30%; ">
                                <table style="width: 100%; font-size: .7rem; font-weight:bold; border: 2px solid black">
                                    <tr><td class="text-center" style="line-spacing: 1rem">INVOICE</td></tr>
                                </table>
                            </div>
                        </div>
                        @endif --}}
                        <div class="row mt-3">
                            <div class="col-6">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 120px">No. Invoice</td>
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->invoice ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Customer</td>
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->customer->nama }} </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Attn</td>
                                        <td style="width:5px">:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Alamat</td>
                                        <td style="vertical-align: top">:</td>
                                        <td>{{ $order->customer->alamat }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $order->tarif->customer->kota }}</td>
                                    </tr> --}}
                                </table>
                            </div>
                        </div>

                        <table class="mt-2 w-100 tables" style="font-size: .7rem">
                            <thead>
                                <tr class="heading">
                                    {{-- <td>No</td> --}}
                                    <td>Uraian</td>
                                    <td>Tipe</td>
                                    <td>Jumlah</td>
                                    <td>X</td>
                                    <td>Tarif</td>
                                    <td>Sub Total</td>
                                </tr>
                            </thead>
                            @php
                                $total = 0;
                                $lain_lain = 0;
                                $rit = 0;
                                $pph = 0;
                            @endphp
                            @foreach ($data as $item)
                            @php
                                $total += $item->first()->tarif_nominal * $item->count();
                                $lain_lain += $item->sum('lain_lain');
                                $rit += $item->count();
                                $pph += round($item->sum('pph_23'));
                            @endphp
                                <tr>
                                    {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                    <td>Ongkos Angkut Perak - {{ $item->first()->tarif->tujuan->tujuanInfo->nama }}</td>
                                    <td class="text-center">{{ $item->first()->tipe }}'</td>
                                    <td class="text-center">{{ $item->count() }} Rit </td>
                                    <td class="text-center">X</td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->first()->tarif_nominal) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->first()->tarif_nominal * $item->count()) }}</span>
                                        </div>
                                    </td>
                                </tr>

                            @endforeach
                            @foreach ($data as $ord)
                                @foreach ($ord as $item)
                                    @if ($item->tagihans->sum('jumlah')>0)
                                    @php
                                        $total += $item->tagihans->sum('jumlah');
                                    @endphp
                                        @foreach ($item->tagihans as $tag)
                                        <tr>
                                            {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                            <td colspan="4">{{ $tag->nama }}</td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                            <tr style="height: 20px !important">
                                <td colspan="7" style="border-bottom: 1px solid black"></td>
                            </tr>
                            @if ($order->customer->pph_23==1)
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-center">{{ $rit }} Rit</td>
                                    <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                    <td class="fw-bold">
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($total) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-bottom border-dark">
                                    <td colspan="3"></td>
                                    <td class="fw-bold text-center" colspan="2">PPH 23 (2%)</td>
                                    <td class="fw-bold">
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($pph) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-bottom border-dark">
                                    <td colspan="5"></td>
                                    <td class="fw-bold">
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($total - $pph) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @else
                            <tr class="border-bottom border-dark">
                                <td colspan="2"></td>
                                <td class="text-center">{{ $rit }} Rit</td>
                                <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($total) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </table>

                        <div class="row mt-3">
                            <div class="col-9">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 100px">Terbilang</td>
                                        <td>: {{ strtoupper(terbilang($total)) }} RUPIAH</td>
                                    </tr>
                                    <tr>
                                        <td>Keterangan</td>
                                        <td>: </td>
                                    </tr>
                                </table>
                                <table style="font-size: .7rem" class="mt-2">
                                    @foreach ($data as $orderss)
                                        @foreach ($orderss as $item)
                                        <tr>
                                            <td style="width: 150px">{{ $item->container }}/{{ $item->seal }}</td>
                                            <td style="width: 50px"> {{ $item->tipe }}'</td>
                                            <td style="width: 100px"> {{ $item->kendaraan->nopol }}</td>
                                            <td style="width: 150px"> {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </table>
                            </div>
                            <div class="col-3">
                                <div class="text-center" style="font-size: .7rem">
                                    <p>Surabaya, {{ is_null($order->tgl_invoice)?'-':tanggal($order->tgl_invoice) }}</p>
                                    <div style="height: 2cm"></div>
                                    (<input type="text" value="Rara" class="text-center pengirim" style="border:none; width:130px"/>)
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if ($tipe=='R2')
                    <div class="invoice-box first-page">
                        <div class="header d-flex" style="gap:5px; width:100%">
                            <img src="{{ asset('logo.png') }}" alt="logo" style="height: 60px; width: 30%" class="img-fluid">
                            <div style="width: 40%; margin-left:35px">
                                <table style="font-size:.7rem">
                                    <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDERA</td></tr>
                                    <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                                    <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                                    <tr><td>Email : info@ptras.id</td></tr>
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
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->invoice ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Customer</td>
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->customer->nama }} </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Attn</td>
                                        <td style="width:5px">:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Alamat</td>
                                        <td style="vertical-align: top">:</td>
                                        <td>{{ $order->customer->alamat }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $order->tarif->customer->kota }}</td>
                                    </tr> --}}
                                </table>
                            </div>
                        </div>

                        <table class="mt-2 w-100 tables" style="font-size: .7rem">
                            <thead>
                                <tr class="heading">
                                    {{-- <td>No</td> --}}
                                    <td>Uraian</td>
                                    <td>Tipe</td>
                                    <td>Jumlah</td>
                                    <td>X</td>
                                    <td>Tarif</td>
                                    <td>Sub Total</td>
                                </tr>
                            </thead>
                            @php
                                $total = 0;
                                $lain_lain = 0;
                                $rit = 0;
                                $pph = 0;
                            @endphp
                            @foreach ($data as $item)
                            @php
                                $total += $item->first()->tarif_nominal * $item->count();
                                $lain_lain += $item->sum('lain_lain');
                                $rit += $item->count();
                                $pph += round($item->sum('pph_23'));
                            @endphp
                                <tr>
                                    {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                    <td>Ongkos Angkut Perak - {{ $item->first()->tarif->tujuan->tujuanInfo->nama }}</td>
                                    <td class="text-center">{{ $item->first()->tipe }}'</td>
                                    <td class="text-center">{{ $item->count() }} Rit </td>
                                    <td class="text-center">X</td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->first()->tarif_nominal) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->first()->tarif_nominal * $item->count()) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($data as $ord)
                                @foreach ($ord as $item)
                                    @if ($item->tagihans->sum('jumlah')>0)
                                    @php
                                        $total += $item->tagihans->sum('jumlah');
                                    @endphp
                                        @foreach ($item->tagihans as $tag)
                                        <tr>
                                            {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                            <td colspan="4">{{ $tag->nama }}</td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                            <tr style="height: 20px !important">
                                <td colspan="7" style="border-bottom: 1px solid black"></td>
                            </tr>
                            @if ($order->customer->pph_23==1)
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-center">{{ $rit }} Rit</td>
                                    <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                    <td class="fw-bold">
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($total) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-bottom border-dark">
                                    <td colspan="3"></td>
                                    <td class="fw-bold text-center" colspan="2">PPH 23 (2%)</td>
                                    <td class="fw-bold">
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($pph) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-bottom border-dark">
                                    <td colspan="5"></td>
                                    <td class="fw-bold">
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($total - $pph) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @else
                            <tr class="border-bottom border-dark">
                                <td colspan="2"></td>
                                <td class="text-center">{{ $rit }} Rit</td>
                                <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($total) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </table>

                        <div class="row mt-3">
                            <div class="col-9">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 100px">Terbilang</td>
                                        <td>: {{ strtoupper(terbilang($total)) }} RUPIAH</td>
                                    </tr>
                                    <tr>
                                        <td>Keterangan</td>
                                        <td>: </td>
                                    </tr>
                                </table>
                                <table style="font-size: .7rem" class="mt-2">
                                    @foreach ($data as $orderss)
                                        @foreach ($orderss as $item)
                                        <tr>
                                            <td style="width: 150px">{{ $item->container }}/{{ $item->seal }}</td>
                                            <td style="width: 50px"> {{ $item->tipe }}'</td>
                                            <td style="width: 100px"> {{ $item->kendaraan->nopol }}</td>
                                            <td style="width: 150px"> {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </table>
                                <div class="mt-3">
                                    <span>Pembayaran dapat dilakukan melalui:</span>
                                    <table style="font-size: .7rem">
                                        <tr>
                                            <td style="width: 150px">Rekening No.</td>
                                            <td>: 1400 046 005 006</td>
                                        </tr>
                                        <tr>
                                            <td>Atas Nama</td>
                                            <td>: PT. RAHMAT ALAM SAMUDERA</td>
                                        </tr>
                                        <tr>
                                            <td>Bank</td>
                                            <td>: Mandiri Cabang Indrapura Surabaya</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="text-center" style="font-size: .7rem">
                                    <p>Surabaya, {{ is_null($order->tgl_invoice)?'-':tanggal($order->tgl_invoice) }}</p>
                                    <div style="height: 2cm"></div>
                                    (<input type="text" value="Rara" class="text-center pengirim" style="border:none; width:130px"/>)
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-break"></div>
                    <div class="invoice-box" style="margin-top: 30px !important">
                        <div class="header d-flex" style="gap:5px; width:100%">
                            <img src="{{ asset('logo.png') }}" alt="logo" style="height: 60px; width: 30%" class="img-fluid">
                            <div style="width: 40%; margin-left:35px">
                                <table style="font-size:.7rem">
                                    <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDERA</td></tr>
                                    <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                                    <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                                    <tr><td>Email : info@ptras.id</td></tr>
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
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->invoice ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Customer</td>
                                        <td style="width:5px">:</td>
                                        <td>{{ $order->customer->nama }} </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60px">Attn</td>
                                        <td style="width:5px">:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Alamat</td>
                                        <td style="vertical-align: top">:</td>
                                        <td>{{ $order->customer->alamat }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $order->tarif->customer->kota }}</td>
                                    </tr> --}}
                                </table>
                            </div>
                        </div>

                        <table class="mt-2 w-100 tables" style="font-size: .7rem">
                            <thead>
                                <tr class="heading">
                                    {{-- <td>No</td> --}}
                                    <td>Uraian</td>
                                    <td>Tipe</td>
                                    <td>Jumlah</td>
                                    <td>X</td>
                                    <td>Tarif</td>
                                    <td>Sub Total</td>
                                </tr>
                            </thead>
                            @php
                                $total = 0;
                                $lain_lain = 0;
                                $rit = 0;
                                $pph = 0;
                            @endphp
                            @foreach ($data as $item)
                            @php
                                $total += $item->first()->tarif_nominal * $item->count();
                                $lain_lain += $item->sum('lain_lain');
                                $rit += $item->count();
                                $pph += round($item->sum('pph_23'));
                            @endphp
                                <tr>
                                    {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                    <td>Ongkos Angkut Perak - {{ $item->first()->tarif->tujuan->tujuanInfo->nama }}</td>
                                    <td class="text-center">{{ $item->first()->tipe }}'</td>
                                    <td class="text-center">{{ $item->count() }} Rit </td>
                                    <td class="text-center">X</td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->first()->tarif_nominal) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->first()->tarif_nominal * $item->count()) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($data as $ord)
                                @foreach ($ord as $item)
                                    @if ($item->tagihans->sum('jumlah')>0)
                                    @php
                                        $total += $item->tagihans->sum('jumlah');
                                    @endphp
                                        @foreach ($item->tagihans as $tag)
                                        <tr>
                                            {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                                            <td colspan="4">{{ $tag->nama }}</td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="price d-flex justify-content-between px-2">
                                                    <span>Rp</span>
                                                    <span>{{ number_format($tag->jumlah) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endforeach
                            <tr style="height: 20px !important">
                                <td colspan="7" style="border-bottom: 1px solid black"></td>
                            </tr>
                            @if ($order->customer->pph_23==1)
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-center">{{ $rit }} Rit</td>
                                    <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                    <td class="fw-bold">
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($total) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-bottom border-dark">
                                    <td colspan="3"></td>
                                    <td class="fw-bold text-center" colspan="2">PPH 23 (2%)</td>
                                    <td class="fw-bold">
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($pph) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-bottom border-dark">
                                    <td colspan="5"></td>
                                    <td class="fw-bold">
                                        <div class="price d-flex justify-content-between px-2">
                                            <span>Rp</span>
                                            <span>{{ number_format($total - $pph) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @else
                            <tr class="border-bottom border-dark">
                                <td colspan="2"></td>
                                <td class="text-center">{{ $rit }} Rit</td>
                                <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($total) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </table>

                        <div class="row mt-3">
                            <div class="col-9">
                                <table style="font-size: .7rem">
                                    <tr>
                                        <td style="width: 100px">Terbilang</td>
                                        <td>: {{ strtoupper(terbilang($total)) }} RUPIAH</td>
                                    </tr>
                                    <tr>
                                        <td>Keterangan</td>
                                        <td>: </td>
                                    </tr>
                                </table>
                                <table style="font-size: .7rem" class="mt-2">
                                    @foreach ($data as $orderss)
                                        @foreach ($orderss as $item)
                                        <tr>
                                            <td style="width: 150px">{{ $item->container }}/{{ $item->seal }}</td>
                                            <td style="width: 50px"> {{ $item->tipe }}'</td>
                                            <td style="width: 100px"> {{ $item->kendaraan->nopol }}</td>
                                            <td style="width: 150px"> {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </table>
                                <div class="mt-3">
                                    <span>Pembayaran dapat dilakukan melalui:</span>
                                    <table style="font-size: .7rem">
                                        <tr>
                                            <td style="width: 150px">Rekening No.</td>
                                            <td>: 1400 046 005 006</td>
                                        </tr>
                                        <tr>
                                            <td>Atas Nama</td>
                                            <td>: PT. RAHMAT ALAM SAMUDERA</td>
                                        </tr>
                                        <tr>
                                            <td>Bank</td>
                                            <td>: Mandiri Cabang Indrapura Surabaya</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="text-center" style="font-size: .7rem">
                                    <p>Surabaya, {{ is_null($order->tgl_invoice)?'-':tanggal($order->tgl_invoice) }}</p>
                                    <img src="{{ asset('assets/img/ttd-trucking.jpg') }}" style="width: 151px; height:94px; position: relative; top:-10px"><br>
                                    (<input type="text" value="Rara" class="text-center pengirim" style="border:none; width:130px"/>)
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function(){
            var _total = @json($total);
            var _pph = @json($pph);
            var _rit = @json($rit);
            var _lain_lain = @json($lain_lain);
            $('#_total').val(_total);
            $('#_pph').val(_pph);
            $('#_rit').val(_rit);
            $('#_lain_lain').val(_lain_lain);
        })

        $('#pengirim').change(function (e) {
            pengirim()
        });
        pengirim()

        function pengirim(){
            var val = $('#pengirim').val();
            $('.pengirim').val(val);
        }
    </script>
@endsection
