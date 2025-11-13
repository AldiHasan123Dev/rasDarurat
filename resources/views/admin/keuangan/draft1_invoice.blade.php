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
                    <a href="" class="btn btn-sm btn-success" id="cetak-invoice" style="font-size: .7rem"><i class="fas fa-print"></i> Cetak Draft Invoice</a>
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
        url: '{{ route('draft.invoice.data1') }}',  // Pastikan URL sesuai
        mtype: 'GET',
        datatype: 'json',
        colModel: [
            {search: true, frozen: true, name: 'created_at', label: 'Tanggal'},
            {search:true, name: 'order_id', label : 'order_id', hidden:true},
            {search: true, frozen: true, name: 'marketing', label: 'Marketing'},
            {search: true, name: 'is_draft', label: 'Draft'},
            {search: true, frozen: true, name: 'cs', label: 'CS'},
            {search: true, frozen: true, name: 'job', label: 'Job'},
            // {search: true, name: 'invoice', label: 'Invoice'},
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
        onCellSelect: function (rowId, iRow, iCol, e) {
    var order_id = $(this).jqGrid('getCell', rowId, 'order_id');
    var draft = $(this).jqGrid('getCell', rowId, 'is_draft'); 

    // Simpan order_id dan status draft di tombol cetak
    $('#cetak-invoice')
        .attr('href', '{{ route('cetak.draft_invoice') }}?order_id=' + order_id)
        .data('is_draft', draft); 
},

// Tambahkan event listener untuk tombol cetak invoice

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
    $('#table-order tbody').on( 'click', 'tr', function () {
            var id =  tableInvoice.row( this ).data().id;
            var invoice =  tableInvoice.row( this ).data().invoice;
            var created_at =  tableInvoice.row( this ).data().created_at;
            var tanggal_kirim =  tableInvoice.row( this ).data().tanggal_kirim;
            var order_id =  tableInvoice.row( this ).data().order_id;
            var tipe_invoice =  tableInvoice.row( this ).data().tipe_invoice;
            $('.invoice').html(invoice);
            $('#invoice_id').val(id);
            $('#created_at').val(convertDate(created_at));
            $('#tanggal_kirim').val(convertDate(tanggal_kirim));
            if (tipe_invoice=='global') {
                $('#cetak-invoice').attr('href','{{ route('cetak.invoice') }}?order_id='+order_id);
            } else {
                $('#cetak-invoice').attr('href','{{ route('cetak.invoice.cont') }}?order_id='+order_id);
            }
        })

    // Aktifkan filterToolbar
     $('#jqGrid').jqGrid('filterToolbar');

$('#cetak-invoice').on('click', function (e) {
    var is_draft = $(this).data('is_draft');

    if (is_draft == 1) {
        alert('Job ini sudah dibuatkan draft invoice!'); // Tampilkan peringatan
        e.preventDefault(); // Mencegah tombol agar tidak membuka link
    }
});


    $('#jqGrid').jqGrid('setFrozenColumns');
});

    const rp = (num)=>{
        return num.toLocaleString('en-US');
    }
    </script>
@endsection
