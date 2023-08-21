<table>
    <thead>
        <tr>
            <th>FK</th>
            <th>KD_JENIS_TRANSAKSI</th>
            <th>FG_PENGGANTI</th>
            <th>NOMOR_FAKTUR</th>
            <th>MASA_PAJAK</th>
            <th>TAHUN_PAJAK</th>
            <th>TANGAL_FAKTUR</th>
            <th>NPWP</th>
            <th>NAMA</th>
            <th>ALAMAT_LENGKAP</th>
            <th>JUMLAH_DPP</th>
            <th>JUMLAH_PPN</th>
            <th>JUMLAH_PPNBM</th>
            <th>ID_KETERANGAN_TAMBAHAN</th>
            <th>FG_UANG_MUKA</th>
            <th>UANG_MUKA_DPP</th>
            <th>UANG_MUKA_PPN</th>
            <th>UANG_MUKA_PPNBM</th>
            <th>REFERENSI</th>
            <th>KODE_DOKUMEN_PENDUKUNG</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaksi as $item)
            <tr>
                <td>FK</td>
                <td>05</td>
                <td>0</td>
                <td>{{ substr(str_replace('.','',$item->nsfp),-13) }}</td>
                <td>{{ (int)date('m',strtotime($item->created_at)) }}</td>
                <td>{{ date('Y',strtotime($item->created_at)) }}</td>
                <td>{{ date('d/m/Y',strtotime($item->created_at)) }}</td>
                <td>{{ str_replace(['.',',','-'],'',$item->pembayar->npwp) }}</td>
                <td>
                    @if ($item->pembayar->npwp=='0'&&$item->pembayar->nik=='-')
                        {{ $item->pembayar->nama_npwp }}
                    @elseif($item->pembayar->npwp)
                        {{ $item->pembayar->nama_npwp }}
                    @else
                        {{ $item->pembayar->nik.'#NIK#NAMA#'.strtoupper(strtolower($item->pembayar->nama)) }}
                    @endif
                </td>
                <td>{{ $item->pembayar->alamat_npwp }}</td>
                <td>{{ ceil($item->sub_total) }}</td>
                <td>{{ ceil($item->ppn) }}</td>
                <td>0</td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>{{ $item->invoice }}</td>
                <td></td>
            </tr>
            <tr>
                @php
                    $price = $item->sub_total;
                    if(substr($item->keterangan,1,2)=='TP'){
                        $price = $item->sub_total - ($item->jobs->count()*500000);
                    }
                @endphp
                <td>OF</td>
                <td></td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ ceil($price) }}</td>
                <td>1</td>
                <td>{{ ceil($price) }}</td>
                <td>0</td>
                <td>{{ ceil($price) }}</td>
                @if (substr($item->keterangan,1,2)=='TP')
                    <td>{{ ceil(ceil($price) * 0.011) }}</td>
                @else
                    <td>{{ ceil($item->ppn) }}</td>
                @endif
                <td>0</td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @if (substr($item->keterangan,1,2)=='TP')
            <tr>
                <td>OF</td>
                <td></td>
                <td>JASA EKSPEDISI</td>
                <td>{{ ceil($item->jobs->count()*500000) }}</td>
                <td>1</td>
                <td>{{ ceil($item->jobs->count()*500000) }}</td>
                <td>0</td>
                <td>{{ ceil($item->jobs->count()*500000) }}</td>
                <td>{{ ceil(($item->jobs->count()*500000) * 0.011) }}</td>
                <td>0</td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>
