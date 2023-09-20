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
            <form action="{{ route('hutang-pelayaran.store') }}" method="POST" id="print">
                @csrf
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
                                                            <td rowspan="5" class="vertical">{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                                            <td>OPP (1X{{ preg_replace("/[^0-9]/", "", $item->order->tarif->shipmentInfo->nama ) }}) {{ $item->order->tarif->customer->nama }} ({{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }})</td>
                                                            <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="opp-opp" name="data[{{ $item->id }}][opp]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>THC LoLo SBY</td>
                                                            <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="opp-thc" name="data[{{ $item->id }}][thc]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>APBS</td>
                                                            <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="opp-apbs" name="data[{{ $item->id }}][apbs]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Cleaning</td>
                                                            <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="opp-cleaning" name="data[{{ $item->id }}][cleaning]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>LSS  (1X{{ preg_replace("/[^0-9]/", "", $item->order->tarif->shipmentInfo->nama ) }}) {{ $item->order->tarif->customer->nama }} ({{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }})</td>
                                                            <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="opp-lss" name="data[{{ $item->id }}][lss]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" class="text-end">PPH 2%</td>
                                                    <td><input type="number" onkeyup="hitung()" onclick="this.select()" id="pph" name="pph" value="0" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end">Pembulatan</td>
                                                    <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" name="pembulatan" id="pembulatan" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NOMINAL BG</td>
                                                    <td><input type="text" class="nominal_bg_opp" name="nominal_bg" id="nominal_bg" readonly style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NO. BG</td>
                                                    <td><input type="text" class="no_bg_opp" name="no_bg" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">TANGGAL BG</td>
                                                    <td><input type="date" class="tgl_bg_opp" name="tanggal_bg" style="width: 100%; padding:5px; border:1px solid gray"></td>
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
                                                            <td><input type="number" onkeyup="hitungOpt()" onclick="this.select()" value="0" min="0" class="opt-opt" name="data[{{ $item->id }}][opp]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>STAMP</td>
                                                            <td><input type="number" onkeyup="hitungOpt()" onclick="this.select()" value="0" min="0" class="opt-stamp" name="data[{{ $item->id }}][thc]" style="width: 100%; padding:5px; border:1px solid gray"></td>
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
                                                    <td><input type="text" class="nominal_bg_opt" name="nominal_bg" id="nominal_bg_opt" readonly style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NO. BG</td>
                                                    <td><input type="text" class="no_bg_opt" name="no_bg" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">TANGGAL BG</td>
                                                    <td><input type="date" class="tgl_bg_opt" name="tanggal_bg" style="width: 100%; padding:5px; border:1px solid gray"></td>
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
                                                            <td><input type="number" onkeyup="hitungUT()" onclick="this.select()" value="0" min="0" class="ut-ut" name="data[{{ $item->id }}][opp]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>BL</td>
                                                            <td><input type="number" onkeyup="hitungUT()" onclick="this.select()" value="0" min="0" class="ut-bl" name="data[{{ $item->id }}][thc]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>STAMP</td>
                                                            <td><input type="number" onkeyup="hitungUT()" onclick="this.select()" value="0" min="0" class="ut-stamp" name="data[{{ $item->id }}][thc]" style="width: 100%; padding:5px; border:1px solid gray"></td>
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
                                                    <td><input type="text" name="nominal_bg" class="nominal_bg_ut" value="0" id="nominal_bg_ut" readonly style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">NO. BG</td>
                                                    <td><input type="text" name="no_bg" class="no_bg_ut" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-end fw-bold">TANGGAL BG</td>
                                                    <td><input type="date" name="tanggal_bg" class="tgl_bg_ut" style="width: 100%; padding:5px; border:1px solid gray"></td>
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
                                                    <td class="nominal_bg_opp"></td>
                                                    <td class="no_bg_opp"></td>
                                                    <td class="tgl_bg_opp"></td>
                                                </tr>
                                                <tr>
                                                    <td>OPT</td>
                                                    <td class="nominal_bg_opt"></td>
                                                    <td class="no_bg_opt"></td>
                                                    <td class="tgl_bg_opt"></td>
                                                </tr>
                                                <tr>
                                                    <td>UT</td>
                                                    <td class="nominal_bg_ut"></td>
                                                    <td class="no_bg_ut"></td>
                                                    <td class="tgl_bg_ut"></td>
                                                </tr>
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
@endsection

@section('script')
<script>

    function hitung(){
        let opp = 0;
        let lss = 0;
        let thc = 0;
        let apbs = 0;
        let cleaning = 0;
        let jumlah = 0;
        let pph = parseFloat($('#pph').val());
        let pembulatan = parseFloat($('#pembulatan').val());
        $('input[type="number"].opp-opp').each(function () {
            opp+=parseFloat($(this).val());
        });
        $('input[type="number"].opp-lss').each(function () {
            lss+=parseFloat($(this).val());
        });
        $('input[type="number"].opp-thc').each(function () {
            thc+=parseFloat($(this).val());
        });
        $('input[type="number"].opp-apbs').each(function () {
            apbs+=parseFloat($(this).val());
        });
        $('input[type="number"].opp-cleaning').each(function () {
            cleaning+=parseFloat($(this).val());
        });

        jumlah = (opp + lss + thc + apbs + cleaning+ pembulatan) - pph;
        $('.nominal_bg_opp').val(jumlah);
        $('.nominal_bg_opp').text(jumlah.toLocaleString('en-US'));
    }

    function hitungOpt(){
        let opt = 0;
        let stamp = 0;
        $('input[type="number"].opt-opt').each(function () {
            opt+=parseFloat($(this).val());
        });
        $('input[type="number"].opt-stamp').each(function () {
            stamp+=parseFloat($(this).val());
        });

        jumlah = opt + stamp;
        $('.nominal_bg_opt').val(jumlah);
        $('.nominal_bg_opt').text(jumlah.toLocaleString('en-US'));
    }

    function hitungUT(){
        let ut = 0;
        let bl = 0;
        let stamp = 0;
        $('input[type="number"].ut-ut').each(function () {
            ut+=parseFloat($(this).val());
        });
        $('input[type="number"].ut-stamp').each(function () {
            stamp+=parseFloat($(this).val());
        });
        $('input[type="number"].ut-bl').each(function () {
            bl+=parseFloat($(this).val());
        });

        jumlah = ut + stamp + bl;
        $('.nominal_bg_ut').val(jumlah);
        $('.nominal_bg_ut').text(jumlah.toLocaleString('en-US'));
    }
</script>
@endsection
