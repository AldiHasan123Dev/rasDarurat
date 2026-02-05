@extends('layouts.admin')
@section('style')
    <style>
        @media print {

            body * {
                visibility: hidden;
                color: #000;
                -webkit-print-color-adjust:exact !important;
                print-color-adjust:exact !important;
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
                font-size: .6rem !important;
            }

            #print {
                width: 100%;
                position: absolute;
                left: 0px;
                top: 0px;
                padding: 0px;
            }

            #table td,
            #table th {
                border: 1px solid rgb(255, 0, 0);
                padding: 0px !important;
            }

            #print {
                color: #000;
            }

            .page-break {
                page-break-after: always;
                overflow: hidden;
            }

            .bg-red{
                background-color: red !important;
                color: #ffffff !important;
            }
            .table tr td {
                padding: 0px 5px !important;
                color: #000;
                font-weight: 600;
            }
        }

        #print{
            padding: 10px;
            background-color: #fff;
        }

        .bg-red{
            background-color: red !important;
            color: #ffffff !important;
            font-weight: bold;
        }

        tr.heading td {
            border: 1px solid rgb(255, 0, 0);
            text-align: center;
        }

        tr th{
            border: 1px solid rgb(255, 0, 0);
            text-align: center;
        }

        .table tr td {
            vertical-align: middle;
            padding: 3px 3px;
            border: 1px solid rgb(255, 0, 0);
        }
        .table tbody tr td:first-child{
            padding-left: 10px !important;
        }

        .vertical{
            text-align:center;
            white-space:nowrap;
            transform-origin:50% 50%;
            transform: rotate(-90deg);
        }
    </style>
