<table style="width:100%; border-collapse: collapse;">
    <thead style="background-color: #e0e0e0;"> {{-- Abu-abu --}}
        <tr>
            <th>No.</th>
            <th>Job</th>
            <th>No Inv</th>
            <th>Nama Kapal</th>
            <th>ETD</th>
            <th>No. Cont</th>
            <th>Volume ('20/40')</th>
            <th>Tarif</th>
            <th>PPN (1,1%)</th>
            <th>Total Tagihan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grandTotal = 0;
        @endphp

        @foreach ($data as $item)
            @php
                $ppn = round($item['tarif1'] * 0.011);
                $subtotal = $ppn + $item['tarif1'];
                $grandTotal += $subtotal;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['no'] }}</td>
                <td>{{ $item['invoice'] }}</td>
                <td>{{ $item['kapal'] }}</td>
                <td>{{ $item['etd'] }}</td>
                <td>{{ $item['container'] }}</td>
                <td>{{ $item['shipment'] }}</td>
                <td>{{ $item['tarif1'] }}</td>
                <td>{{ $ppn }}</td>
                <td>{{ $subtotal }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot style="background-color: #f5f5f5; font-weight: bold;">
        <tr>
            <td colspan="9" style="text-align: right;">Grand Total</td>
            <td>{{ $grandTotal }}</td>
        </tr>
    </tfoot>
</table>
