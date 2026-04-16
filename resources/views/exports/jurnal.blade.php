<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nomor</th>
            <th>Container</th>
            <th>Nopol</th>
            <th>JOB</th>
            <th>Invoice</th>
            <th>Keterangan</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Saldo</th>
            <th>No BG</th>
        </tr>
    </thead>
    <tbody>

        {{-- SALDO AWAL --}}
        @php $saldoJalan = $saldo; @endphp
        <tr>
            <td>{{ date('d/m/y', strtotime($last)) }}</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>SALDO AWAL</td>
            <td>-</td>
            <td>-</td>
            <td>{{ number_format($saldoJalan,2,',','.') }}</td>
            <td>-</td>
        </tr>

        {{-- DATA --}}
        @foreach ($data as $item)
            @php
                if ($tipe == 'D') {
                    $saldoJalan += ($item->debit - $item->credit);
                } else {
                    $saldoJalan += ($item->credit - $item->debit);
                }
            @endphp

            <tr>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/y') }}</td>
                <td>{{ $item->nomor ?? '-' }}</td>
                <td>{{ $item->container ?? '-' }}</td>
                <td>{{ $item->nopol ?? '-' }}</td>

                {{-- 🔥 FIX N+1 --}}
                <td>
                    {{ $item->order 
                        ? $item->order->job . '-' . sprintf('%02d', $item->order->no_job) 
                        : '-' 
                    }}
                </td>

                <td>{{ $item->invoice ?? '-' }}</td>
                <td>{{ $item->nama ?? '-' }}</td>

                {{-- format tetap tapi minimal --}}
                <td>{{ number_format($item->debit,2,',','.') }}</td>
                <td>{{ number_format($item->credit,2,',','.') }}</td>

                <td>{{ number_format($saldoJalan,2,',','.') }}</td>
                <td>{{ $item->no_bg ?? '-' }}</td>
            </tr>
        @endforeach

        {{-- TOTAL --}}
        <tr>
            <td colspan="7"><b>JUMLAH</b></td>
            <td>{{ number_format($total->debit ?? 0,2,',','.') }}</td>
            <td>{{ number_format($total->credit ?? 0,2,',','.') }}</td>
            <td>{{ number_format($saldoJalan,2,',','.') }}</td>
            <td></td>
        </tr>

    </tbody>
</table>