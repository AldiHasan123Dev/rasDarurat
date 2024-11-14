<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Pengirim</th>
            <th>Tgl Stuffing</th>
            <th>No. Cont</th>
            <th>Nama Barang</th>
            <th>Kapal</th>
            <th>TD</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['pengirim'] }}</td>
                <td>{{ $item['stuffing'] }}</td>
                <td>{{ $item['container'] }}</td>
                <td>{{ $item['barang'] }}</td>
                <td>{{ $item['kapal'] }}</td>
                <td>{{ $item['td'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
