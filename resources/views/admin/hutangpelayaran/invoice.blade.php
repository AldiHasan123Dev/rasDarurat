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
            border: 1px solid black;
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
    <div class="container">
        <div class="card p-3 mt-3">
            <form action="{{ route('hutang-pelayaran.store') }}" method="POST" id="form-id">
                @csrf
                <input type="hidden" name="ids" value="{{ $ids }}">
                <div class="invoice-box first-page">
                    <div class="header d-flex" style="gap:5px; width:100%">
                        <div style="width: 100%;">
                            <table style="font-size:1.2rem; width: 100%;">
                                <tr>
                                    <td class="fw-bold" style="text-align: center">BUKTI BANK KELUAR</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">OPP</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">OPT</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">UT</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-verify-tab" data-bs-toggle="pill" data-bs-target="#pills-verify" type="button" role="tab" aria-controls="pills-verify" aria-selected="false">VERIFIKASI</button>
                                </li>
                            </ul>
                            <div class="card p-3 shadow-xl">
                                <div class="d-flex justify-content-between">
                                    <div class="my-2">
                                        <label for="kolektif">
                                            <input type="checkbox" name="kolektif" id="kolektif" value="1" checked> Kolektif Input
                                        </label>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Input BL
                                    </button>
                                </div>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                        <table class="mt-2 table" style="font-size: .7rem; width:100%; border:1px solid black;">
                                            <thead>
                                                <tr class="heading table-primary" style="height: 35px">
                                                    <td colspan="3" class="text-center fw-bold text-uppercase">{{ $pelayaran->nama }}</td>
                                                </tr>
                                                <tr class="heading table-warning">
                                                    <td class="fw-bold text-uppercase" style="width: 100px">ID JOB</td>
                                                    <td class="fw-bold text-uppercase" style="width: 50%">Uraian</td>
                                                    <td class="fw-bold text-uppercase" style="width: 50%">Jumlah</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $job)
                                                    @foreach ($job as $item)
                                                        <tr>
                                                            <td rowspan="8" class="vertical">{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                                            <td>OPP (1X{{ preg_replace("/[^0-9]/", "", $item->order->tarif->shipmentInfo->nama ) }}) {{ $item->order->tarif->customer->nama }} ({{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }})</td>
                                                            <td><input type="number" onkeyup="hitung('opp',this.value)" onclick="this.select()" value="{{ $item->opp ?? 0 }}"  class="opp-opp" name="data[{{ $item->id }}][opp]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>THC LoLo SBY</td>
                                                            <td><input type="number" onkeyup="hitung('thc',this.value)" onclick="this.select()" value="{{ $item->thc }}"  class="opp-thc" name="data[{{ $item->id }}][thc]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>APBS</td>
                                                            <td><input type="number" onkeyup="hitung('apbs',this.value)" onclick="this.select()" value="{{ $item->apbs }}"  class="opp-apbs" name="data[{{ $item->id }}][apbs]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Cleaning</td>
                                                            <td><input type="number" onkeyup="hitung('cleaning',this.value)" onclick="this.select()" value="{{ $item->cleaning }}"  class="opp-cleaning" name="data[{{ $item->id }}][cleaning]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Stamp</td>
                                                            <td><input type="number" onkeyup="hitung('opp_stamp',this.value)" onclick="this.select()" value="{{ $item->opp_stamp }}"  class="opp-stamp" name="data[{{ $item->id }}][opp_stamp]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                         <tr>
                                                            <td>Seal</td>
                                                            <td><input type="number" onkeyup="hitung('hp_seal',this.value)" onclick="this.select()" value="{{ $item->hp_seal }}"  class="opp-hp_seal" name="data[{{ $item->id }}][hp_seal]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>LSS  (1X{{ preg_replace("/[^0-9]/", "", $item->order->tarif->shipmentInfo->nama ) }}) {{ $item->order->tarif->customer->nama }} ({{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }})</td>
                                                            <td><input type="number" onkeyup="hitung('lss',this.value)" onclick="this.select()" value="{{ $item->lss }}"  class="opp-lss" name="data[{{ $item->id }}][lss]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                         <tr>
                                                            <td>VGM</td>
                                                            <td><input type="number" onkeyup="hitung('vgm',this.value)" onclick="this.select()" value="{{ $item->vgm }}"  class="opp-vgm" name="data[{{ $item->id }}][vgm]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" class="text-end">PPH 2%</td>
                                                    <td><input type="number" onkeyup="hitung()" onclick="this.select()" id="pph" name="pph" value="{{ $hp->pph }}" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end">Pembulatan</td>
                                                    <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="{{ $hp->pembulatan }}" name="pembulatan" id="pembulatan" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NOMINAL BG</td>
                                                    <td><input type="text" class="nominal_bg_opp" name="nominal_bg_opp" id="nominal_bg_opp" value="{{ $hp->nominal_bg_opp }}" readonly style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NO. BG</td>
                                                    <td><input type="text" id="no_bg_opp" name="no_bg_opp" value="{{ $hp->no_bg_opp }}" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">TANGGAL BG</td>
                                                    <td><input type="date" id="tgl_bg_opp" name="tanggal_bg_opp" value="{{ $hp->tgl_bg_opp  }}" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                        <table class="mt-2 table" style="font-size: .7rem; width:100%; border:1px solid black;">
                                            <thead>
                                                <tr class="heading table-primary" style="height: 35px">
                                                    <td colspan="3" class="text-center fw-bold text-uppercase">{{ $pelayaran->nama }}</td>
                                                </tr>
                                                <tr class="heading table-warning">
                                                    <td class="fw-bold text-uppercase" style="width: 100px">ID JOB</td>
                                                    <td class="fw-bold text-uppercase" style="width: 50%">Uraian</td>
                                                    <td class="fw-bold text-uppercase" style="width: 50%">Jumlah</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $job)
                                                    @foreach ($job as $item)
                                                        <tr>
                                                            <td rowspan="4" class="vertical">{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                                            <td>OPT (1X{{ preg_replace("/[^0-9]/", "", $item->order->tarif->shipmentInfo->nama ) }}) {{ $item->order->tarif->customer->nama }} ({{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }})</td>
                                                            <td><input type="number" onkeyup="hitungOpt('opt',this.value)" onclick="this.select()" value="{{ $item->opt }}"  class="opt-opt" name="data[{{ $item->id }}][opt]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>STAMP</td>
                                                            <td><input type="number" step="any" onkeyup="hitungOpt('stamp',this.value)" onclick="this.select()" value="{{ $item->opt_stamp }}"  class="opt-stamp" name="data[{{ $item->id }}][opt_stamp]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>-</td>
                                                            <td>-</td>
                                                        </tr>
                                                        <tr>
                                                            <td>-</td>
                                                            <td>-</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NOMINAL BG</td>
                                                    <td><input type="text" class="nominal_bg_opt" name="nominal_bg_opt" id="nominal_bg_opt" value="{{ $hp->nominal_bg_opt }}" readonly style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                 <tr>
                                                    <td colspan="2" class="text-end fw-bold">OPT PPH 2%</td>
                                                    <td><input type="number" onkeyup="hitungOpt()" onclick="this.select()" id="opt_pph" name="opt_pph" value="{{ $hp->opt_pph }}" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NO. BG</td>
                                                    <td><input type="text" id="no_bg_opt" name="no_bg_opt" value="{{ $hp->no_bg_opt }}" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">TANGGAL BG</td>
                                                    <td><input type="date" id="tgl_bg_opt" name="tanggal_bg_opt" value="{{ $hp->tgl_bg_opt }}" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                                        <table class="mt-2 table" style="font-size: .7rem; width:100%; border:1px solid black;">
                                            <thead>
                                                <tr class="heading table-primary" style="height: 35px">
                                                    <td colspan="3" class="text-center fw-bold text-uppercase">{{ $pelayaran->nama }}</td>
                                                </tr>
                                                <tr class="heading table-warning">
                                                    <td class="fw-bold text-uppercase" style="width: 100px">ID JOB</td>
                                                    <td class="fw-bold text-uppercase" style="width: 50%">Uraian</td>
                                                    <td class="fw-bold text-uppercase" style="width: 50%">Jumlah</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $job)
                                                    @foreach ($job as $item)
                                                        <tr>
                                                            <td rowspan="4" class="vertical">{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                                            <td>Uang Tambang</td>
                                                            <td><input type="number" onkeyup="hitungUT('ut',this.value)" readonly onclick="this.select()" value="{{ $item->ut }}"  class="ut-ut" name="data[{{ $item->id }}][ut]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>BL</td>
                                                            <td><input type="number" onkeyup="hitungUT('bl',this.value)" onclick="this.select()" value="{{ $item->bl }}"  class="ut-bl {{ $item->order->job }}" name="data[{{ $item->id }}][bl]" readonly style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>STAMP</td>
                                                            <td><input type="number" onkeyup="hitungUT('stamp',this.value)" onclick="this.select()" value="{{ $item->ut_stamp }}"  class="ut-stamp" name="data[{{ $item->id }}][ut_stamp]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>CLEANING</td>
                                                            <td><input type="number" onkeyup="hitungUT('ut_cleaning',this.value)" onclick="this.select()" value="{{ $item->ut_cleaning }}"  class="ut-cleaning" name="data[{{ $item->id }}][ut_cleaning]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>-</td>
                                                            <td>-</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold"><input type="text" name="penambahan" id="penambahan" value="{{ $hp->penambahan }}" onclick="this.select()" style="width: 100%"></td>
                                                    <td><input type="number" name="penambahan_nominal" class="penambahan_nominal" onkeyup="hitungUT('penambahan',this.value)" onclick="this.select()" value="{{ $hp->penambahan_nominal }}" id="penambahan_nominal" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                {{-- <tr>
                                                    <td colspan="2" class="text-end fw-bold"><input type="text" name="pengurangan" id="pengurangan" value="PENGURANGAN" onclick="this.select()" style="width: 100%"></td>
                                                    <td><input type="number" name="pengurangan_nominal" class="pengurangan_nominal" onkeyup="hitungUT('pengurangan',this.value)" onclick="this.select()" value="0" id="pengurangan_nominal" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr> --}}
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NOMINAL BG</td>
                                                    <td><input type="text" name="nominal_bg_ut" class="nominal_bg_ut" value="{{ $hp->nominal_bg_ut }}" id="nominal_bg_ut" readonly style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NO. BG</td>
                                                    <td><input type="text" name="no_bg_ut" id="no_bg_ut" value="{{ $hp->no_bg_ut }}" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">TANGGAL BG</td>
                                                    <td><input type="date" name="tanggal_bg_ut" id="tgl_bg_ut" value="{{ $hp->tgl_bg_ut }}" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="pills-verify" role="tabpanel" aria-labelledby="pills-verify-tab">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <td>Keterangan</td>
                                                    <td>Nominal BG</td>
                                                    <td>No. BG</td>
                                                    <td>TANGGAL BG</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>OPP</td>
                                                    <td class="nominal_bg_opp">{{ number_format($hp->nominal_bg_opp,2,',','.') ?? '' }}</td>
                                                    <td class="no_bg_opp">{{ $hp->no_bg_opp ?? '' }}</td>
                                                    <td class="tgl_bg_opp">{{ $hp->tgl_bg_opp ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>OPT</td>
                                                    <td class="nominal_bg_opt">{{ number_format($hp->nominal_bg_opt,2,',','.') ?? '' }}</td>
                                                    <td class="no_bg_opt">{{ $hp->no_bg_opt ?? '' }}</td>
                                                    <td class="tgl_bg_opt">{{ $hp->tgl_bg_opt ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>UT</td>
                                                    <td class="nominal_bg_ut">{{ number_format($hp->nominal_bg_ut,2,',','.') ?? '' }}</td>
                                                    <td class="no_bg_ut">{{ $hp->no_bg_ut ?? '' }}</td>
                                                    <td class="tgl_bg_ut">{{ $hp->tgl_bg_ut ?? '' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table mt-3">
                                            <thead>
                                                <tr>
                                                    <td style="width: 300px">PENERIMA BL</td>
                                                    <td>TOTAL OPP</td>
                                                    <td>TOTAL OPT</td>
                                                    <td>TOTAL UT</td>
                                                    <td>ID JOB</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data_bl as $job)
                                                    @foreach ($job as $item)
                                                    <tr>
                                                        @php
                                                            $t_opp = $job->sum('opp') + $job->sum('apbs') + $job->sum('lss') + $job->sum('cleaning') + $job->sum('thc') + $job->sum('opp_stamp') + $job->sum('hp_seal');
                                                            $t_opt = $job->sum('opt') + $job->sum('opt_stamp');
                                                            $t_ut = $job->sum('ut') + $job->sum('bl') + $job->sum('ut_stamp') + $job->sum('ut_cleaning');
                                                        @endphp
                                                        @if ($loop->first)
                                                            <td rowspan="{{ $job->count() }}">{{ $job->first()->order->penerimabl ?? '-' }}</td>
                                                            <td class="text-center" rowspan="{{ $job->count() }}"><b>{{ number_format($t_opp,2,',','.') }}</b></td>
                                                            <td class="text-center" rowspan="{{ $job->count() }}"><b>{{ number_format($t_opt,2,',','.') }}</b></td>
                                                            <td class="text-center" rowspan="{{ $job->count() }}"><b>{{ number_format($t_ut,2,',','.') }}</b></td>
                                                        @endif
                                                        <td>{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                                    </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <button type="submit" class="btn btn-success mt-3 w-100" onclick="return confirm('are you sure?')">Cetak BBK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="row mt-3">
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
                            </table>
                        </div>
                        <div class="col-3">
                            <div class="text-center" style="font-size: .7rem">
                                <p>Surabaya,
                                    {{ is_null($order->tgl_invoice) ? '-' : tanggal($order->tgl_invoice) }}
                                </p>
                                <div style="height: 1.5cm"></div>
                                (<input type="text" value="Totok" class="text-center"
                                    style="border:none; width:130px" />)
                            </div>
                        </div>
                    </div> --}}
                </div>
            </form>
        </div>
    </div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Input BL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm w-100">
                    <thead>
                        <tr>
                            <td>Group JOB</td>
                            <td class="text-center">CONT</td>
                            <td>TOTAL BL</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $idx => $item)
                        <tr>
                            <td>{{ $idx }}</td>
                            <td class="text-center">{{ $item->count() }}</td>
                            <td><input type="number" value="0"  onclick="this.select()" onkeyup="hitungBL('{{ $idx }}',{{ $item->count() }},this.value)"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/jquery-serializeFields.js') }}"></script>
<script>

function hitung(tipe, val) {
    if ($('#kolektif').is(':checked')) {
        if (tipe == 'opp') {
            $('input[type="number"].opp-opp').each(function () {
                $(this).val(val);
            });
        }
        if (tipe == 'hp_seal') {
            $('input[type="number"].opp-hp_seal').each(function () {
                $(this).val(val);
            });
        }
        if (tipe == 'vgm') {
            $('input[type="number"].opp-vgm').each(function () {
                $(this).val(val);
            });
        }
        if (tipe == 'lss') {
            $('input[type="number"].opp-lss').each(function () {
                $(this).val(val);
            });
        }
        if (tipe == 'thc') {
            $('input[type="number"].opp-thc').each(function () {
                $(this).val(val);
            });
        }
        if (tipe == 'apbs') {
            $('input[type="number"].opp-apbs').each(function () {
                $(this).val(val);
            });
        }
        if (tipe == 'cleaning') {
            $('input[type="number"].opp-cleaning').each(function () {
                $(this).val(val);
            });
        }
        if (tipe == 'opp_stamp') {
            $('input[type="number"].opp-stamp').each(function () {
                $(this).val(val);
            });
        }
    }

    let opp = 0;
    let lss = 0;
    let hp_seal = 0;
    let thc = 0;
    let apbs = 0;
    let cleaning = 0;
    let stamp = 0;
    let jumlah = 0;
    let vgm = 0;

    let pph = parseFloat($('#pph').val()) || 0;
    let pembulatan = parseFloat($('#pembulatan').val()) || 0;

    $('input[type="number"].opp-opp').each(function () {
        opp += parseFloat($(this).val()) || 0;
    });
    $('input[type="number"].opp-vgm').each(function () {
        vgm += parseFloat($(this).val()) || 0;
    });
    $('input[type="number"].opp-lss').each(function () {
        lss += parseFloat($(this).val()) || 0;
    });
    $('input[type="number"].opp-thc').each(function () {
        thc += parseFloat($(this).val()) || 0;
    });
    $('input[type="number"].opp-apbs').each(function () {
        apbs += parseFloat($(this).val()) || 0;
    });
    $('input[type="number"].opp-cleaning').each(function () {
        cleaning += parseFloat($(this).val()) || 0;
    });
    $('input[type="number"].opp-stamp').each(function () {
        stamp += parseFloat($(this).val()) || 0;
    });
    $('input[type="number"].opp-hp_seal').each(function () {
        hp_seal += parseFloat($(this).val()) || 0;
    });

    jumlah = (opp + lss + thc + apbs + cleaning + stamp + pembulatan + hp_seal + vgm) - pph;

    $('.nominal_bg_opp').val(jumlah);
    $('.nominal_bg_opp').text(jumlah.toLocaleString('en-US'));
}


    function hitungOpt(tipe,val){
        if ($('#kolektif').is(':checked')) {
            if(tipe=='opt'){
                if(val.length>0){
                    $('input[type="number"].opt-opt').each(function () {
                    $(this).val(parseFloat(val));
                });
                }
            }
            if(tipe=='stamp'){
                if(val.length>0){
                    $('input[type="number"].opt-stamp').each(function () {
                        $(this).val(parseFloat(val));
                    });
                }
            }
        }
        let opt = 0;
        let stamp = 0;
        let opt_pph = parseFloat($('#opt_pph').val()) || 0;
        $('input[type="number"].opt-opt').each(function () {
            opt+=parseFloat($(this).val());
        });
        $('input[type="number"].opt-stamp').each(function () {
            stamp+=parseFloat($(this).val());
        });

        jumlah = (opt + stamp) - opt_pph;
        $('.nominal_bg_opt').val(jumlah);
        $('.nominal_bg_opt').text(jumlah.toLocaleString('en-US'));
    }

    function hitungUT(tipe,val){
        if($('#kolektif').is(':checked')){
            if(tipe=='ut'){
                $('input[type="number"].ut-ut').each(function () {
                    $(this).val(val);
                });
            }
            if(tipe=='bl'){
                $('input[type="number"].ut-bl').each(function () {
                    $(this).val(val);
                });
            }
            if(tipe=='stamp'){
                $('input[type="number"].ut-stamp').each(function () {
                    $(this).val(val);
                });
            }
            if(tipe=='ut_cleaning'){
                $('input[type="number"].ut-cleaning').each(function () {
                    $(this).val(val);
                });
            }
        }
        let ut = 0;
        let bl = 0;
        let stamp = 0;
        let ut_cleaning = 0;
        let penambahan = parseFloat($('#penambahan_nominal').val());
        // let pengurangan = parseFloat($('#pengurangan_nominal').val());
        $('input[type="number"].ut-ut').each(function () {
            ut+=parseFloat($(this).val());
        });
        $('input[type="number"].ut-stamp').each(function () {
            stamp+=parseFloat($(this).val());
        });
        $('input[type="number"].ut-cleaning').each(function () {
            ut_cleaning+=parseFloat($(this).val());
        });
        $('input[type="number"].ut-bl').each(function () {
            bl+=parseFloat($(this).val());
        });

        jumlah = ut + stamp + bl + penambahan + ut_cleaning;
        $('.nominal_bg_ut').val(jumlah);
        $('.nominal_bg_ut').text(jumlah.toLocaleString('en-US'));
    }

    $('#no_bg_opp').keyup(function (e) {
        let val = $(this).val();
        $('.no_bg_opp').text(val);
    });
    $('#no_bg_opt').keyup(function (e) {
        let val = $(this).val();
        $('.no_bg_opt').text(val);
    });
    $('#no_bg_ut').keyup(function (e) {
        let val = $(this).val();
        $('.no_bg_ut').text(val);
    });
    $('#tgl_bg_opp').change(function (e) {
        let date = new Date($(this).val());
        let d = date.getDate();
        let m = date.getMonth() + 1;
        let y = date.getFullYear();
        $('.tgl_bg_opp').text(String(d).padStart(2, '0')+'/'+String(m).padStart(2, '0')+'/'+y);
    });
    $('#tgl_bg_opt').change(function (e) {
        let date = new Date($(this).val());
        let d = date.getDate();
        let m = date.getMonth() + 1;
        let y = date.getFullYear();
        $('.tgl_bg_opt').text(String(d).padStart(2, '0')+'/'+String(m).padStart(2, '0')+'/'+y);
    });
    $('#tgl_bg_ut').change(function (e) {
        let date = new Date($(this).val());
        let d = date.getDate();
        let m = date.getMonth() + 1;
        let y = date.getFullYear();
        $('.tgl_bg_ut').text(String(d).padStart(2, '0')+'/'+String(m).padStart(2, '0')+'/'+y);
    });

    function hitungBL(job,count,val){
        let price = Math.floor(val / count);
        let selisih = val - (price * count)
        let data = Array(count);
        let last = count;
        for (let i = 0; i < count; i++) {
            if(i==(last-1)){
                data[i] = price + selisih
            }else{
                data[i] = price;
            }
        }

        $('input[type="number"].'+job).each(function (index) {
            $(this).val(data[index]);
        });

        hitungUT('as',0);
    }

    function save(){
        let data = $( '#form-id' ).serializeFields();
        $.ajax({
            type: "POST",
            url: "{{ route('api.hutang-pelayaran.updateId') }}",
            data: {
                data:data
            },
            success: function (response) {
                console.log(response);
            }
        });
    }

    setInterval(() => {
        save();
    }, 10000);
</script>
@endsection
