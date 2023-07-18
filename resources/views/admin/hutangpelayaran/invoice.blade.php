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

            .first-page {
                width: 100%;
                height: 100%;
                position: absolute;
                top: -180px;
            }

            .first-page2 {
                width: 100%;
                height: 100%;
                position: absolute;
                top: -190px;
            }

            #print,
            #print * {
                visibility: visible;
                font-size: .7rem !important;
            }

            #print {
                width: 100%;
                position: relative;
                left: 0;
                /* top: -20px; */
            }

            #table td,
            #table th {
                border: 1px solid black;
            }

            #print {
                color: #000;
            }

            /* .table tr td {
                                                                                                                                                                                                                                            padding: 0px 2px;
                                                                                                                                                                                                                                            border-left: 1px solid !important;
                                                                                                                                                                                                                                            border-right: 1px solid !important;
                                                                                                                                                                                                                                            border-bottom: none;
                                                                                                                                                                                                                                            border-top: none;
                                                                                                                                                                                                                                        }

                                                                                                                                                                                                                                        .table>tbody>tr>td:first-child {
                                                                                                                                                                                                                                            padding: 0px 2px !important;
                                                                                                                                                                                                                                        } */

            .page-break {
                page-break-after: always;
                overflow: hidden;
            }
        }

        tr.heading td {
            border: 1px solid black;
            text-align: center;
        }

        .table tr td {
            vertical-align: middle;
            padding: 3px 3px;
        }
    </style>
