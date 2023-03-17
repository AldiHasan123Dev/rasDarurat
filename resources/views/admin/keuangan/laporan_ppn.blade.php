@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card p-3">
                <div class="table-responsive">
                    <table class="table nowrap table-sm w-100" id="table" style="font-size: .7rem">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksi as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->invoice }}</td>
                                    <td>{{ $item->pembayar->npwp }}</td>
                                    <td>{{ $item->pembayar->nik }}</td>
                                    <td>{{ $item->pembayar->nama }}</td>
                                    <td>{{ $item->pembayar->nama_npwp }}</td>
                                    <td>{{ $item->pembayar->alamat_npwp }}</td>
                                    <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                                    <td>{{ $item->tujuan }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->nsfp }}</td>
                                    <td>{{ number_format(ceil($item->sub_total)) }}</td>
                                    <td>{{ number_format($item->ppn) }}</td>
                                    <td>{{ number_format(ceil($item->ppn + $item->sub_total)) }}</td>
                                    <td>{{ number_format($item->pph) }}</td>
                                    @if ($item->tipe_invoice=='cont')
                                        <td>{{ $item->job }}</td>
                                    @else
                                        <td>{{ $item->no_job() }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
