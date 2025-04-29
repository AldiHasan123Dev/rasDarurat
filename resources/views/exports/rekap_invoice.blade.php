<style>
    table, th, tr, td {
        border: 1px solid black;
    }
</style>

<table>
    <thead>
        <tr>
            <th>No Invoice :</th>
            <th>{{ $data[0]->invoice ?? '' }}</th>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th colspan="2">Surat Jalan</th>
            <th rowspan="2">Penerima</th>
            <th rowspan="2">No Container</th>
            <th rowspan="2">Tanggal TD</th>
            <th rowspan="2">Nama Kapal/ Voyage</th>
            <th rowspan="2">Shipment</th>
            <th rowspan="2">No Polisi</th>
            <th rowspan="2">Tarif</th>
            <th rowspan="2">Tujuan</th>
            <th rowspan="2">Muatan / Qty</th>
            <th rowspan="2">Charge</th>
            <th rowspan="2">Total Nominal (Qty x Charge)</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>No Surat Jalan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach ($data as $order)
            @foreach ($order->bttb as $item)
            <tr>
                @if ($loop->first)
                <td rowspan="{{ $order->bttb->count() }}">{{ $no }}</td>
                <td rowspan="{{ $order->bttb->count() }}">{{ $order->stuffing ? date('d/m/Y', strtotime($order->stuffing)) : '' }}</td>
                <td rowspan="{{ $order->bttb->count() }}"></td>
                <td rowspan="{{ $order->bttb->count() }}">{{ $order->penerima->nama }}</td>
                <td rowspan="{{ $order->bttb->count() }}">{{ $order->container }}</td>
                <td rowspan="{{ $order->bttb->count() }}">{{ $order->jadwal_kapal ? ($order->jadwal_kapal->td ? date('d/m/y', strtotime($order->jadwal_kapal->td)) : '') : '' }}</td>
                <td rowspan="{{ $order->bttb->count() }}">{{ $order->jadwal_kapal->kapal->nama ?? '' }} / {{ $order->jadwal_kapal->voyage ?? '' }}</td>
                <td rowspan="{{ $order->bttb->count() }}">{{ $order->tarif->shipmentInfo->nama ?? '-' }}</td>
                <td rowspan="{{ $order->bttb->count() }}">{{ $order->nopol }}</td>
                <td rowspan="{{ $order->bttb->count() }}">{{ $order->tarif->tarif ?? '' }}</td>
                <td rowspan="{{ $order->bttb->count() }}">{{ $order->tarif->tujuan_lokasi->nama ?? '' }}</td>
                @endif
                <td>{{ $item->qty }}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
            @php
                $no++;
            @endphp
        @endforeach
    </tbody>
</table>
