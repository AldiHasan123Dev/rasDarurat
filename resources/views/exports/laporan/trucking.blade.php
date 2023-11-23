<table class="table table-sm table-bordered mt-3" style="font-size: .7rem; white-space:nowrap">
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
            <th>NOPOL</th>
            @for ($i = 1; $i <=26; $i++)
            <th class="text-center">{{ $i%2==0?'RIT':'M' }}</th>
            @endfor
            {{-- <th class="text-center">Sub Total</th> --}}
        </tr>
    </thead>
    <tbody>
        @php
            $sub = array();
            $total = 0;
        @endphp
        @foreach ($data as $idx => $item)
            <tr>
                <td class="text-dark">{{ $item->nopol }} ({{ $item->milik }})</td>
                @php
                    $month = 1;
                    $rit = 0;
                    $m = 0;
                @endphp
                @for ($i = 1; $i <=24; $i++)
                    @if ($i%2==0)
                        <th class="text-center">{{ $item->laporanRit($month,$year) }}</th>
                        @php
                            $rit += $item->laporanRit($month,$year);
                            $sub[$i] = ($sub[$i]??0) + $item->laporanRit($month,$year);
                            $month++;
                        @endphp
                    @else
                        <th class="text-center">{{ formatNumber($item->laporanMargin($month,$year)) }}</th>
                        @php
                            $m += $item->laporanMargin($month,$year);
                            $sub[$i] = ($sub[$i]??0) + $item->laporanMargin($month,$year);
                        @endphp
                    @endif
                @endfor
                <th class="text-center text-warning">{{ formatNumber($m) }}</th>
                <th class="text-center text-warning">{{ $rit }}</th>
                {{-- <th class="text-center text-warning">{{ $rit + $m }}</th> --}}
                @php
                    $sub[25] = ($sub[25]??0) + $m;
                    $sub[26] = ($sub[26]??0) + $rit;
                    $total += $rit + $m;
                @endphp
            </tr>
        @endforeach
        <tr>
            <th rowspan="2" class="align-middle text-center text-primary">Total</th>
            @for ($i = 1; $i <=26; $i++)
            @if ($i%2==0)
            <th class="text-center text-primary">{{ $sub[$i] }}</th>
            @else
            <th class="text-center text-primary">{{ formatNumber($sub[$i]) }}</th>
            @endif
            @endfor
            {{-- <th rowspan="2" class="align-middle text-center text-primary">{{ $total }}</th> --}}
        </tr>
        {{-- <tr>
            @for ($i = 1; $i <= 26; $i+=2)
            <th class="text-center text-primary" colspan="2">{{ formatNumber($sub[$i] + $sub[$i+1]) }}</th>
            @endfor
        </tr> --}}
    </tbody>
</table>
