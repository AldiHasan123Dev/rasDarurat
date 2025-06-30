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
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <div class="d-flex gap-2">
                    <button class="py-2 px-3 btn btn-success" type="button" onclick="getIds()"><i class="fas fa-excel"></i> Export Rekap</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsives" id="jtable">
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script>
    $(function(){
        topbar.show();
    });
</script>
<script>
    let data = [];
    let ids = [];
    var grid = $("#jqGrid").jqGrid({
        url: '{{ route('jqgrid.order') }}',
        mtype: 'GET',
        datatype: 'json',
        colModel: [
            {search:true, name: 'invoice', label : 'invoice', frozen:true, width:70},
            {search:true, name: 'job', label : 'job', frozen:true, width:70},
            {search:true, name: 'no', label : 'no', frozen:true, width:70},
            {search:true, name: 'created_at', label : 'Tanggal', frozen:true, width:70},
            {search:true, name: 'asuransi', label : 'asuransi', frozen:true, width:70},
            {search:true, name: 'pembayar', label : 'pembayar', frozen:true, width:70},
            {search:true, name: 'id', label : 'id', hidden:false},
            {search:true, name: 'class', label : 'class', hidden:true},
            {search:true, name: 'marketing', label : 'marketing'},
            {search:true, name: 'cs', label : 'cs'},
            {search:true, name: 'pengirim', label : 'pengirim'},
            {search:true, name: 'penerima', label : 'penerima'},
            {search:true, name: 'dari', label : 'dari'},
            {search:true, name: 'tujuan', label : 'tujuan'},
            {search:true, name: 'shipment', label : 'shipment'},
            {search:true, name: 'kondisi', label : 'kondisi'},
            {search:true, name: 'barang', label : 'barang'},
            {search:true, name: 'pelayaran', label : 'pelayaran'},
            {search:true, name: 'kapal', label : 'kapal'},
            {search:true, name: 'voyage', label : 'voyage'},
            {search:true, name: 'etd', label : 'etd',sorttype: 'date', datefmt:'d/m/y'},
              {search:true, name: 'eta', label : 'eta',sorttype: 'date', datefmt:'d/m/y'},
            
            {search:true, name: 'td', label : 'td',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'ba_kirim', label : 'ba_kirim',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'nopol', label : 'nopol'},
            {search:true, name: 'trucking', label : 'trucking'},
            {search:true, name: 'container', label : 'container'},
            {search:true, name: 'seal', label : 'seal'},
            {search:true, name: 'stuffing', label : 'stuffing'},
            {search:true, name: 'stuffing_type', label : 'stuffing_type'},
            {search:true, name: 'full', label : 'full'},
            {search:true, name: 'barang_diantar', label : 'barang_diantar'},
            {search:true, name: 'ba_diantar_sby', label : 'ba_diantar_sby',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'ba_kembali', label : 'ba_kembali',sorttype: 'date', datefmt:'d/m/y'},
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
        multiselect: true,
        onCellSelect: function (rowId, iRow, iCol, e) {
            // var id = $(this).jqGrid('getCell', rowId, 'id');
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
                }else{
                    topbar.hide();
                }
            }
        });
    }

    // getData(0)

    function getIds(){
        var rows = grid.getGridParam('selarrrow');
        console.log(rows);
    }

    </script>
@endsection
