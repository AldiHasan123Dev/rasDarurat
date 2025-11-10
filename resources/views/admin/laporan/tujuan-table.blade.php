<table class="table table-sm table-bordered mt-3" id="table1" style="font-size: .7rem">
    <thead>
        <tr>
            <th rowspan="2" class="text-center">Tujuan</th>
            <th class="text-center" colspan="2">Jan</th>
            <th class="text-center" colspan="2">Feb</th>
            <th class="text-center" colspan="2">Mar</th>
            <th class="text-center" colspan="2">Apr</th>
            <th class="text-center" colspan="2">Mei</th>
            <th class="text-center" colspan="2">Jun</th>
            <th class="text-center" colspan="2">Jul</th>
            <th class="text-center" colspan="2">Agu</th>
            <th class="text-center" colspan="2">Sep</th>
            <th class="text-center" colspan="2">Okt</th>
            <th class="text-center" colspan="2">Nov</th>
            <th class="text-center" colspan="2">Des</th>
            <th class="text-center" colspan="4">Total</th>
        </tr>
        <tr>
            @for ($i = 1; $i <= 26; $i++)
                <th class="text-center" style="min-width:40px !important">{{ $i % 2 == 0 ? '20' : 40 }}</th>
            @endfor
            <th class="text-center">Sub Total</th>
            <th class="text-center">Persentase</th>
        </tr>
    </thead>

    <tbody>
        @php $sub = []; $total = 0; @endphp
        @foreach ($data as $idx => $item)
            @php
                $fit20 = $fit40 = 0;
                $tempValues = [];
                for ($m = 1; $m <= 12; $m++) {
                    $v20 = $item->laporan20Fit($m, $year, $port_id);
                    $v40 = $item->laporan40Fit($m, $year, $port_id);
                    $fit20 += $v20;
                    $fit40 += $v40;
                    $tempValues[$m] = ['20' => $v20, '40' => $v40];
                }
            @endphp

            @if ($fit20 + $fit40 == 0)
                @continue
            @endif

            <tr>
                <td>{{ $item->nama }}</td>
                @php $month = 1; @endphp
                @for ($i = 1; $i <= 24; $i++)
                    @if ($i % 2 == 0)
                        <th class="text-center">{{ $tempValues[$month]['20'] }}</th>
                        @php
                            $sub[$i] = ($sub[$i] ?? 0) + $tempValues[$month]['20'];
                            $month++;
                        @endphp
                    @else
                        <th class="text-center">{{ $tempValues[$month]['40'] }}</th>
                        @php
                            $sub[$i] = ($sub[$i] ?? 0) + $tempValues[$month]['40'];
                        @endphp
                    @endif
                @endfor

                <th class="text-center text-warning">{{ $fit40 }}</th>
                <th class="text-center text-warning">{{ $fit20 }}</th>
                <th class="text-center text-warning">{{ $fit20 + $fit40 }}</th>
                <th class="text-center text-warning">
                    {{ $count > 0 ? number_format((($fit20 + $fit40) / $count) * 100, 2, '.', ',') : '0.00' }}%
                </th>

                @php
                    $sub[25] = ($sub[25] ?? 0) + $fit40;
                    $sub[26] = ($sub[26] ?? 0) + $fit20;
                    $total += $fit20 + $fit40;
                @endphp
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th rowspan="2" class="align-middle text-center text-primary">Total</th>
            @for ($i = 1; $i <= 26; $i++)
                <th class="text-center text-primary">{{ $sub[$i] ?? 0 }}</th>
            @endfor
            <th rowspan="2" class="align-middle text-center text-primary">{{ $total }}</th>
            <th rowspan="2" class="align-middle text-center text-primary">100%</th>
        </tr>
        <tr>
            @for ($i = 1; $i <= 26; $i += 2)
                <th class="text-center text-primary" colspan="2">{{ ($sub[$i] ?? 0) + ($sub[$i + 1] ?? 0) }}</th>
            @endfor
        </tr>
    </tfoot>
</table>
