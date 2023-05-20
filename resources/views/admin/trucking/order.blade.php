@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<style>
    .select2.select2-container.select2-container--default{
        width: 100% !important;
    }
    tr td{
        padding: 2px 10px;
    }
</style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                    {{-- <div class="d-flex gap-2">
                        <button class="py-2 px-3 btn btn-success" data-bs-toggle="modal" data-bs-target="#order"><i class="fas fa-plus"></i> Tambah Order Trucking</button>
                        <button class="py-2 px-3 btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit" id="btn-edit"><i class="fas fa-pencil"></i> Edit</button>
                    </div> --}}
                </div>
                <div class="card-body">
                    {{-- <div class="d-flex justify-content-center">
                        <img src="{{ asset('assets/img/loading.gif') }}" alt="Loading" class="img-fluid" id="loading" style="height:300px">
                    </div> --}}
                    <div class="table-responsives">
                        <table id="jqGrid"></table>
                        <div id="jqGridPager"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header" style="gap:10px" id="bttb-info">
                        <div class="d-flex justify-content-between">
                            <b>N0. JOB (selected): <b class="nojob"></b></b>
                            <b><b class="koli"></b> Koli</b>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm nowrap" id="table-bttb" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>No.</th>
                                        <th>Tanggal</th>
                                        <th>No. Gudang</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>P</th>
                                        <th>L</th>
                                        <th>T</th>
                                        <th>Vol</th>
                                        <th>Berat</th>
                                        <th>Tgl Masuk</th>
                                        <th>Pengirim</th>
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

@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script>

    let data = [];
    let id;

    $("#jqGrid").jqGrid({
        url: '{{ route('jqgrid.order') }}',
        mtype: 'GET',
        datatype: 'json',
        colModel: [
            // {search:true, frozen:true, name: 'invoice', label : 'invoice'},
            {search:true, frozen:true, name: 'job', label : 'job'},
            {search:true, frozen:true, name: 'no', label : 'no'},
            // {search:true, frozen:true, name: 'asuransi', label : 'asuransi'},
            {search:true, frozen:true, name: 'pembayar', label : 'pembayar'},
            {search:true, name: 'id', label : 'id', hidden:true},
            {search:true, name: 'class', label : 'class', hidden:true},
            {search:true, name: 'marketing', label : 'marketing'},
            {search:true, name: 'cs', label : 'cs'},
            {search:true, name: 'pengirim', label : 'pengirim'},
            {search:true, name: 'penerima', label : 'penerima'},
            {search:true, name: 'dari', label : 'dari'},
            {search:true, name: 'tujuan', label : 'tujuan'},
            {search:true, name: 'shipment', label : 'shipment'},
            // {search:true, name: 'kondisi', label : 'kondisi'},
            {search:true, name: 'barang', label : 'barang'},
            {search:true, name: 'pelayaran', label : 'pelayaran'},
            // {search:true, name: 'kapal', label : 'kapal'},
            // {search:true, name: 'voyage', label : 'voyage'},
            // {search:true, name: 'etd', label : 'etd',sorttype: 'date', datefmt:'d/m/y'},
            // {search:true, name: 'td', label : 'td',sorttype: 'date', datefmt:'d/m/y'},
            // {search:true, name: 'ba_kirim', label : 'ba_kirim',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'nopol', label : 'nopol'},
            {search:true, name: 'trucking', label : 'trucking'},
            {search:true, name: 'container', label : 'container'},
            {search:true, name: 'seal', label : 'seal'},
            {search:true, name: 'stuffing', label : 'stuffing'},
            {search:true, name: 'stuffing_type', label : 'stuffing_type'},
            {search:true, name: 'full', label : 'full'},
            // {search:true, name: 'barang_diantar', label : 'barang_diantar'},
            // {search:true, name: 'ba_kembali', label : 'ba_kembali',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'koli', label : 'koli'},
            {search:true, name: 'satuan', label : 'satuan'},
            {search:true, name: 'unit', label : 'unit'},
            {search:true, name: 'tarif', label : 'tarif'},
            {search:true, name: 'komisi', label : 'Fee Cust'},
            {search:true, name: 'agen', label : 'agen'},
            {search:true, name: 'penerima_bl', label : 'penerima_bl'},
            {search:true, name: 'keterangan', label : 'keterangan'},
            {search:true, name: 'add_cost', label : 'Add Cost'},
        ],
        autowidth: true,
        shrinkToFit: false,
        height: 250,
        oadonce: true,
        rowNum: 25,
        rowList:[10,25,50,100,250,500,1000],
        viewrecords: true,
        pager: "#jqGridPager",
        caption: "Order Job (read only)",
        onCellSelect: function (rowId, iRow, iCol, e) {
            id = $(this).jqGrid('getCell', rowId, 'id');
            var koli = $(this).jqGrid('getCell', rowId, 'koli');
            $('.koli').html(koli);
            tablebttb.ajax.reload();
            // var order_id = $(this).jqGrid('getCell', rowId, 'order_id');
            // var sangu = $(this).jqGrid('getCell', rowId, 'sangu');
            // var simpanan = $(this).jqGrid('getCell', rowId, 'simpanan');
            // var nopol = $(this).jqGrid('getCell', rowId, 'nopol');
            // $('#edit-form').attr('action','{{ url('admin/ordertrucking') }}/'+id);
            // getOrder(nopol,order_id);
            // $('#sangu').val(sangu);
            // $('#simpanan').val(simpanan);
            // $('#btn-edit').show();
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


    let tablebttb = $('#table-bttb').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('bttb.data') }}',
                method:'POST',
                data:function( d) {
                    d.order_id = id;
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id', visible:false },
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                { data: 'created_at', name: 'created_at' },
                { data: 'no_gudang', name: 'no_gudang' },
                { data: 'barang_id', name: 'barang_id' },
                { data: 'qty', name: 'qty' },
                { data: 'satuan_id', name: 'satuan_id' },
                { data: 'p', name: 'p' },
                { data: 'l', name: 'l' },
                { data: 't', name: 't' },
                { data: 'vol', name: 'vol' },
                { data: 'berat', name: 'berat' },
                { data: 'tgl_masuk', name: 'tgl_masuk' },
                { data: 'pengirim_id', name: 'pengirim_id' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'action', name: 'action', orderable: false, searchable: false, visible:false },
            ],
            select:true
        });


    function loadTable() {
        $('#jqGrid').jqGrid('clearGridData');
        $('#jqGrid').jqGrid('setGridParam', {data: data});
        $('#jqGrid').trigger('reloadGrid');
    }

    function getData(start) {
        $.ajax({
            type: "GET",
            url: "{{ url('api/get-order') }}",
            data:{start:start,limit:50},
            success: function (response) {
                $.each(response.data, function (idx, item) {
                    data.push(item)
                });
                loadTable();
                if(response.start<response.count){
                    getData(response.start)
                }
            }
        });
    }

    // getData(0)

    </script>
@endsection
