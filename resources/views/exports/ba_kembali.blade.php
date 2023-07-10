<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Job</th>
            <th>ID JOB</th>
            <th>Pembayar</th>
            <th>Pengirim</th>
            <th>Penerima</th>
            <th>Penerima BL</th>
            <th>Dari</th>
            <th>Tujuan</th>
            <th>Shipment</th>
            <th>Kondisi</th>
            <th>Jenis Barang</th>
            <th>Pelayaran</th>
            <th>Kapal</th>
            <th>Voyage</th>
            <th>Container</th>
            <th>Seal</th>
            <th>ETD</th>
            <th>TD</th>
            <th>Barang Diantar</th>
            <th>BA Kembali</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['job'] }}</td>
                <td>{{ $item['no'] }}</td>
                <td>{{ $item['pembayar'] }}</td>
                <td>{{ $item['pengirim'] }}</td>
                <td>{{ $item['penerima'] }}</td>
                <td>{{ $item['penerima_bl'] }}</td>
                <td>{{ $item['dari'] }}</td>
                <td>{{ $item['tujuan'] }}</td>
                <td>{{ $item['shipment'] }}</td>
                <td>{{ $item['kondisi'] }}</td>
                <td>{{ $item['barang'] }}</td>
                <td>{{ $item['pelayaran'] }}</td>
                <td>{{ $item['kapal'] }}</td>
                <td>{{ $item['voyage'] }}</td>
                <td>{{ $item['container'] }}</td>
                <td>{{ $item['seal'] }}</td>
                <td>{{ $item['etd'] }}</td>
                <td>{{ $item['td'] }}</td>
                <td>{{ $item['barang_diantar'] }}</td>
                <td>{{ $item['ba_kembali'] }}</td>
                <td>{{ $item['keterangan'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
