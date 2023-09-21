@extends('layouts.admin')
@section('style')
    <style>
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');

            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
                -webkit-print-color-adjust: exact;
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
                position: absolute;
                left: 0px;
                top: 0px;
                padding: 0px;
            }

            #table td,
            #table th {
                border: 1px solid rgb(255, 0, 0);
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
                color: #fff !important;
            }
        }

        #print{
            padding: 10px;
            background-color: #fff;
        }

        .bg-red{
            background-color: red !important;
            color: #fff !important;
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
    <div id="print">
        <table class="w-100 table">
            <thead>
                <tr>
                    <th style="width: 100px; color:red" colspan="3">Dibayar Kepada</th>
                    <th rowspan="2" style="vertical-align: middle; color:red">BUKTI BANK BAYAR</th>
                    <th style="color: red">Nomor</th>
                    <th style="width: 250px"></th>
                </tr>
                <tr>
                    <th colspan="3">TANTO</th>
                    <th style="color: red">Tanggal</th>
                    <th style="width: 250px"></th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: red" class="bg-red text-white">
                    <td style="background: white" rowspan="11"></td>
                    <td rowspan="11" style="background-color:white;transform: rotate(180deg);white-space: nowrap; writing-mode: vertical-rl; ms-writing-mode: tb-rl; -webkit-writing-mode: vertical-rl; color:red">KEPERLUAN INTERN</td>
                    <td class="text-center">PERKIRAAN</td>
                    <td class="text-center" colspan="2">URAIAN</td>
                    <td class="text-center">JUMLAH</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center" colspan="2">TANTO RAYA VOY. 235</td>
                    <td></td>
                </tr>
                @for ($i = 0; $i < 8; $i++)
                <tr>
                    <td></td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
                @endfor
                <tr style="border: 2px solid red">
                    <td style="color:red">Ch/ BG. No :</td>
                    <td colspan="2">WL AJODAJ 120120</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
