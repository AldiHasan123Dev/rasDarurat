@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />

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
                            <th>PPh %</th>
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
        <div class="card-header p-2 d-flex" style="gap:10px">
            <button class="py-2 px-3 btn btn-success" onclick="tambahTarif()">Tambah Tarif Pelayaran</button>
            <button class="py-2 px-3 btn btn-primary" onclick="editTarif()">Edit Tarif Pelayaran</button>
            <button class="py-2 px-3 btn btn-danger" onclick="deleteTarif()">Hapus Tarif Pelayaran</button>
            <button class="py-2 px-3 btn btn-secondary" onclick="nonAktif()">Non Aktif</button>
        </div>
        <div class="card-body">
            <div class="table-responsives">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
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
            <form action="{{ route('tarifpelayaran.store') }}" method="post" id="tarif-create">
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

    var offcanvasElementList = [].slice.call(document.querySelectorAll('.offcanvas'))
    var offcanvasList = offcanvasElementList.map(function (offcanvasEl) {
        return new bootstrap.Offcanvas(offcanvasEl)
    })
</script>
    <script>
        var myOffcanvas = document.getElementById('offcanvasTarifPelayaran')
        var offcanvas = new bootstrap.Offcanvas(myOffcanvas)
        let tarif_id = null;
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
                { data: 'pph', name: 'pph' },
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
        url: '{{ route('jqgrid.tarif.pelayaran') }}',
        mtype: 'GET',
        datatype: 'json',
        postData: { pelayaran_id: 1 },
        colModel: [
            {search:true, name: 'class', label : 'class', hidden:true, width:10, frozen: true},
            {search:true, name: 'id', label : 'id', width:50, frozen: true},
            {search:true, name: 'customer', label : 'pembayar', width:50, frozen: true},
            {search:true, name: 'pelayaran', label : 'pelayaran', width:50, frozen: true},
            {search:true, name: 'tanggal', label : 'tanggal', sorttype: 'date', datefmt:'d/m/y', width:100, frozen: true},
            {search:true, name: 'customer_id', label : 'customer_id',hidden:true},
            {search:true, name: 'dari', label : 'dari'},
            {search:true, name: 'tujuan', label : 'tujuan'},
            {search:true, name: 'tipe', label : 'tipe'},
            {search:true, name: 'tarif', label : 'tarif'},
            {search:true, name: 'kubikasi', label : 'kubikasi'},
            {search:true, name: 'komoditi', label : 'komoditi'},
            {search:true, name: 'keterangan', label : 'keterangan'},
            {search:true, name: 'is_active', label : 'status'},
            {search:false, hidden:true, name: 'dari_id', label : 'status'},
            {search:false, hidden:true, name: 'tujuan_id', label : 'status'},
            {search:false, hidden:true, name: 'tipe_id', label : 'status'},
            {search:false, hidden:true, name: 'date_tanggal', label : 'status'},
            {search:false, hidden:true, name: 'tarif_nominal', label : 'status'},
            {search:false, hidden:true, name: 'kubikasi_nominal', label : 'status'},
        ],
        autowidth: true,
        shrinkToFit: false,
        height: 250,
        oadonce: true,
        rowNum: 25,
        rowList:[10,25,50,100,250,500,1000],
        viewrecords: true,
        pager: "#jqGridPager",
        caption: "List Tarif Pelayaran",
        onCellSelect: function (rowId, iRow, iCol, e) {
            row_id = rowId;
            tarif_id = $(this).jqGrid('getCell', rowId, 'id');
            let dari = $(this).jqGrid('getCell', rowId, 'dari_id');
            let tujuan = $(this).jqGrid('getCell', rowId, 'tujuan_id');
            let tipe = $(this).jqGrid('getCell', rowId, 'tipe_id');
            let tanggal = $(this).jqGrid('getCell', rowId, 'date_tanggal');
            let tarif = $(this).jqGrid('getCell', rowId, 'tarif_nominal');
            let kubikasi = $(this).jqGrid('getCell', rowId, 'kubikasi_nominal');
            let keterangan = $(this).jqGrid('getCell', rowId, 'keterangan');
            let komoditi = $(this).jqGrid('getCell', rowId, 'komoditi');
            let customer_id = $(this).jqGrid('getCell', rowId, 'customer_id');
            let is_active = $(this).jqGrid('getCell', rowId, 'is_active');
            $('#is_active_1').attr('checked',false);
            $('#is_active_0').attr('checked',false);
            if (is_active=='AKTIF') {
                $('#is_active_1').attr('checked',true);
                $('#is_active_0').attr('checked',false);
            }else{
                $('#is_active_1').attr('checked',false);
                $('#is_active_0').attr('checked',true);
            }
            $('#tanggal').val(tanggal);
            $('#tipe').val(tipe);
            $('#tarif').val(tarif);
            $('#kubikasi').val(kubikasi);
            $('#keterangan').val(keterangan);
            $('#komoditi').val(komoditi);
            $('#customer_id').val(customer_id).trigger('change');
            $('#tujuan').val(tujuan).trigger('change');
            $('#dari').val(dari).trigger('change');
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

    $('#tb-pelayaran tbody').on( 'click', 'tr', function () {
        pelayaran_id =  tb_pelayaran.row( this ).data().id;
        $('#tarif-create #pelayaran_id').val(pelayaran_id).trigger('change');
        $("#jqGrid").jqGrid('setGridParam', {
                postData: {pelayaran_id:pelayaran_id }
        }).trigger('reloadGrid');
    });
    $("select[name=dari]").select2({
        dropdownParent: $('#offcanvasTarifPelayaran')
    });
    $("select[name=customer_id]").select2({
        dropdownParent: $('#offcanvasTarifPelayaran')
    });
    $("select[name=tujuan]").select2({
        dropdownParent: $('#offcanvasTarifPelayaran')
    });
    $("select[name=pelayaran_id]").select2({
        dropdownParent: $('#offcanvasTarifPelayaran')
    });

    $('#tarif-create').submit(function (e) {
        e.preventDefault();
        let data = $(this).serializeFields();
        data.pelayaran_id = pelayaran_id;
        if (tarif_id) {
            data.tarif_id = tarif_id;
        }
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data:data,
            success: function (response) {
                $('#tarif-create').trigger("reset");
                $('#tarif-create #pelayaran_id').val(pelayaran_id).trigger('change');
                $("#jqGrid").jqGrid('setGridParam', {
                        postData: {pelayaran_id:pelayaran_id }
                }).trigger('reloadGrid');
                alert(response)
                offcanvas.hide();
            }
        });
    });

    function nonAktif(){
        if(confirm('are you sure?')){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('tarifpelayaran.store') }}",
                data:{
                    tarif_id:tarif_id,
                    is_active:0,
                },
                success: function (response) {
                    $("#jqGrid").jqGrid('setGridParam', {
                            postData: {pelayaran_id:pelayaran_id }
                    }).trigger('reloadGrid');
                    alert(response);
                }
            });
        }
    }

    function tambahTarif(){
        tarif_id = null;
        $('#tarif-create').trigger("reset");
        $('#tarif-create #pelayaran_id').val(pelayaran_id).trigger('change');
        $("#jqGrid").jqGrid('setGridParam', {
                postData: {pelayaran_id:pelayaran_id }
        }).trigger('reloadGrid');
        $('#is_active_1').attr('checked',true);
        $('#is_active_0').attr('checked',false);
        $('#customer_id').val('').trigger('change');
        $('#tujuan').val('').trigger('change');
        $('#dari').val('').trigger('change');
        offcanvas.show();
    }

    function editTarif(){
        offcanvas.show();
    }

    function deleteTarif(){
        $.ajax({
            type: "DELETE",
            url: "{{ url('admin/tarifpelayaran') }}/"+tarif_id,
            data:{
                "_token": "{{ csrf_token() }}",
            },
            success: function (response) {
                $("#jqGrid").jqGrid('setGridParam', {
                        postData: {pelayaran_id:pelayaran_id }
                }).trigger('reloadGrid');
                alert(response)
                tb_pelayaran.ajax.reload();
            }
        });
    }
    </script>
@endsection
