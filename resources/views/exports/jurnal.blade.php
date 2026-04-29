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
            <td>{{ number_format($saldoAwal, 2, ',', '.') }}</td>
            <td>-</td>
        </tr>

        {{-- DATA --}}
        @php $lastSaldo = $saldoAwal; @endphp

        @foreach ($data as $item)
            @php $lastSaldo = $item->running_saldo; @endphp
            <tr>
                <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                <td>{{ $item->nomor ?? '-' }}</td>
                <td>{{ $item->container ?? '-' }}</td>
                <td>{{ $item->nopol ?? '-' }}</td>
                <td>
                    {{ $item->order ? $item->order->job . '-' . sprintf('%02d', $item->order->no_job) : '-' }}
                </td>
                <td>{{ $item->invoice ?? '-' }}</td>
                <td>{{ $item->nama ?? '-' }}</td>
                <td>{{ number_format($item->debit, 2, ',', '.') }}</td>
                <td>{{ number_format($item->credit, 2, ',', '.') }}</td>
                <td>{{ number_format($item->running_saldo, 2, ',', '.') }}</td>
                <td>{{ $item->no_bg ?? '-' }}</td>
            </tr>
        @endforeach

        {{-- TOTAL --}}
        <tr>
            <td colspan="7"><b>JUMLAH</b></td>
            <td>{{ number_format($summary->debit ?? 0, 2, ',', '.') }}</td>
            <td>{{ number_format($summary->credit ?? 0, 2, ',', '.') }}</td>
            <td>{{ number_format($lastSaldo, 2, ',', '.') }}</td>
            <td></td>
        </tr>

    </tbody>
</table>
