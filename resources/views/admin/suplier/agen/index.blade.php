@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
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
            <a href="{{ route('agen.index') }}" class="btn-link p-3">Agen <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('pelayaran.index') }}" class="btn-link p-3 text-dark">Pelayaran <span class="nav-link-icon"></span></span></a>
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
            <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAgen" aria-controls="offcanvasAgen">Tambah Agen</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm nowrap" style="font-size:.7rem" id="tb-agen">
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
            <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarifAgen" aria-controls="offcanvasTarifAgen">Tambah Tarif Agen</button>
        </div>
        <div class="card-body">
            <div class="table-responsives">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
            </div>
        </div>
    </div>
</div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasAgen" aria-labelledby="offcanvasAgenLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasAgenLabel">Form Agen</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('agen.store') }}" method="post">
                @csrf
                @include('admin.suplier.agen.form',['agen'=>[]])
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTarifAgen" aria-labelledby="offcanvasTarifAgenLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTarifAgenLabel">Form Tarif Agen</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('tarifagen.store') }}" method="post" id="tarif-create">
                @csrf
                @include('admin.tarifagen.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ asset('assets/js/jquery-serializeFields.js') }}"></script>
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script>
    $(document).ready(function() {
        document.oncontextmenu = new Function("return false");
        $('body').bind('cut copy paste', function(event) {
            event.preventDefault();
        });
    });
</script>
    <script>
        let agen_id = 1;
        let tb_agen = $('#tb-agen').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('agen.data') }}',
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

    $("#jqGrid").jqGrid({
        url: '{{ route('jqgrid.tarif.agent') }}',
        mtype: 'GET',
        datatype: 'json',
        postData: { agen_id: 1 },
        colModel: [
            {search:true, name: 'class', label : 'class', hidden:true, width:10, frozen: true},
            {search:true, name: 'id', label : 'id', width:50, frozen: true},
            {search:true, name: 'agen', label : 'agen', width:50, frozen: true},
            {search:true, name: 'tanggal', label : 'tanggal', sorttype: 'date', datefmt:'d/m/y', width:100, frozen: true},
            {search:true, name: 'dari', label : 'dari'},
            {search:true, name: 'tujuan', label : 'tujuan'},
            {search:true, name: 'tipe', label : 'tipe'},
            {search:true, name: 'tarif', label : 'tarif'},
            {search:true, name: 'kubikasi', label : 'kubikasi'},
            {search:true, name: 'keterangan', label : 'keterangan'},
            {search:true, name: 'is_active', label : 'status'},
        ],
        autowidth: true,
        shrinkToFit: false,
        height: 250,
        oadonce: true,
        rowNum: 25,
        rowList:[10,25,50,100,250,500,1000],
        viewrecords: true,
        pager: "#jqGridPager",
        caption: "List Tarif Agen",
        onCellSelect: function (rowId, iRow, iCol, e) {
            row_id = rowId;
            agen_id = $(this).jqGrid('getCell', rowId, 'id');
            console.log(agen_id);
        },
        rowattr: function (item) {
            return { "class": item.class };
        }
    });

    $('#jqGrid').jqGrid('filterToolbar');
    $('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
        search: false, // show search button on the toolbar
        add: false,
        edit: false,
        del: false,
        refresh: true
    });

    $("#jqGrid").jqGrid('setFrozenColumns');

    $('#tb-agen tbody').on( 'click', 'tr', function () {
        agen_id =  tb_agen.row( this ).data().id;
        $('#tarif-create #agen_id').val(agen_id).trigger('change');
        $("#jqGrid").jqGrid('setGridParam', {
                postData: {agen_id:agen_id }
        }).trigger('reloadGrid');
    });
    $("select[name=dari]").select2({
        dropdownParent: $('#offcanvasTarifAgen')
    });
    $("select[name=tujuan]").select2({
        dropdownParent: $('#offcanvasTarifAgen')
    });
    $("select[name=agen_id]").select2({
        dropdownParent: $('#offcanvasTarifAgen')
    });

    $('#tarif-create').submit(function (e) {
        e.preventDefault();
        let data = $(this).serializeFields();
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data:data,
            success: function (response) {
                $('#tarif-create').trigger("reset");
                $('#tarif-create #pelayaran_id').val(agen_id).trigger('change');
                $("#jqGrid").jqGrid('setGridParam', {
                        postData: {agen_id:agen_id }
                }).trigger('reloadGrid');
                alert(response)
            }
        });
    });
    </script>
@endsection
