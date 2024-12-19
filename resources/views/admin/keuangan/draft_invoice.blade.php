@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
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
                    <div class="mt-3">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script src="{{ asset('assets/js/resize-column.js') }}"></script>
<script>

    function load(){
        (function (window, ResizableTableColumns, undefined) {
            var store = window.store && window.store.enabled
                ? window.store
                : null;

            var els = document.querySelectorAll('table.data');
            for (var index = 0; index < els.length; index++) {
                var table = els[index];
                if (table['rtc_data_object']) {
                    continue;
                }

                var options = { store: store };
                if (table.querySelectorAll('thead > tr').length > 1) {
                    options.resizeFromBody = false;
                }

                new ResizableTableColumns(els[index], options);
            }

        })(window, window.validide_resizableTableColumns.ResizableTableColumns, void (0));
    }

    load();
</script>
<script>

    let data = [];
    let id;
    $(document).ready(function () {
    $("#jqGrid").jqGrid({
        url: '{{ route('draft.invoice.data') }}',  // Pastikan URL sesuai
        mtype: 'GET',
        datatype: 'json',
        colModel: [
            {search: true, frozen: true, name: 'created_at', label: 'Tanggal'},
            {search: true, frozen: true, name: 'marketing', label: 'Marketing'},
            {search: true, frozen: true, name: 'cs', label: 'CS'},
            {search: true, frozen: true, name: 'job', label: 'Job'},
            {search: true, name: 'invoice', label: 'Invoice'},
            {search: true, name: 'customer', label: 'Customer'},
            {search: true, name: 'barang', label: 'Barang'},
            {search: true, name: 'pengirim', label: 'Pengirim'},
            {search: true, name: 'penerima', label: 'Penerima'},
            {search: true, name: 'trucking', label: 'Trucking'},
            {search: true, name: 'seal', label: 'Seal'},
            {search: true, name: 'container', label: 'Container'},
            {search: true, name: 'nopol', label: 'Nopol'},
            {search: true, name: 'dari_lokasi', label: 'Dari Lokasi'},
            {search: true, name: 'kapal', label: 'Kapal'},
            {search: true, name: 'voyage', label: 'Voyage'},
            {search: true, name: 'shipment', label: 'Shipment'},
        ],
        autowidth: true,
        shrinkToFit: false,
        height: 250,
        rowNum: 20,
        rowList: [20, 50, 100, 250, 500, 1000],
        viewrecords: true,
        pager: "#jqGridPager",
        caption: "Draft Invoice",
        jsonReader: {
            root: "rows",  // Data rows diambil dari 'rows'
            page: "page",  // Halaman
            total: "total", // Total halaman
            records: "records", // Total record
            repeatitems: false,
        },
        loadComplete: function (data) {
            console.log("Data yang dimuat:", data);
        }
    });

    // Aktifkan filterToolbar
    $('#jqGrid').jqGrid('filterToolbar', {
    stringResult: true,  // Menggunakan string hasil filter
    searchOnEnter: false,  // Filter saat Enter ditekan
    defaultSearch: 'bw'  // Menentukan tipe pencarian (misalnya 'bw' = begins with)
});


    $('#jqGrid').jqGrid('setFrozenColumns');
});

    const rp = (num)=>{
        return num.toLocaleString('en-US');
    }
    </script>
@endsection
