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

                <td>{{ number_format($saldo,2,',','.') }}</td>
            <td>-</td>
        </tr>
        @foreach ($data as $item)
        @php
            if ($tipe=='D') {
                if ($item->debit>0) {
                    $saldo += $item->debit;
                } else {
                    $saldo -= $item->credit;
                }
            } else {
                if ($item->debit>0) {
                    $saldo -= $item->debit;
                } else {
                    $saldo += $item->credit;
                }
            }
        @endphp
            <tr>
                <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                <td>{{ $item->nomor }}</td>
                <td>{{ $item->container }}</td>
                <td>{{ $item->nopol }}</td>
                <td>{{ $item->order ? $item->order->job.'-'.sprintf('%02d',$item->order->no_job) : '-' }}</td>
                <td>{{ $item->invoice }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ number_format($item->debit,2,',','.') }}</td>
                <td>{{ number_format($item->credit,2,',','.') }}</td>
                    <td>{{ number_format($saldo,2,',','.') }}</td>
                <td>{{ $item->no_bg }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="7"><b>JUMLAH</b></td>
            <td>{{ number_format($data->sum('debit'),2,',','.') }}</td>
            <td>{{ number_format($data->sum('credit'),2,',','.') }}</td>
                <td>{{ number_format($saldo,2,',','.') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
