<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>JOB</th>
            <th>No Job</th>
            <th>Carton</th>
            <th>Tgl Stuffing</th>
            <th>No. Cont</th>
            <th>Nopol Truck</th>
            <th>Kapal</th>
            <th>Voyage</th>
            <th>Kondisi</th>
            <th>TD</th>
            <th>ETA</th>
            <th>Barang Diantar</th>
            <th>RC Cust</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['job'] }}</td>
                <td>{{ $item['no'] }}</td>
                <td>{{ $item['koli'] }}</td>
                <td>{{ $item['stuffing'] }}</td>
                <td>{{ $item['container'] }}</td>
                <td>{{ $item['nopol'] }}</td>
                <td>{{ $item['kapal'] }}</td>
                <td>{{ $item['voyage'] }}</td>
                <td>{{ $item['kondisi'] }}</td>
                <td>{{ $item['td'] }}</td>
                <td>{{ $item['eta'] }}</td>
                <td>{{ $item['barang_diantar'] }}</td>
                <td>{{ $item['komisi'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
