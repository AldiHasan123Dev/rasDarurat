<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kode</th>
            <th>COA</th>
            <th>Nomor</th>
            <th>Container</th>
            <th>Nopol</th>
            <th>JOB</th>
            <th>Invoice</th>
            <th>Keterangan</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>No BG</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                <td>{{ $item->coa->kode }}</td>
                <td>{{ $item->coa->nama }}</td>
                <td>{{ $item->nomor }}</td>
                <td>{{ $item->container }}</td>
                <td>{{ $item->nopol }}</td>
                <td>{{ $item->order ? $item->order->job.'-'.sprintf('%02d',$item->order->no_job) : '-' }}</td>
                <td>{{ $item->invoice }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ number_format($item->debit,2,',','.') }}</td>
                <td>{{ number_format($item->credit,2,',','.') }}</td>
                <td>{{ $item->no_bg }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
