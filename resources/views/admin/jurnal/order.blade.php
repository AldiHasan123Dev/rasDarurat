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
                        <div class="table-responsive">
                            <table data-rtc-resizable-table="table.1" class="data table table-sm mt-3 table-bordered" style="font-size: .7rem; white-space:nowrap">
                                <thead>
                                    <tr>
                                        <th data-rtc-resizable="tanggal">Tanggal</th>
                                        <th data-rtc-resizable="nomor">Nomor</th>
                                        <th data-rtc-resizable="akun">No. Akun</th>
                                        <th data-rtc-resizable="akun_name">Nama Akun</th>
                                        <th data-rtc-resizable="invoice">Invoice</th>
                                        <th data-rtc-resizable="job">JOB</th>
                                        <th data-rtc-resizable="keterangan">Keterangan</th>
                                        <th data-rtc-resizable="debit">Debit</th>
                                        <th data-rtc-resizable="credit">Credit</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
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

    $("#jqGrid").jqGrid({
        url: '{{ route('jqgrid.order') }}',
        mtype: 'GET',
        datatype: 'json',
        colModel: [
            {search:true, frozen:true, name: 'jurnal_piutang', label : 'Jurnal Otomatis'},
            {search:true, frozen:true, name: 'invoice', label : 'invoice'},
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
            {search:true, name: 'kapal', label : 'kapal'},
            {search:true, name: 'voyage', label : 'voyage'},
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
            let no = $(this).jqGrid('getCell', rowId, 'no');
            let jurnal_piutang = $(this).jqGrid('getCell', rowId, 'jurnal_piutang');
            $.ajax({
                type: "POST",
                url: "{{ url('api/get-jurnal') }}",
                data:{
                    nomor:jurnal_piutang,
                },
                success: function (response) {
                    let html = '';
                    $.each(response, function (idx, item) {
                        html += `<tr>
                                <td>${item.created_at}</td>
                                <td>${item.nomor}</td>
                                <td>${item.coa.kode}</td>
                                <td>${item.coa.nama}</td>
                                <td>${item.order.invoice ?? '-'}</td>
                                <td>${no}</td>
                                <td>${item.nama}</td>
                                <td>${rp(item.debit)}</td>
                                <td>${rp(item.credit)}</td>
                            </tr>`;
                    });
                    $('#tbody').html(html);
                }
            });
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

    const rp = (num)=>{
        return num.toLocaleString('en-US');
    }
    </script>
@endsection
