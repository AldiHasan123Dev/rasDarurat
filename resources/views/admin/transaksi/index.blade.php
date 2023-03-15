@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTransaksi" aria-controls="offcanvasTransaksi">Tambah Transaksi</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Pembayar_id</th>
                                <th>Invoice</th>
                                <th>Nsfp</th>
                                <th>Keterangan</th>
                                <th>Tujuan</th>
                                <th>Sub_total</th>
                                <th>Tagihan</th>
                                <th>Ppn</th>
                                <th>Asuransi</th>
                                <th>Admin</th>
                                <th>Total</th>
                                <th>Pph</th>
                                <th>Job</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTransaksi" aria-labelledby="offcanvasTransaksiLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTransaksiLabel">Form Transaksi</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('transaksi.store') }}" method="post">
                @csrf
                @include('admin.transaksi.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('transaksi.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
            { data: 'pembayar_id', name: 'pembayar_id' },
            { data: 'invoice', name: 'invoice' },
            { data: 'nsfp', name: 'nsfp' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'tujuan', name: 'tujuan' },
            { data: 'sub_total', name: 'sub_total' },
            { data: 'tagihan', name: 'tagihan' },
            { data: 'ppn', name: 'ppn' },
            { data: 'asuransi', name: 'asuransi' },
            { data: 'admin', name: 'admin' },
            { data: 'total', name: 'total' },
            { data: 'pph', name: 'pph' },
            { data: 'job', name: 'job' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection