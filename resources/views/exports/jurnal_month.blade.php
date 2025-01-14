<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Kode</th>
            <th>COA</th>
            <th>Nomor</th>
            <th>Container</th>
            <th>Nopol</th>
            <th>JOB</th>
            <th>Invoice</th>
            <th>Invoice Ext</th>
            <th>Invoice Agen</th>
            <th>Invoice Vendor</th>
            <th>Invoice Trucking</th>
            <th>Relasi</th>
            <th>Keterangan</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>No BG</th>
            <th>TGL BG</th>
            <th>Nomor Bupot</th>
            <th>Masa Pajak</th>
            <th>Tgl Bupot</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                <td>{{ $item->coa->kode }}</td>
                <td>{{ $item->coa->nama }}</td>
                <td>{{ $item->nomor }}</td>
                <td>{{ $item->container }}</td>
                <td>{{ $item->nopol }}</td>
                @if ($item->order)
                <td>{{ $item->order->job.'-'.sprintf('%02d',$item->order->no_job)  }}</td>
                @else
                    @if ($item->order_trucking)
                        @if ($item->order_trucking->order)
                        <td>{{ $item->order_trucking->order->job.'-'.sprintf('%02d',$item->order_trucking->order->no_job)  }}</td>
                        @else
                        <td>-</td>
                        @endif
                    @else
                        <td>-</td>
                    @endif
                @endif
                <td>{{ $item->invoice }}</td>
                <td>{{ $item->invoice_external }}</td>
                <td>{{ $item->invoice_agen }}</td>
                <td>{{ $item->invoice_vendor }}</td>
                <td>{{ $item->invoice_trucking }}</td>
                <td>{{ $item->relasi }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->debit }}</td>
                <td>{{ $item->credit }}</td>
                <td>{{ $item->no_bg }}</td>
                <td>{{ $item->bg_tgl() }}</td>
                <td>{{ $item->transaksi()->no_bupot ?? '' }}</td>
                <td>{{ $item->transaksi()->masa_bupot ?? '' }}</td>
                <td>{{ $item->transaksi()->tanggal_bupot ?? '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
