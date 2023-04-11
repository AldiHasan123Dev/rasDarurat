<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Job</th>
            <th>ID JOB</th>
            <th>Invoice</th>
            <th>Asuransi</th>
            <th>Pembayar</th>
            <th>Marketing</th>
            <th>CS</th>
            <th>Pengirim</th>
            <th>Penerima</th>
            <th>Dari</th>
            <th>Tujuan</th>
            <th>Shipment</th>
            <th>Kondisi</th>
            <th>Jenis Barang</th>
            <th>Pelayaran</th>
            <th>Kapal</th>
            <th>Voyage</th>
            <th>ETD</th>
            <th>TD</th>
            <th>BA Kirim</th>
            <th>Nopol</th>
            <th>Trucking</th>
            <th>Container</th>
            <th>Seal</th>
            <th>Stuffing</th>
            <th>Stuffing Tipe</th>
            <th>Tgl Full</th>
            <th>Barang Diantar</th>
            <th>BA Kembali</th>
            <th>Koli</th>
            <th>M3</th>
            <th>Berat</th>
            <th>Satuan</th>
            <th>Unit</th>
            <th>Tarif</th>
            <th>Agen</th>
            <th>Penerima BL</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['tanggal'] }}</td>
                <td>{{ $item['job'] }}</td>
                <td>{{ $item['no'] }}</td>
                <td>{{ $item['invoice'] }}</td>
                <td>{{ $item['asuransi'] }}</td>
                <td>{{ $item['pembayar'] }}</td>
                <td>{{ $item['marketing'] }}</td>
                <td>{{ $item['cs'] }}</td>
                <td>{{ $item['pengirim'] }}</td>
                <td>{{ $item['penerima'] }}</td>
                <td>{{ $item['dari'] }}</td>
                <td>{{ $item['tujuan'] }}</td>
                <td>{{ $item['shipment'] }}</td>
                <td>{{ $item['kondisi'] }}</td>
                <td>{{ $item['barang'] }}</td>
                <td>{{ $item['pelayaran'] }}</td>
                <td>{{ $item['kapal'] }}</td>
                <td>{{ $item['voyage'] }}</td>
                <td>{{ $item['etd'] }}</td>
                <td>{{ $item['td'] }}</td>
                <td>{{ $item['ba_kirim'] }}</td>
                <td>{{ $item['nopol'] }}</td>
                <td>{{ $item['trucking'] }}</td>
                <td>{{ $item['container'] }}</td>
                <td>{{ $item['seal'] }}</td>
                <td>{{ $item['stuffing'] }}</td>
                <td>{{ $item['stuffing_type'] }}</td>
                <td>{{ $item['full'] }}</td>
                <td>{{ $item['barang_diantar'] }}</td>
                <td>{{ $item['ba_kembali'] }}</td>
                <td>{{ $item['koli'] }}</td>
                <td>{{ $item['m3'] }}</td>
                <td>{{ $item['berat'] }}</td>
                <td>{{ $item['satuan'] }}</td>
                <td>{{ $item['unit'] }}</td>
                <td>{{ $item['tarif'] }}</td>
                <td>{{ $item['agen'] }}</td>
                <td>{{ $item['penerima_bl'] }}</td>
                <td>{{ $item['keterangan'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
