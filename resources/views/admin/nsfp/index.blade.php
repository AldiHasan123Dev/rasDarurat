@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card p-3 shadow-lg">
                    <div class="row" style="font-size: .7rem">
                        <div class="col">
                            <div class="mb-2">
                                <label for="nomor">Nomor Awal Faktur</label>
                                <input type="text" class="form-control" id="nomor-i">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-2">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah-i">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-2">
                                <button type="button" class="btn btn-sm btn-success mt-3" id="generate">Generate No Faktur</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header py-2 px-5 d-flex justify-content-between" style="gap:10px">
                        {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNSFP" aria-controls="offcanvasNSFP">Tambah NSFP</button> --}}
                        <b>Nomor Faktur Tersedia</b>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="table-available" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>ID.</th>
                                        <th>No.</th>
                                        <th>NSFP</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header py-2 px-5 d-flex justify-content-between" style="gap:10px">
                        {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNSFP" aria-controls="offcanvasNSFP">Tambah NSFP</button> --}}
                        <b>Faktur Pajak Invoice</b>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="table-invoice" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>ID.</th>
                                        <th>No.</th>
                                        <th>NSFP</th>
                                        <th>Invoice</th>
                                        <th>Keterangan</th>
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
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasNSFP" aria-labelledby="offcanvasNSFPLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNSFPLabel">Form NSFP</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('nsfp.store') }}" method="post">
                @csrf
                @include('admin.nsfp.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let table = $('#table-available').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('nsfp.data') }}',
                method:'POST',
                data:{
                    filter:'available'
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id', visible:false },
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                { data: 'nomor', name: 'nomor' },
                { data: 'invoice', name: 'invoice' },
            ]
        });

        let tableInvoice = $('#table-invoice').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('nsfp.data') }}',
                method:'POST',
                data:{
                    filter:'invoice'
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id', visible:false },
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                { data: 'nomor', name: 'nomor' },
                { data: 'invoice', name: 'invoice' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $('#generate').click(function (e) {
            if (confirm('are you sure?')) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('api.nsfp.generate') }}",
                    data: {
                        nomor:$('#nomor-i').val(),
                        jumlah:$('#jumlah-i').val(),
                    },
                    success: function (response) {
                        table.ajax.reload();
                    }
                });
            }
        });
    </script>
@endsection
