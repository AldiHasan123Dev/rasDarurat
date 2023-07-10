@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
@endsection
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
                        <form action="{{ route('nsfp.delete.all') }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('are you sure?')">Hapus Semua NSFP</button>
                        </form>
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
                                        <th>Aksi</th>
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
                        <div class="d-flex gap-5">
                            <span class="mt-2">NSFP: <span class="nsfp"></span></span>
                            <span class="mt-2">INVOICE: <span class="invoice"></span></span>
                            <div class="d-flex gap-2" id="action">
                                <form action="{{ route('nsfp.revisi.non') }}" method="post" id="revisi1">
                                    @csrf
                                    <input type="hidden" name="id" class="id-nsfp">
                                    <button type="submit" onclick="return confirm('are you sure?')" class="btn btn-sm btn-primary"> Revisi Tarif</button>
                                </form>
                                <form action="{{ route('nsfp.revisi') }}" method="post" id="revisi">
                                    @csrf
                                    <input type="hidden" name="id" class="id-nsfp">
                                    <button type="submit" onclick="return confirm('are you sure?')" class="btn btn-sm btn-warning"> Revisi Faktur</button>
                                </form>
                                <form action="{{route('nsfp.tarik')}}" method="post" id="tarik">
                                    @csrf
                                    <input type="hidden" name="id" class="id-nsfp">
                                    <button type="submit" onclick="return confirm('are you sure?')" class="btn btn-sm btn-danger"> Tarik Faktur</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="table-invoice" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>ID.</th>
                                        <th class="text-center">No.</th>
                                        <th>NSFP</th>
                                        <th>Invoice</th>
                                        <th>Keterangan</th>
                                        {{-- <th>Action</th> --}}
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
    <script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
    <script>
        $('#revisi1').hide();
        $('#revisi').hide();
        $('#tarik').hide();
        let id;
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
                { data: 'keterangan', name: 'keterangan' },
                { data: 'action', name: 'action', orderable: false, searchable: false  },
            ],"columnDefs": [
                { className: "text-center", "targets": [1] }
            ]
        });

        let tableInvoice = $('#table-invoice').DataTable({
            processing: true,
            serverSide: true,
            select: true,
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
                // { data: 'action', name: 'action', orderable: false, searchable: false },
            ],"columnDefs": [
                { className: "text-center", "targets": [1] }
            ]
        });

        $('#table-invoice tbody').on( 'click', 'tr', function () {
            $('#revisi1').show();
            $('#revisi').show();
            $('#tarik').show();
            id =  tableInvoice.row( this ).data().id;
            $('.id-nsfp').val(id);
            $('.nsfp').html(tableInvoice.row(this).data().nomor);
            $('.invoice').html(tableInvoice.row(this).data().invoice);
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
