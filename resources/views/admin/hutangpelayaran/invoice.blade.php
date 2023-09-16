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
                    <div class="row mt-3">
                        <div class="col-8">
                            <table style="font-size: .8rem">
                                <tr>
                                    <td style="width: 170px">DIBAYARKAN KEPADA</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ $pelayaran->tarif_pelayaran->pelayaran->nama }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table style="font-size: .8rem">
                                <tr>
                                    <td style="width: 120px">NAMA</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ Auth::user()->name }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 120px">TANGGAL</td>
                                    <td style=" width:5px">:</td>
                                    <td>{{ date('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <table class="mt-2 table" style="font-size: .7rem; width:100%; border:1px solid black;">
                                <thead>
                                    <tr class="heading table-primary" style="height: 25px">
                                        <td colspan="3" class="text-center fw-bold text-uppercase">{{ $pelayaran->tarif_pelayaran->pelayaran->nama }}</td>
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
                                                <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="opp" name="data[{{ $item->id }}][opp]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                            </tr>
                                            <tr>
                                                <td>THC LoLo SBY</td>
                                                <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="thc" name="data[{{ $item->id }}][thc]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                            </tr>
                                            <tr>
                                                <td>APBS</td>
                                                <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="apbs" name="data[{{ $item->id }}][apbs]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                            </tr>
                                            <tr>
                                                <td>Cleaning</td>
                                                <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="cleaning" name="data[{{ $item->id }}][cleaning]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                            </tr>
                                            <tr>
                                                <td>LSS  (1X{{ preg_replace("/[^0-9]/", "", $item->order->tarif->shipmentInfo->nama ) }}) {{ $item->order->tarif->customer->nama }} ({{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }})</td>
                                                <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" class="lss" name="data[{{ $item->id }}][lss]" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end">PPH {{ $pelayaran->tarif_pelayaran->pelayaran->pph }}%</td>
                                        <td><input type="number" onkeyup="hitung()" readonly id="pph" name="pph" value="{{ $pelayaran->tarif_pelayaran->pelayaran->pph==0?0:'' }}" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end">Pembulatan</td>
                                        <td><input type="number" onkeyup="hitung()" onclick="this.select()" value="0" min="0" name="pembulatan" id="pembulatan" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">NOMINAL BG</td>
                                        <td><input type="text" name="nominal_bg" id="nominal_bg" readonly style="width: 100%; padding:5px; border:1px solid gray"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">NO. BG</td>
                                        <td><input type="text" name="no_bg" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">TANGGAL BG</td>
                                        <td><input type="date" name="tanggal_bg" style="width: 100%; padding:5px; border:1px solid gray"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary w-100" onclick="return confirm('are you sure?')">Cetak BBK</button>
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
    let pph = @json($pelayaran->tarif_pelayaran->pelayaran->pph);
    $('.opp').keyup(function (e) {
        let jumlah = 0;
        $('input[type="number"].opp').each(function () {
            jumlah+=parseFloat($(this).val());
        });
        let a = jumlah / 1.11;
        let total = parseInt(pph) / 100;
        total *= a;
        $('#pph').val(Math.round(total));
        hitung();
    });

    let timer
    function hitung(){
        clearTimeout(timer)
        setTimeout(() => {
            let opp = 0;
            let lss = 0;
            let thc = 0;
            let apbs = 0;
            let cleaning = 0;
            let jumlah = 0;
            let pph = parseFloat($('#pph').val());
            let pembulatan = parseFloat($('#pembulatan').val());
            $('input[type="number"].opp').each(function () {
                opp+=parseFloat($(this).val());
            });
            $('input[type="number"].lss').each(function () {
                lss+=parseFloat($(this).val());
            });
            $('input[type="number"].thc').each(function () {
                thc+=parseFloat($(this).val());
            });
            $('input[type="number"].apbs').each(function () {
                apbs+=parseFloat($(this).val());
            });
            $('input[type="number"].cleaning').each(function () {
                cleaning+=parseFloat($(this).val());
            });

            jumlah = (opp + lss + thc + apbs + cleaning+ pembulatan) - pph;
            $('#nominal_bg').val(jumlah);

        }, 1000);
    }
</script>
@endsection