@endsection
@section('content')
@php
if (!function_exists('terbilang')) {
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
}
@endphp
    <div id="print">
        <table class="w-100 table">
            <thead>
                <tr>
                    <th style="width: 100px; color:red" colspan="2">Dibayar Kepada</th>
                    <th style="vertical-align: middle; color:red">BUKTI BANK KELUAR</th>
                    <th style="color: red">Nomor</th>
                    <th style="width: 120px"></th>
                </tr>
                <tr>
                    <th colspan="2">{{ strtoupper($jadwal_kapal->pelayaran->nama) }}</th>
                    <th style="color: red; border:none">{{ $hp->jurnal_opp }}</th>
                    <th style="color: red">Tanggal</th>
                    <th style="width: 120px"></th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: red" class="text-white">
                    <td style="background: white" id="rowspan-opp" rowspan="{{ ($jobs->count() + $opp) + 2 }}"></td>
                    {{-- <td rowspan="{{ ($jobs->count() * 6) + 2 }}" style="background-color:white;transform: rotate(180deg);white-space: nowrap; writing-mode: vertical-rl; ms-writing-mode: tb-rl; -webkit-writing-mode: vertical-rl; color:red">KEPERLUAN INTERN</td> --}}
                    <td class="bg-red text-center">PERKIRAAN</td>
                    <td class="bg-red text-center" colspan="2">URAIAN</td>
                    <td class="bg-red text-center">JUMLAH</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center" colspan="2">{{ strtoupper($jadwal_kapal->kapal->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }}</td>
                    <td></td>
                </tr>
                @foreach ($jobs as $list)
                    @foreach ($list->where('opp','!=',0)->groupBy('opp') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">OPP({{ $item->count() }}X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}) ({{ implode(',',$item->sortBy('order.no_job')->pluck('order.no_job')->toArray()) }})
                                </td>
                                <td class="text-end">{{ number_format($item->first()->opp * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">OPP(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                                <td class="text-end">{{ number_format($item->first()->opp,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->where('thc','!=',0)->groupBy('thc') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">THC LoLo SBY</td>
                                <td class="text-end">{{ number_format($item->first()->thc * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">THC Lolo SBY</td>
                                <td class="text-end">{{ number_format($item->first()->thc,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->where('apbs','!=',0)->groupBy('apbs') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">APBS</td>
                                <td class="text-end">{{ number_format($item->first()->apbs * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">APBS</td>
                                <td class="text-end">{{ number_format($item->first()->apbs,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->where('cleaning','!=',0)->groupBy('cleaning') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">Cleaning</td>
                                <td class="text-end">{{ number_format($item->first()->cleaning * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">Cleaning</td>
                                <td class="text-end">{{ number_format($item->first()->cleaning,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->where('opp_stamp','!=',0)->groupBy('opp_stamp') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->opp_stamp * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->opp_stamp,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->where('hp_seal','!=',0)->groupBy('hp_seal') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">SEAL</td>
                                <td class="text-end">{{ number_format($item->first()->hp_seal * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">SEAL</td>
                                <td class="text-end">{{ number_format($item->first()->hp_seal,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->where('lss','!=',0)->groupBy('lss') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">LSS</td>
                                <td class="text-end">{{ number_format($item->first()->lss * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">LSS</td>
                                <td class="text-end">{{ number_format($item->first()->lss,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                      @foreach ($list->where('vgm','!=',0)->groupBy('vgm') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">VGM Terminal</td>
                                <td class="text-end">{{ number_format($item->first()->vgm * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">VGM Terminal</td>
                                <td class="text-end">{{ number_format($item->first()->vgm,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr style="height:12px">
                        <td></td>
                        <td colspan="2"></td>
                        <td></td>
                    </tr>
                @endforeach
                @if ($hp->pph>0)
                <tr>
                    <td></td>
                    <td colspan="2">PPh (2%)</td>
                    <td class="text-end text-danger">- {{ number_format($hp->pph,2,',','.') }}</td>
                </tr>
                @endif
                @if ($hp->pembulatan!=0)
                <tr>
                    <td></td>
                    <td colspan="2">Pembulatan</td>
                    <td class="text-end text-primary">{{ number_format($hp->pembulatan,2,',','.') }}</td>
                </tr>
                @endif
                {{-- @foreach ($opp as $list)
                    @foreach ($list->groupBy('order.job') as $item)
                        <tr>
                            <td></td>
                            <td colspan="2">OPP(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                            <td class="text-end">{{ number_format($item->first()->opp,2,',','.') }}</td>
                        </tr>
                    @endforeach
                @endforeach --}}
                <tr style="border: 2px solid red">
                    <td colspan="2" style="color:red">Ch/ BG. No :</td>
                    <td class="fw-bold" colspan="2">{{ $hp->no_bg_opp }} ({{ date('d-m-Y',strtotime($hp->tgl_bg_opp)) }})</td>
                    <td class="text-end fw-bold">{{ number_format($hp->nominal_bg_opp,2,',','.') }}</td>
                </tr>
            </tbody>
        </table>
        <table class="w-100 table" style="position: relative; top:-10px">
            <thead>
                <tr>
                    <td class="text-start" style="color: red">TERBILANG :</td>
                    <td colspan="7" style="text-transform: uppercase; font-weight:bold">{{ terbilang($hp->nominal_bg_opp) }} RUPIAH</td>
                </tr>
                <tr>
                    <td class="text-start" colspan="2"><u style="color: red">CATATAN :</u></td>
                    <td class="bg-red">Pembukuan</td>
                    <td class="bg-red">Mengetahui</td>
                    <td class="bg-red" colspan="2" style="width: 100px">Kasir</td>
                    <td class="bg-red">Penerima</td>
                </tr>
            </thead>
            <tbody>
                <tr style="border: none !important; height:25px">
                    <td colspan="2"></td>
                    <td></td>
                    <td></td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        @if ($hp->no_bg_opt)
        <p class="page-break"></p>
        <table class="w-100 table">
            <thead>
                <tr>
                    <th style="width: 100px; color:red" colspan="2">Dibayar Kepada</th>
                    <th style="vertical-align: middle; color:red">BUKTI BANK KELUAR</th>
                    <th style="color: red">Nomor</th>
                    <th style="width: 120px"></th>
                </tr>
                <tr>
                    <th colspan="2">{{ strtoupper($jadwal_kapal->pelayaran->nama) }}</th>
                    <th style="color: red">{{ $hp->jurnal_opt }}</th>
                    <th style="color: red">Tanggal</th>
                    <th style="width: 120px"></th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: red" class="text-white">
                    <td style="background: white" id="rowspan-opt" rowspan="{{ ($opt * 2) +2 }}"></td>
                    {{-- <td rowspan="{{ ($jobs->count() * 6) + 2 }}" style="background-color:white;transform: rotate(180deg);white-space: nowrap; writing-mode: vertical-rl; ms-writing-mode: tb-rl; -webkit-writing-mode: vertical-rl; color:red">KEPERLUAN INTERN</td> --}}
                    <td class="bg-red text-center">PERKIRAAN</td>
                    <td class="bg-red text-center" colspan="2">URAIAN</td>
                    <td class="bg-red text-center">JUMLAH</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center" colspan="2">{{ strtoupper($jadwal_kapal->kapal->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }}</td>
                    <td></td>
                </tr>
                @foreach ($jobs as $list)
                    @foreach ($list->where('opt','!=',0)->groupBy('opt') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">OPT({{ $item->count() }}X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}) ({{ implode(',',$item->sortBy('order.no_job')->pluck('order.no_job')->toArray()) }})
                                </td>
                                <td class="text-end">{{ number_format($item->first()->opt * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">OPT(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                                <td class="text-end">{{ number_format($item->first()->opt,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->where('opt_stamp','!=',0)->groupBy('opt_stamp') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->opt_stamp * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->opt_stamp,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @if ($list->where('opt','!=',0)->groupBy('opt')->count()>0 || $list->where('opt_stamp','!=',0)->groupBy('opt_stamp')->count()>0)
                        <tr style="height:12px">
                            <td></td>
                            <td colspan="2"></td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                {{-- @foreach ($opp as $list)
                    @foreach ($list->groupBy('order.job') as $item)
                        <tr>
                            <td></td>
                            <td colspan="2">OPP(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                            <td class="text-end">{{ number_format($item->first()->opp,2,',','.') }}</td>
                        </tr>
                    @endforeach
                @endforeach --}}
                @if ($hp->opt_pph>0)
                <tr>
                    <td></td>
                    <td colspan="2">PPh OPT (2%)</td>
                    <td class="text-end text-danger">- {{ number_format($hp->opt_pph,2,',','.') }}</td>
                </tr>
                @endif
                <tr style="border: 2px solid red">
                    <td style="color:red" colspan="2">Ch/ BG. No :</td>
                    <td class="fw-bold" colspan="2">{{ $hp->no_bg_opt }} ({{ date('d-m-Y',strtotime($hp->tgl_bg_opt)) }})</td>
                    <td class="text-end fw-bold">{{ number_format($hp->nominal_bg_opt,2,',','.') }}</td>
                </tr>
            </tbody>
        </table>
        <table class="w-100 table" style="position: relative; top:-10px">
            <thead>
                <tr>
                    <td class="text-start" style="color: red">TERBILANG :</td>
                    <td colspan="7" style="text-transform: uppercase; font-weight:bold">{{ terbilang($hp->nominal_bg_opt) }} RUPIAH</td>
                </tr>
                <tr>
                    <td class="text-start" colspan="2"><u style="color: red">CATATAN :</u></td>
                    <td class="bg-red">Pembukuan</td>
                    <td class="bg-red">Mengetahui</td>
                    <td class="bg-red" colspan="2" style="width: 100px">Kasir</td>
                    <td class="bg-red">Penerima</td>
                </tr>
            </thead>
            <tbody>
                <tr style="border: none !important; height:25px">
                    <td colspan="2"></td>
                    <td></td>
                    <td></td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        @endif
        @if ($hp->no_bg_ut)
        <p class="page-break"></p>
        <table class="w-100 table">
            <thead>
                <tr>
                    <th style="width: 100px; color:red" colspan="2">Dibayar Kepada</th>
                    <th style="vertical-align: middle; color:red">BUKTI BANK KELUAR</th>
                    <th style="color: red">Nomor</th>
                    <th style="width: 120px"></th>
                </tr>
                <tr>
                    <th colspan="2">{{ strtoupper($jadwal_kapal->pelayaran->nama) }}</th>
                    <th style="color: red">{{ $hp->jurnal_ut }}</th>
                    <th style="color: red">Tanggal</th>
                    <th style="width: 120px"></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $a = 1;
                    $b = 2;
                    $c = 2;
                    if(!is_null($hp->penambahan) && $hp->penambahan_nominal != 0){
                        $a = $jobs->count() + 2;
                        $b = $jobs->count() + 1;
                        $c = $jobs->count();

                    }
                @endphp
                <tr style="background-color: red" class="text-white">
                    <td style="background: white" id="rowspan-ut" rowspan="{{ ($jobs->count() + $ut) + $a}}"></td>
                    {{-- <td rowspan="{{ ($jobs->count() * 6) + 2 }}" style="background-color:white;transform: rotate(180deg);white-space: nowrap; writing-mode: vertical-rl; ms-writing-mode: tb-rl; -webkit-writing-mode: vertical-rl; color:red">KEPERLUAN INTERN</td> --}}
                    <td class="bg-red text-center">PERKIRAAN</td>
                    <td class="bg-red text-center" colspan="2">URAIAN</td>
                    <td class="bg-red text-center">JUMLAH</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center" colspan="2">{{ strtoupper($jadwal_kapal->kapal->nama) }} VOY. {{ strtoupper($jadwal_kapal->voyage) }}</td>
                    <td></td>
                </tr>
                @foreach ($jobs as $list)
                    @foreach ($list->where('ut','!=',0)->groupBy('ut') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">UT ({{ $item->count() }}X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}) ({{ implode(',',$item->sortBy('order.no_job')->pluck('order.no_job')->toArray()) }})
                                </td>
                                <td class="text-end">{{ number_format($item->first()->ut * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">UT (1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                                <td class="text-end">{{ number_format($item->first()->ut,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($list->where('ut_stamp','!=',0)->groupBy('ut_stamp') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->ut_stamp * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">STAMP</td>
                                <td class="text-end">{{ number_format($item->first()->ut_stamp,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @if ($list->sum('bl')>0)
                        <tr>
                            <td></td>
                            <td colspan="2">BL</td>
                            <td class="text-end">{{ number_format($list->sum('bl'),2,',','.') }}</td>
                        </tr>
                    @endif
                    @foreach ($list->where('ut_cleaning','!=',0)->groupBy('ut_cleaning') as $item)
                        @if ($item->count()>1)
                            <tr>
                                <td></td>
                                <td colspan="2">Cleaning</td>
                                <td class="text-end">{{ number_format($item->first()->ut_cleaning * $item->count(),2,',','.') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td colspan="2">Cleaning</td>
                                <td class="text-end">{{ number_format($item->first()->ut_cleaning,2,',','.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr style="height:12px">
                        <td></td>
                        <td colspan="2"></td>
                        <td></td>
                    </tr>
                @endforeach
                @if (!is_null($hp->penambahan) && $hp->penambahan_nominal != 0)
                <tr>
                    <td></td>
                    <td colspan="2">{{ $hp->penambahan }}</td>
                    <td class="text-end {{ $hp->penambahan_nominal < 0 ? 'text-danger' : '' }}">{{ number_format($hp->penambahan_nominal,2,',','.') }}</td>
                </tr>
                @endif
                {{-- @foreach ($opp as $list)
                    @foreach ($list->groupBy('order.job') as $item)
                        <tr>
                            <td></td>
                            <td colspan="2">OPP(1X{{ preg_replace("/[^0-9]/", "", $item->first()->order->tarif->shipmentInfo->nama ) }}) {{ $item->first()->order->tarif->customer->nama }} ({{ $item->first()->order->job }}-{{ sprintf('%02d',$item->first()->order->no_job) }})</td>
                            <td class="text-end">{{ number_format($item->first()->opp,2,',','.') }}</td>
                        </tr>
                    @endforeach
                @endforeach --}}
                <tr style="border: 2px solid red">
                    @if (!is_null($hp->penambahan) && $hp->penambahan_nominal != 0)
                    <td style="color:red" colspan="2">Ch/ BG. No :</td>
                    <td colspan="1" class="fw-bold">{{ $hp->no_bg_ut }} ({{ date('d-m-Y',strtotime($hp->tgl_bg_ut)) }})</td>
                    @else
                    <td style="color:red" colspan="{{ $b }}">Ch/ BG. No :</td>
                    <td colspan="{{ $b }}" class="fw-bold">{{ $hp->no_bg_ut }} ({{ date('d-m-Y',strtotime($hp->tgl_bg_ut)) }})</td>
                    @endif
                    <td class="text-end fw-bold">{{ number_format($hp->nominal_bg_ut,2,',','.') }}</td>
                </tr>
            </tbody>
        </table>
        <table class="w-100 table" style="position: relative; top:-10px">
            <thead>
                <tr>
                    <td class="text-start" style="color: red">TERBILANG :</td>
                    <td colspan="7" style="text-transform: uppercase; font-weight:bold">{{ terbilang($hp->nominal_bg_ut) }} RUPIAH</td>
                </tr>
                <tr>
                    <td class="text-start" colspan="2"><u style="color: red">CATATAN :</u></td>
                    <td class="bg-red">Pembukuan</td>
                    <td class="bg-red">Mengetahui</td>
                    <td class="bg-red" colspan="2" style="width: 100px">Kasir</td>
                    <td class="bg-red">Penerima</td>
                </tr>
            </thead>
            <tbody>
                <tr style="border: none !important; height:25px">
                    <td colspan="2"></td>
                    <td></td>
                    <td></td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        @endif
    </div>
@endsection

@section('script')
    <script>
        window.print();
    </script>
@endsection
