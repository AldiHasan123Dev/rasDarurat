@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <table class="table table-sm table-bordered" style="font-size: .7rem">
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
                                <th class="text-center" colspan="2">Total</th>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                @for ($i = 1; $i <=26; $i++)
                                    <th class="text-center">{{ $i%2!=0?'20':40 }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    @php
                                        $month = 1;
                                        $fit20 = 0;
                                        $fit40 = 0;
                                    @endphp
                                    @for ($i = 1; $i <=24; $i++)
                                        @if ($i%2==0)
                                            <th class="text-center">{{ $item->laporanCs20Fit($month) }}</th>
                                            @php
                                                $month++;
                                                $fit20 += $item->laporanCs20Fit($month);
                                            @endphp
                                        @else
                                            <th class="text-center">{{ $item->laporanCs40Fit($month) }}</th>
                                            @php
                                                $fit40 += $item->laporanCs40Fit($month);
                                            @endphp
                                        @endif
                                    @endfor
                                    <td>{{ $fit40 }}</td>
                                    <td>{{ $fit20 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
