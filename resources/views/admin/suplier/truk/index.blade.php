@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<style>
    table.dataTable tbody th, table.dataTable tbody td{
        padding: 0px 10px !important;
    }
    .select2.select2-container.select2-container--default{
        width: 100% !important;
    }
</style>
@endsection
@section('content')
<div class="horizontal-menu">
    <div class="d-flex gap-2 flex-nowrap" style="overflow-x:auto">
        <div class="sub-menu">
            <a href="{{ route('agen.index') }}" class="btn-link p-3 text-dark">Agen <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('pelayaran.index') }}" class="btn-link p-3 text-dark">Pelayaran <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('truk.index') }}" class="btn-link p-3">Truk <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('asuransi.index') }}" class="btn-link p-3 text-dark">Asuransi <span class="nav-link-icon"></span></span></a>
        </div>
    </div>
</div>
<div class="content-main">
    <div class="card">
        <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
            <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTruk" aria-controls="offcanvasTruk">Tambah Suplier Truk</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm nowrap" id="tb-truk" style="font-size:.7rem">
                    <thead>
                        <tr>
                            <th>ID.</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Pic</th>
                            <th>Alamat</th>
                            <th>Kota</th>
                            <th>Telp</th>
                            <th>HP</th>
                            <th>Fax</th>
                            <th>Email</th>
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

<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
            <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarifTruk" aria-controls="offcanvasTarifTruk">Tambah Tarif Truk</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm nowrap" id="tb-tarif" style="font-size:.7rem">
                    <thead>
                        <tr>
                            <th>ID.</th>
                            <th>Truk</th>
                            <th>Tanggal</th>
                            <th>Dari</th>
                            <th>Tujuan</th>
                            <th>Shipment</th>
                            <th>Tarif</th>
                            <th>Kubikasi</th>
                            <th>Keterangan</th>
                            <th>Status</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTruk" aria-labelledby="offcanvasTrukLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTrukLabel">Form Truk</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('truk.store') }}" method="post">
                @csrf
                @include('admin.suplier.truk.form',['truk'=>[]])
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTarifTruk" aria-labelledby="offcanvasTarifTrukLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTarifTrukLabel">Form Tarif Truk</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('tariftruk.store') }}" method="post" id="tarif-create">
                @csrf
                @include('admin.tariftruk.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script>
    $(document).ready(function() {
        document.oncontextmenu = new Function("return false");
        $('body').bind('cut copy paste', function(event) {
            event.preventDefault();
        });
    });
</script>
    <script>
        let truk_id = 1;
        let tb_truk = $('#tb-truk').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('truk.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
            { data: 'kode', name: 'kode' },
            { data: 'nama', name: 'nama' },
            { data: 'pic', name: 'pic' },
            { data: 'alamat', name: 'alamat' },
            { data: 'kota', name: 'kota' },
            { data: 'telp', name: 'telp' },
            { data: 'hp', name: 'hp' },
            { data: 'fax', name: 'fax' },
            { data: 'email', name: 'email' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            select:true
        });

        let tb_tarif = $('#tb-tarif').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('tariftruk.data') }}',
                method:'POST',
                data:function(d){
                    d.truk_id = truk_id;
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'truk_id', name: 'truk_id' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'dari', name: 'dari' },
                { data: 'tujuan', name: 'tujuan' },
                { data: 'tipe', name: 'tipe' },
                { data: 'tarif', name: 'tarif' },
                { data: 'kubikasi', name: 'kubikasi' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $('#tb-truk tbody').on( 'click', 'tr', function () {
            id =  tb_truk.row( this ).data().id;
            $('#tarif-create #truk_id').val(id).trigger('change');
            truk_id = id;
            tb_tarif.ajax.reload();
        });
        $("select[name=dari]").select2({
            dropdownParent: $('#offcanvasTarifTruk')
        });
        $("select[name=tujuan]").select2({
            dropdownParent: $('#offcanvasTarifTruk')
        });
        $("select[name=truk_id]").select2({
            dropdownParent: $('#offcanvasTarifTruk')
        });
    </script>
@endsection
