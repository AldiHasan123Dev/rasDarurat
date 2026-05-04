<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Invoice</th>
            <th>NPWP</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Nama NPWP</th>
            <th>Alamat NPWP</th>
            <th>Tanggal Faktur</th>
            <th>Tujuan</th>
            <th>Uraian</th>
            <th>Daftar Faktur Pajak</th>
            <th>Sub Total</th>
            <th>PPN</th>
            <th>Total</th>
            <th>PPH</th>
            <th>No.JOB</th>
            <th>Jurnal BUPOT</th>
            <th>No BUPOT</th>
            <th>Tanggal BUPOT</th>
            <th>No BUPOT</th>
            <th>Masa BUPOT</th>
            <th>BUPOT</th>
            <th>Selisih BUPOT</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaksi as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->invoice }}</td>
                <td>{{ $item->pembayar->npwp }}'</td>
                <td>{{ $item->pembayar->nik }}'</td>
                <td>{{ $item->pembayar->nama }}</td>
                <td>{{ $item->pembayar->nama_npwp }}</td>
                <td>{{ $item->pembayar->alamat_npwp }}</td>
                <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                <td>{{ $item->tujuan }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ $item->nsfp }}</td>
                <td>{{ number_format(round($item->sub_total)) }}</td>
                <td>{{ number_format(round($item->ppn)) }}</td>
                <td>{{ number_format(round($item->ppn)+round($item->sub_total)) }}</td>
                <td>{{ number_format($item->pph) }}</td>
                <td>{{ $item->no_job() }}</td>
                <td>{{ $item->jurnal_bupot }}</td>
                <td>{{ $item->no_bupot }}</td>
                <td>{{ $item->tanggal_bupot }}</td>
                <td>{{ $item->masa_bupot }}</td>
                <td>{{ number_format(round($item->bupot)) }}</td>
                <td>{{ $item->selisih_bupot }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="11">JUMLAH</td>
            <td>{{ number_format(round($transaksi->sum('sub_total'))) }}</td>
            <td>{{ number_format(round($transaksi->sum('ppn'))) }}</td>
            <td>{{ number_format(round($transaksi->sum('ppn')) + round($transaksi->sum('sub_total'))) }}</td>
            <td>{{ number_format(round($transaksi->sum('pph'))) }}</td>
        </tr>
    </tbody>
</table>
