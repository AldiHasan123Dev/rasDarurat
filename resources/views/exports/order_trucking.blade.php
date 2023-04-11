<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal Muat</th>
            <th>Invoice</th>
            <th>Tgl Invoice</th>
            <th>Customer</th>
            <th>Trucking</th>
            <th>Pembayar</th>
            <th>JOB</th>
            <th>Sopir</th>
            <th>Nopol</th>
            <th>Container</th>
            <th>Seal</th>
            <th>Dari</th>
            <th>Tujuan</th>
            <th>Tipe</th>
            <th>SJ Kembali</th>
            <th>SJ Diterima FA</th>
            <th>Borongan</th>
            <th>Sangu Sopir</th>
            <th>Simpanan Sopir</th>
            <th>Borongan Kuli</th>
            <th>Sangu Kuli</th>
            <th>Simpanan Kuli</th>
            <th>Tambah Isi</th>
            <th>Tambah Solar</th>
            <th>TB/TL</th>
            <th>Tally</th>
            <th>Uang Makan</th>
            <th>Cleaning</th>
            <th>Stappel</th>
            <th>Lain-lain</th>
            <th>Totalan Sopir</th>
            <th>Tarif</th>
            <th>PPh 21(3%)</th>
            <th>PPh 23(2%)</th>
            <th>Total Invoice</th>
            <th>Margin</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['tgl_muat'] }}</td>
                <td>{{ $item['invoice'] }}</td>
                <td>{{ $item['tgl_invoice'] }}</td>
                <td>{{ $item['customer'] }}</td>
                <td>{{ $item['trucking'] }}</td>
                <td>{{ $item['pembayar'] }}</td>
                <td>{{ $item['job'] }}</td>
                <td>{{ $item['sopir'] }}</td>
                <td>{{ $item['nopol'] }}</td>
                <td>{{ $item['container'] }}</td>
                <td>{{ $item['seal'] }}</td>
                <td>{{ $item['dari'] }}</td>
                <td>{{ $item['tujuan'] }}</td>
                <td>{{ $item['tipe'] }}</td>
                <td>{{ $item['sj_kembali'] }}</td>
                <td>{{ $item['sj_kembali_fa'] }}</td>
                <td>{{ $item['borongan'] }}</td>
                <td>{{ $item['sangu'] }}</td>
                <td>{{ $item['simpanan'] }}</td>
                <td>{{ $item['borongan_kuli'] }}</td>
                <td>{{ $item['sangu_kuli'] }}</td>
                <td>{{ $item['simpanan_kuli'] }}</td>
                <td>{{ $item['tambah_isi'] }}</td>
                <td>{{ $item['tambah_solar'] }}</td>
                <td>{{ $item['tb_tl'] }}</td>
                <td>{{ $item['tally'] }}</td>
                <td>{{ $item['uang_makan'] }}</td>
                <td>{{ $item['cleaning'] }}</td>
                <td>{{ $item['stappel'] }}</td>
                <td>{{ $item['lain_lain'] }}</td>
                <td>{{ $item['total_sopir'] }}</td>
                <td>{{ $item['tarif'] }}</td>
                <td>{{ $item['pph_21'] }}</td>
                <td>{{ $item['pph_23'] }}</td>
                <td>{{ $item['total_invoice'] }}</td>
                <td>{{ $item['margin'] }}</td>
                <td>{{ $item['keterangan'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
