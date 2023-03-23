@extends('layouts.admin')
@section('style')
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<style>
    td, th {
        border: 1px solid #ccc;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card p-3">
                <form action="{{ route('keuangan.ppn.export') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">Export Excel</button>
                </form>
                <div class="table-responsives mt-3">
                    <table class="table table-sm w-100 nowrap" id="table-ppn" style="font-size: .7rem">
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
                                    <td>{{ Str::limit($item->pembayar->alamat_npwp, 30, '...') }}</td>
                                    <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                                    <td>{{ $item->tujuan }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->nsfp }}</td>
                                    <td>{{ number_format(ceil($item->sub_total)) }}</td>
                                    <td>{{ number_format($item->ppn) }}</td>
                                    <td>{{ number_format(ceil($item->ppn + $item->sub_total)) }}</td>
                                    <td>{{ number_format($item->pph) }}</td>
                                    <td>{{ $item->no_job() }}</td>
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

@section('script')
<script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <script>
        let table = $('#table-ppn').dataTable({
            ordering:false,
            scrollX:true,
            dom: 'Blfrtip',
            autoWidth: false,
        });

        $('table th').resizable({
            handles: 'e',
            minWidth: 18,
            stop: function(e, ui) {
                $(this).width(ui.size.width);
            }
        });
    </script>
@endsection
