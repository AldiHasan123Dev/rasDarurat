@extends('layouts.admin')
@section('style')
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<style>
    td, th {
        border: 1px solid #ccc;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card p-3">
                <div class="card-header py-2">
                    <div class="d-flex gap-5">
                        <a href="" id="cetak" class="btn btn-success"><i class="fas fa-dollar"></i> Mutasi</a>
                    </div>
                </div>
                <div class="table-responsives mt-3">
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <div class="card p-3">
                <div class="table-responsives mt-3">
                    <table id="jqGrid1"></table>
                    <div id="jqGridPager1"></div>
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
        var data = @json($data);
        var data1 = @json($data1);

        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: data,
            colModel: [
                {search:true, name: 'jurnal', label : 'jurnal'},
                {search:true, name: 'tgl_jurnal', label : 'Tanggal jurnal', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'tgl_invoice', label : 'Tanggal Invoice', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'invoice', label : 'Invoice'},
                {search:true, name: 'sopir', label : 'Sopir',},
                {search:true, name: 'container', label : 'Container', width:500},
                {search:true, name: 'total', label : 'Total',},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100],
			viewrecords: true,
            pager: "#jqGridPager",
            caption: "Invoice Sopir",
            onCellSelect: function (rowId, iRow, iCol, e) {
                var invoice = $(this).jqGrid('getCell', rowId, 'invoice');
                $('#cetak').attr('href',@json(url('admin/trucking/totalan-sopir/invoice'))+'?invoice='+invoice+'&mutasi=1');
            },
        });

        $('#jqGrid').jqGrid('filterToolbar',{stringResult: true, searchOnEnter: false, defaultSearch: 'cn'});
			$('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
                search: false, // show search button on the toolbar
                add: false,
                edit: false,
                del: false,
                refresh: true
            });


        $("#jqGrid1").jqGrid({
            datatype: 'local',
            data: data1,
            colModel: [
                {search:true, name: 'jurnal', label : 'jurnal'},
                {search:true, name: 'tgl_jurnal', label : 'Tanggal jurnal', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'tgl_invoice', label : 'Tanggal Invoice', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'invoice', label : 'Invoice'},
                {search:true, name: 'sopir', label : 'Sopir',},
                {search:true, name: 'container', label : 'Container', width:500},
                {search:true, name: 'total', label : 'Total',},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100],
			viewrecords: true,
            pager: "#jqGridPager1",
            caption: "Invoice Sopir",
            onCellSelect: function (rowId, iRow, iCol, e) {
                var invoice = $(this).jqGrid('getCell', rowId, 'invoice');
                $('#cetak').attr('href',@json(url('admin/trucking/totalan-sopir/invoice'))+'?invoice='+invoice);
            },
        });

        $('#jqGrid1').jqGrid('filterToolbar',{stringResult: true, searchOnEnter: false, defaultSearch: 'cn'});
			$('#jqGrid1').jqGrid('navGrid',"#jqGridPager1", {
                search: false, // show search button on the toolbar
                add: false,
                edit: false,
                del: false,
                refresh: true
            });


    </script>
@endsection
