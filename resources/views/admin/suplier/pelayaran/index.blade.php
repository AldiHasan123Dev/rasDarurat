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
            <a href="{{ route('pelayaran.index') }}" class="btn-link p-3">Pelayaran <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('truk.index') }}" class="btn-link p-3 text-dark">Truk <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('asuransi.index') }}" class="btn-link p-3 text-dark">Asuransi <span class="nav-link-icon"></span></span></a>
        </div>
    </div>
</div>
<div class="content-main">
    <div class="card">
        <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
            <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasPelayaran" aria-controls="offcanvasPelayaran">Tambah Pelayaran</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm nowrap" id="tb-pelayaran" style="font-size:.7rem">
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
            <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarifPelayaran" aria-controls="offcanvasTarifPelayaran">Tambah Tarif Pelayaran</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm nowrap" id="tb-tarif" style="font-size:.7rem">
                    <thead>
                        <tr>
                            <th>ID.</th>
                            <th>Pelayaran</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTarifPelayaran" aria-labelledby="offcanvasTarifPelayaranLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTarifPelayaranLabel">Form Tarif Pelayaran</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('tarifpelayaran.store') }}" method="post">
                @csrf
                @include('admin.tarifpelayaran.form')
            </form>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasPelayaran" aria-labelledby="offcanvasPelayaranLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasPelayaranLabel">Form Pelayaran</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('pelayaran.store') }}" method="post">
                @csrf
                @include('admin.suplier.pelayaran.form',['pelayaran'=>[]])
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
        let pelayaran_id = 1;
        let tb_pelayaran = $('#tb-pelayaran').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('pelayaran.data') }}',
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
                url: '{{ route('tarifpelayaran.data') }}',
                method:'POST',
                data:function(d){
                    d.pelayaran_id = pelayaran_id;
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'pelayaran_id', name: 'pelayaran_id' },
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

        $('#tb-pelayaran tbody').on( 'click', 'tr', function () {
            id =  tb_pelayaran.row( this ).data().id;
            pelayaran_id = id;
            tb_tarif.ajax.reload();
        });
        $("select[name=dari]").select2({
            dropdownParent: $('#offcanvasTarifPelayaran')
        });
        $("select[name=tujuan]").select2({
            dropdownParent: $('#offcanvasTarifPelayaran')
        });
        $("select[name=pelayaran_id]").select2({
            dropdownParent: $('#offcanvasTarifPelayaran')
        });
    </script>
@endsection
