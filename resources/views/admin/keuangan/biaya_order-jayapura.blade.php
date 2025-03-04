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
                    <button class="py-2 px-3 btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-order" id="btn-edit"><i class="fas fa-pencil"></i> Edit</button>
                </div>
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

    <div class="modal fade" id="modal-edit-order" tabindex="-1"  aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit <span class="nojob"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="iframe-order" style="width: 100%; height:340px"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script>

    $("#jqGrid").jqGrid({
        url: '{{ route('order_biaya.jqgrid') }}',
        mtype: 'GET',
        datatype: 'json',
        postData: { kota:  'jayapura' },
        colModel: [
            {search:true, name: 'job', label : 'Job', frozen:true, width:70},
            {search:true, name: 'no_job', label : 'ID Job', frozen:true, width:70},
            {search:true, name: 'pembayar', label : 'Pembayar', frozen:true, width:70},
            {search:true, name: 'penerima', label : 'Penerima', frozen:true, width:70},
            {search:true, name: 'tujuan', label : 'Tujuan', frozen:true, width:70},
            {search:true, name: 'id', label : 'ID', hidden:true},
            {search:true, name: 'shipment', label : 'Shipment'},
            {search:true, name: 'container', label : 'Container'},
            {search:true, name: 'seal', label : 'Seal'},
            {search:true, name: 'kapal', label : 'Kapal'},
            {search:true, name: 'voyage', label : 'Voy'},
            {search:true, name: 'tgl_dcf', label : 'Tanggal'},
            {search:true, name: 'nominal_do', label : 'Nominal DO & Lolo Meratus'},
            {search:true, name: 'nominal_cleaning', label : 'Nominal Cleaning'},
            {search:true, name: 'nominal_fee', label : 'Nominal Fee'},
            {search:true, name: 'tgl_opt', label : 'Tgl OPT'},
            {search:true, name: 'nominal_opt', label : 'Nominal OPT'},
            {search:true, name: 'tgl_truk', label : 'Tgl Truk'},
            {search:true, name: 'nominal_truk', label : 'Nominal Truk'},
            {search:true, name: 'tgl_kuli', label : 'Tgl Kuli'},
            {search:true, name: 'nominal_kuli', label : 'Nominal Kuli'},
            {search:true, name: 'tgl_jc', label : 'Tgl JC'},
            {search:true, name: 'nominal_jc', label : 'Nominal JC'},
            {search:true, name: 'kondisi', label : 'Kondisi'},
        ],
        autowidth: true,
        shrinkToFit: false,
        height: 250,
        oadonce: true,
        rowNum: 25,
        rowList:[10,25,50,100,250,500,1000],
        viewrecords: true,
        pager: "#jqGridPager",
        caption: "Monitoring Biaya Shipment Jayapura",
        onCellSelect: function (rowId, iRow, iCol, e) {
            var id = $(this).jqGrid('getCell', rowId, 'id');
            var order_id = $(this).jqGrid('getCell', rowId, 'no_job');
            $('.nojob').html(order_id);
            $('#iframe-order').attr('src',@json(url('admin/monitoring-shipment'))+'/'+id);
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

        var myModalEl = document.getElementById('modal-edit-order')
        myModalEl.addEventListener('hidden.bs.modal', function (event) {
            $("#jqGrid").trigger("reloadGrid");
        })
    </script>
@endsection
