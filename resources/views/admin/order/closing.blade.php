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
                <div class="d-flex" style="gap: 12px">
                    <b>N0. JOB (selected): <span class="nojob"></span></b>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsives">
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
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
    $('#bttb-info').hide();
    $('#ag').hide();
    let id = null;
    let row_id = null;

    let data = @json($data);
    $("#jqGrid").jqGrid({
        datatype: 'local',
        data: data,
        colModel: [
            {search:true, name: 'invoice', label : 'invoice', frozen:true, width:70},
            {search:true, name: 'job', label : 'job', frozen:true, width:70},
            {search:true, name: 'no', label : 'no', frozen:true, width:70},
            {search:true, name: 'asuransi', label : 'asuransi', frozen:true, width:70},
            {search:true, name: 'pembayar', label : 'pembayar', frozen:true, width:70},
            {search:true, name: 'id', label : 'id', hidden:true},
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
            {search:true, name: 'ba_kembali', label : 'ba_kembali',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'satuan', label : 'satuan'},
            {search:true, name: 'unit', label : 'unit'},
            {search:true, name: 'tarif', label : 'tarif'},
            {search:true, name: 'agen', label : 'agen'},
            {search:true, name: 'penerima_bl', label : 'penerima_bl'},
            {search:true, name: 'keterangan', label : 'keterangan'},
        ],
        autowidth: true,
        shrinkToFit: false,
        height: 250,
        oadonce: true,
        rowNum: 25,
        rowList:[10,25,50,100,250,500,1000],
        viewrecords: true,
        pager: "#jqGridPager",
        caption: "Order Job Belum TD Melebihi Closing",
        onCellSelect: function (rowId, iRow, iCol, e) {
            // row_id = rowId;
            // id = $(this).jqGrid('getCell', rowId, 'id');
            // var no = $(this).jqGrid('getCell', rowId, 'no');
            // $('#order_id_bttb').val(id);
            // $('#order_id').val(id);
            // $('.nojob').html(no);
            // $('#form-ba-kembali').attr('action','{{ url('admin/order') }}/'+id);
        },
        rowattr: function (item) {
            return { "class": item.class };
        }
    });

    $('#jqGrid').jqGrid('filterToolbar',{stringResult: true, searchOnEnter: false, defaultSearch: 'cn'});
    $('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
        search: false, // show search button on the toolbar
        add: false,
        edit: false,
        del: false,
        refresh: true
    });
    $("#jqGrid").jqGrid('setFrozenColumns');

</script>
@endsection