@endsection
@section('content')
    @php
        // function terbilang($angka)
        // {
        //     $angka = (float) $angka;
        //     $bilangan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        //     if ($angka < 12) {
        //         return $bilangan[$angka];
        //     } elseif ($angka < 20) {
        //         return $bilangan[$angka - 10] . ' belas';
        //     } elseif ($angka < 100) {
        //         $hasil_bagi = (int) ($angka / 10);
        //         $hasil_mod = $angka % 10;
        //         return trim(sprintf('%s puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
        //     } elseif ($angka < 200) {
        //         return 'seratus ' . terbilang($angka - 100);
        //     } elseif ($angka < 1000) {
        //         $hasil_bagi = (int) ($angka / 100);
        //         $hasil_mod = $angka % 100;
        //         return trim(sprintf('%s ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
        //     } elseif ($angka < 2000) {
        //         return 'seribu ' . terbilang($angka - 1000);
        //     } elseif ($angka < 1000000) {
        //         $hasil_bagi = (int) ($angka / 1000);
        //         $hasil_mod = $angka % 1000;
        //         return trim(sprintf('%s ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
        //     } elseif ($angka < 1000000000) {
        //         $hasil_bagi = (int) ($angka / 1000000);
        //         $hasil_mod = $angka % 1000000;
        //         return trim(sprintf('%s juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
        //     } elseif ($angka < 1000000000000) {
        //         $hasil_bagi = (int) ($angka / 1000000000);
        //         $hasil_mod = fmod($angka, 1000000000);
        //         return trim(sprintf('%s miliar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
        //     } else {
        //         return 'Angka terlalu besar';
        //     }
        // }
        
        // function tanggal($date)
        // {
        //     $tanggal = date('d', strtotime($date));
        //     $bulan = date('m', strtotime($date));
        //     $tahun = date('Y', strtotime($date));
        
        //     $nama_bulan = '';
        //     switch ($bulan) {
        //         case '01':
        //             $nama_bulan = 'Januari';
        //             break;
        //         case '02':
        //             $nama_bulan = 'Februari';
        //             break;
        //         case '03':
        //             $nama_bulan = 'Maret';
        //             break;
        //         case '04':
        //             $nama_bulan = 'April';
        //             break;
        //         case '05':
        //             $nama_bulan = 'Mei';
        //             break;
        //         case '06':
        //             $nama_bulan = 'Juni';
        //             break;
        //         case '07':
        //             $nama_bulan = 'Juli';
        //             break;
        //         case '08':
        //             $nama_bulan = 'Agustus';
        //             break;
        //         case '09':
        //             $nama_bulan = 'September';
        //             break;
        //         case '10':
        //             $nama_bulan = 'Oktober';
        //             break;
        //         case '11':
        //             $nama_bulan = 'November';
        //             break;
        //         case '12':
        //             $nama_bulan = 'Desember';
        //             break;
        //     }
        //     return $tanggal . ' ' . $nama_bulan . ' ' . $tahun;
        // }
    @endphp
    <div class="container">
        <div class="card p-3 shadow">
            {{-- @if (is_null($order->invoice))
                <div class="d-flex" style="gap:5px">
                    @if ($null_job > 0 && $order->customer_id == 2)
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
                                <input type="hidden" name="order_id" value="{{ implode(',', $order_id) }}">
                                <input type="hidden" name="order" value="{{ $order->id }}">
                                <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
                                <input type="hidden" name="tipe" value="{{ $tipe }}">
                                <input type="hidden" name="pph" id="_pph">
                                <input type="hidden" name="total" id="_total">
                                <input type="hidden" name="rit" id="_rit">
                                <input type="hidden" name="lain_lain" id="_lain_lain">
                                <button type="submit" onclick="return confirm('Apa anda yakin?')"
                                    class="btn btn-sm btn-success mb-3">Submit Invoice</button>
                            </form>
                        @endif
                    @endif
                </div>
            @else
                <script>
                    window.print();
                </script>
                <button onclick="window.print()" class="btn btn-sm btn-success mb-3">Print</button>
            @endif --}}
        </div>
        <div class="card p-3 mt-3">
            <div id="print">
                @php
                    $pph = 0;
                @endphp
                <div class="invoice-box first-page">
                    <div class="header d-flex" style="gap:5px; width:100%">
                        <div style="width: 100%;">
                            <table style="font-size:.9rem; width: 100%;">
                                <tr>
                                    <td class="fw-bold" style="text-align: center">BUKTI BANK KELUAR</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-8">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 120px">Dibayarkan kepada</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ $order->invoice ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 120px">Nama</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ $order->invoice ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 120px">Tanggal</td>
                                    <td style=" width:5px">:</td>
                                    <td>{{ $order->invoice ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <table class="mt-2 tables" style="font-size: .7rem; width:100%">
                        <thead>
                            <tr class="heading">
                                <td>Perkiraan</td>
                                <td colspan="6" style="width: 50%">Uraian</td>
                                <td>Jumlah</td>
                            </tr>
                        </thead>
                        @php
                            $total = 0;
                            $lain_lain = 0;
                            $rit = 0;
                            $pph = 0;
                        @endphp

                        @foreach ($data as $ord)
                            @foreach ($ord as $item)
                                @if ($item->jumlah > 0)
                                    @php
                                        $total += $item->jumlah;
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                        <tbody>
                            @if (is_null($data))
                                <tr style="height: 20px !important">
                                    <td colspan="6" style="border-bottom: 1px solid black;"></td>
                                </tr>
                            @else
                                @foreach ($data as $datas => $orders)
                                    @foreach ($orders as $order)
                                        <td></td>
                                        <td colspan="6" style="width: 50%;">
                                            {{ $order->order->jadwal_kapal->pelayaran->nama }}</td>
                                        <td style="text-align:end;">{{ $order->jumlah }}</td>
                                    @endforeach
                                @endforeach
                            @endif
                            <tr class="border-bottom border-dark">
                                <td class="text-center"></td>
                                <td class="fw-bold text-center" colspan="6" style="width: 50%">TOTAL</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($total) }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row mt-3">
                        <div class="col-9">
                            {{-- <table style="font-size: .7rem">
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
                            </table> --}}
                        </div>
                        <div class="col-3">
                            <div class="text-center" style="font-size: .7rem">
                                <p>Surabaya,
                                    {{-- {{ is_null($order->tgl_invoice) ? '-' : tanggal($order->tgl_invoice) }} --}}
                                </p>
                                <div style="height: 1.5cm"></div>
                                (<input type="text" value="Totok" class="text-center"
                                    style="border:none; width:130px" />)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            var _total = @json($total);
            var _pph = @json($pph);
            var _rit = @json($rit);
            var _lain_lain = @json($lain_lain);
            $('#_total').val(_total);
            $('#_pph').val(_pph);
            $('#_rit').val(_rit);
            $('#_lain_lain').val(_lain_lain);
        })
    </script>
@endsection
