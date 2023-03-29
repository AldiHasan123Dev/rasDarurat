@extends('layouts.admin')
@section('style')
<style>
    @media print{
        @page {
            size: landscape
        }
        body * {
            visibility: hidden;
        }
        body{
            width: 100%;
        }
        #print, #print * {
            visibility: visible;
            font-family: 'Open Sans', sans-serif;
            font-size: .7rem !important;
            color: black !important;
        }
        #print{
            position: absolute;
            top: -80px;
        }
        tr th, tr{
            border: 1px solid black;
        }
}
</style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <div>
                        <button type="button" class="btn btn-sm btn-success" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                    </div>
                    <div id="print">
                        <table class="table table-sm table-bordered mt-3" style="font-size: .7rem">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th class="text-center" colspan="2">Januari</th>
                                    <th class="text-center" colspan="2">Februari</th>
                                    <th class="text-center" colspan="2">Maret</th>
                                    <th class="text-center" colspan="2">April</th>
                                    <th class="text-center" colspan="2">Mei</th>
                                    <th class="text-center" colspan="2">Juni</th>
                                    <th class="text-center" colspan="2">July</th>
                                    <th class="text-center" colspan="2">Agustus</th>
                                    <th class="text-center" colspan="2">September</th>
                                    <th class="text-center" colspan="2">Oktober</th>
                                    <th class="text-center" colspan="2">November</th>
                                    <th class="text-center" colspan="2">Desember</th>
                                    <th class="text-center" colspan="3">Total</th>
                                </tr>
                                <tr>
                                    <th>Tujuan</th>
                                    @for ($i = 1; $i <=26; $i++)
                                    <th class="text-center">{{ $i%2==0?'20':40 }}</th>
                                    @endfor
                                    <th class="text-center">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sub = array();
                                    $total = 0;
                                @endphp
                                @foreach ($data as $idx => $item)
                                    <tr>
                                        <td>{{ $item->nama }}</td>
                                        @php
                                            $month = 1;
                                            $fit20 = 0;
                                            $fit40 = 0;
                                        @endphp
                                        @for ($i = 1; $i <=24; $i++)
                                            @if ($i%2==0)
                                                <th class="text-center">{{ $item->laporan20Fit($month) }}</th>
                                                @php
                                                    $fit20 += $item->laporan20Fit($month);
                                                    $sub[$i] = ($sub[$i]??0) + $item->laporan20Fit($month);
                                                    $month++;
                                                @endphp
                                            @else
                                                <th class="text-center">{{ $item->laporan40Fit($month) }}</th>
                                                @php
                                                    $fit40 += $item->laporan40Fit($month);
                                                    $sub[$i] = ($sub[$i]??0) + $item->laporan40Fit($month);
                                                @endphp
                                            @endif
                                        @endfor
                                        <th class="text-center text-warning">{{ $fit40 }}</th>
                                        <th class="text-center text-warning">{{ $fit20 }}</th>
                                        <th class="text-center text-warning">{{ $fit20 + $fit40 }}</th>
                                        @php
                                            $sub[25] = ($sub[25]??0) + $fit40;
                                            $sub[26] = ($sub[26]??0) + $fit20;
                                            $total += $fit20 + $fit40;
                                        @endphp
                                    </tr>
                                @endforeach
                                <tr>
                                    <th rowspan="2" class="align-middle text-center text-primary">Total</th>
                                    @for ($i = 1; $i <=26; $i++)
                                    <th class="text-center text-primary">{{ $sub[$i] }}</th>
                                    @endfor
                                    <th rowspan="2" class="align-middle text-center text-primary">{{ $total }}</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 26; $i+=2)
                                    <th class="text-center text-primary" colspan="2">{{ $sub[$i] + $sub[$i+1] }}</th>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
