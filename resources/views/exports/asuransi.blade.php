<table>
    <thead>
    <tr>
        <th>NO</th>
        <th>NO JOB</th>
        <th>NAMA ASURANSI</th>
        <th>NAMA CUSTOMER</th>
        <th>PENGIRIM</th>
        <th>PENERIMA</th>
        <th>PELAYARAN</th>
        <th>NAMA KAPAL</th>
        <th>VOYAGE</th>
        <th>TD STUFFING</th>
        <th>TD KAPAL</th>
        <th>TIPE PENGEMASAN</th>
        <th>JUMLAH YANG DI ASURANSIKAN</th>
        <th>NO CONTAINER</th>
        <th>NO SEAL</th>
        <th>BARANG YANG DIASURANSIKAN</th>
        <th>HARGA PERTANGGUNGAN</th>
        <th>TUJUAN</th>
        <th>BIAYA POLIS</th>
        <th>NILAI PREMI + BIAYA POLIS</th>
        <th>TOTAL BIAYA NILAI</th>
    </tr>
    </thead>
    <tbody>
    @php
        $no = 1;
    @endphp
    @foreach($asuransi_cont as $order)
        <tr>
            <td>{{ $no }}</td>
            <td>{{ $order->job }}-{{ sprintf('%02d',$order->no_job) }}</td>
            <td>{{ $order->asuransiInfo->nama }}</td>
            <td>{{ $order->tarif->customer->nama }}</td>
            <td>{{ $order->pengirim->nama }}</td>
            <td>{{ $order->penerima->nama }}</td>
            <td>{{ $order->jadwal_kapal->pelayaran->nama }}</td>
            <td>{{ $order->jadwal_kapal->kapal->nama }}</td>
            <td>{{ $order->jadwal_kapal->voyage }}</td>
            <td>{{ $order->stuffing }}</td>
            <td>{{ $order->jadwal_kapal->td ?? '-' }}</td>
            <td>CONT</td>
            <td>1</td>
            <td>{{ $order->container }}</td>
            <td>{{ $order->seal }}</td>
            <td>{{ $order->barang->nama }}</td>
            <td>{{ $order->pertanggungan }}</td>
            <td>{{ $order->tarif->tujuan_lokasi->nama }}</td>
            <td>{{ $order->asuransiInfo->rate/100 }}</td>
            <td>{{ (($order->asuransiInfo->rate/100) * $order->pertanggungan) + $order->asuransiInfo->admin }}</td>
            <td></td>
        </tr>
        @php
            $no++;
        @endphp
    @endforeach
    @foreach($asuransi_job as $orders)
        @foreach ($orders as $order)
            <tr>
                <td>{{ $no }}</td>
                <td>{{ $order->job }}-{{ sprintf('%02d',$order->no_job) }}</td>
                <td>{{ $order->asuransiInfo->nama }}</td>
                <td>{{ $order->tarif->customer->nama }}</td>
                <td>{{ $order->jadwal_kapal->kapal->nama }}</td>
                <td>{{ $order->jadwal_kapal->voyage }}</td>
                <td>{{ $order->stuffing }}</td>
                <td>{{ $order->jadwal_kapal->etd }}</td>
                <td>CONT</td>
                @if ($loop->first)
                    <td rowspan="{{ $orders->count() }}">{{ $orders->count() }}</td>
                @endif
                <td>{{ $order->container }}</td>
                <td>{{ $order->seal }}</td>
                <td>{{ $order->barang->nama }}</td>
                @if ($loop->first)
                <td rowspan="{{ $orders->count() }}">{{ $order->pertanggungan }}</td>
                @endif
                <td>{{ $order->tarif->tujuan_lokasi->nama }}</td>
                @if ($loop->first)
                <td rowspan="{{ $orders->count() }}">{{ $order->asuransiInfo->rate/100 }}</td>
                <td rowspan="{{ $orders->count() }}">{{ (($order->asuransiInfo->rate/100) * $order->pertanggungan) + $order->asuransiInfo->admin }}</td>
                @endif
                <td></td>
            </tr>
            @php
                $no++;
            @endphp
        @endforeach
    @endforeach
    </tbody>
</table>
