<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        td {
            font-family: 'Segoe UI', sans-serif;
            font-size: 10pt;
        }
           th {
        font-family: 'Calibri', sans-serif;
        font-size: 11pt;
        font-weight: bold;
    }

    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>Baris</th>
                <th>Tanggal Faktur</th>
                <th>Jenis Faktur</th>
                <th>Kode Transaksi</th>
                <th>Keterangan Tambahan</th>
                <th>Dokumen Pendukung</th>
                <th>Periode Dok Pendukung</th>
                <th>Refrensi</th>
                <th>Cap Fasilitas</th>
                <th>ID TKU Penjual</th>
                <th>NPWP/NIK Pembeli</th>
                <th>Jenis ID Pembeli</th>
                <th>Negara Pembeli</th>
                <th>Nomor Dokumen Pembeli</th>
                <th>Nama Pembeli</th>
                <th>Alamat Pembeli</th>
                <th>Email Pembeli</th>
                <th>ID TKU Pembeli</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                    <td>Normal</td>
                    <td>05</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $item->invoice }}</td>
                    <td></td>
                    <td>{{ $npwpPenjual . '000000000' }}</td>
                    <td>
    {{ $item->pembayar->nik === '-' ? str_replace('.', '', $item->pembayar->npwp) : $item->pembayar->npwp }}
                    </td>


                    <td>TIN</td>
                    <td>IDN
                    <td>
                    <td>{{ $item->pembayar->nama }}</td>
                    <td>{{ $item->pembayar->alamat }}</td>
                    <td>{{ $item->pembayar->email ?? '-' }}</td>
                    <td>
    {{ $item->pembayar->nik === '-' ? str_replace('.', '', $item->pembayar->npwp) : $item->pembayar->npwp }}
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
