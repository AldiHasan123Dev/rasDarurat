@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
@endsection
@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header py-2 px-5 d-flex justify-content-between" style="gap:10px">
                        {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNSFP" aria-controls="offcanvasNSFP">Tambah NSFP</button> --}}
                        <b>Faktur Pajak Invoice di Tarik</b>
                        <div class="d-flex gap-5">
                            <span class="mt-2">NSFP: <span class="nsfp"></span></span>
                            <span class="mt-2">INVOICE: <span class="invoice"></span></span>
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
        let tableInvoice = $('#table-invoice').DataTable({
            processing: true,
            serverSide: true,
            select: true,
            ajax:{
                url: '{{ route('nsfp.data') }}',
                method:'POST',
                data:{
                    filter:'tarik'
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
            id =  tableInvoice.row( this ).data().id;
            $('.id-nsfp').val(id);
            $('.nsfp').html(tableInvoice.row(this).data().nomor);
            $('.invoice').html(tableInvoice.row(this).data().invoice);
        });
    </script>
@endsection
