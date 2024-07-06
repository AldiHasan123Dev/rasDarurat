<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>JOB</th>
            <th>RC Cust</th>
            <th>Carton</th>
            <th>Tgl Stuffing</th>
            <th>No. Cont</th>
            <th>Nopol Truck</th>
            <th>Kapal</th>
            <th>TD</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['job'] }}</td>
                <td>{{ $item['komisi'] }}</td>
                <td>{{ $item['koli'] }}</td>
                <td>{{ $item['stuffing'] }}</td>
                <td>{{ $item['container'] }}</td>
                <td>{{ $item['nopol'] }}</td>
                <td>{{ $item['kapal'] }}</td>
                <td>{{ $item['td'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
