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
            url: '{{ route('jqgrid.ordertrucking') }}',
            mtype: 'GET',
            datatype: 'json',
            colModel: [
                {search:true, frozen:true, name: 'id', label : 'ID', sorttype: 'number', width:50},
                {search:true, frozen:true, name: 'jurnal_piutang', label : 'Jurnal Otomatis', width:90},
                {search:true, frozen:true, name: 'tgl_muat', label : 'Tanggal Muat', sorttype: 'date', datefmt:'d/m/y', width:80},
                {search:true, frozen:true, name: 'invoice', label : 'Invoice', width:80},
                {search:true, frozen:true, name: 'tgl_invoice', label : 'Tgl Invoice', width:80},
                {search:true, frozen:true, name: 'tgl_total', label : 'Tanggal Totalan', width:80},
                {search:true, frozen:true, name: 'customer', label : 'Customer', width:80},
                {search:true, frozen:true, name: 'trucking', label : 'Trucking', width:80},
                {search:true, frozen:true, name: 'pembayar', label : 'Pembayar', width:80},
                {search:false, name: 'class', label : 'class', hidden:true},
                {search:false, name:'is_vendor', label:'#', hidden:true},
                {search:false, name:'ambil_empty_tambak_langon', label:'#', hidden:true},
                {search:false, name:'ambil_empty_teluk_langon', label:'#', hidden:true},
                {search:false, name:'bongkar_full_teluk_langon', label:'#', hidden:true},
                {search:false, name: 'order_id', label : 'order_id', hidden:true},
                {search:false, name: 'customer_id', label : 'customer_id', hidden:true},
                {search:false, name: 'kendaraan_id', label : 'kendaraan_id', hidden:true},
                {search:false, name: 'sopir_id', label : 'sopir_id', hidden:true},
                {search:false, name: 'sangu_id', label : 'sangu_id', hidden:true},
                {search:false, name: 'date_sj_kembali', label : 'SJ Kembali D', hidden:true},
                {search:false, name: 'date_sj_kembali_fa', label : 'SJ Diterima FA D', hidden:true},
                {search:false, name: 'date_tgl_muat', label : 'Tanggal Muat D', hidden:true},
                // {search:true, name: 'tanggal', label : 'Tanggal', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'job', label : 'Job'},
                {search:true, name: 'sopir', label : 'Sopir'},
                {search:true, name: 'nopol', label : 'Nopol'},
                {search:true, name: 'container', label : 'Container'},
                {search:true, name: 'seal', label : 'Seal'},
                {search:true, name: 'dari', label : 'Dari'},
                {search:true, name: 'tujuan', label : 'Tujuan'},
                {search:true, name: 'tipe', label : 'Tipe'},
                {search:true, name: 'sj_kembali', label : 'SJ Kembali'},
                {search:true, name: 'sj_kembali_fa', label : 'SJ Diterima FA'},
                {search:true, name: 'borongan', label : 'Borongan'},
                {search:true, name: 'sangu', label : 'Sangu Sopir'},
                {search:true, name: 'simpanan', label : 'Simpanan Sopir'},
                {search:true, name: 'borongan_kuli', label : 'Borongan Kuli'},
                {search:true, name: 'kuli', label : 'Sangu Kuli'},
                {search:true, name: 'simpanan_kuli', label : 'Simpanan Kuli'},
                {search:true, name: 'tambah_isi', label : 'Tambah Isi'},
                {search:true, name: 'tambah_solar', label : 'Tambah Solar'},
                {search:true, name: 'tb_tl', label : 'TB/TL'},
                {search:true, name: 'tally', label : 'Tally'},
                {search:true, name: 'uang_makan', label : 'Uang Makan'},
                {search:true, name: 'op', label : 'OP/naikkan Mty'},
                {search:true, name: 'cleaning', label : 'Cleaning'},
                {search:true, name: 'stappel', label : 'Stappel/Inap'},
                {search:true, name: 'lain_lain', label : 'Lain-lain Totalan'},
                {search:true, name: 'lain', label : 'Lain-lain'},
                {search:true, name: 'total_sopir', label : 'Totalan Sopir'},
                {search:true, name: 'tarif_vendor', label : 'Tarif vendor'},
                {search:true, name: 'tarif', label : 'Tarif'},
                {search:true, name: 'add_cost', label : 'Add Cost'},
                {search:true, name: 'pph_21', label : 'PPh 21-3%'},
                {search:true, name: 'pph_23', label : 'PPh 23-2%'},
                {search:true, name: 'total_invoice', label : 'Inv'},
                {search:true, name: 'margin', label : 'Margin'},
                {search:true, name: 'keterangan', label : 'Keterangan', width:450},
                {search:true, name: 'keterangan_lain', label : 'Keterangan Lain', width:450},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100],
			viewrecords: true,
            pager: "#jqGridPager",
            caption: "Order Trucking",
            onCellSelect: function (rowId, iRow, iCol, e) {
                id = $(this).jqGrid('getCell', rowId, 'id');
                let nomor = $(this).jqGrid('getCell', rowId, 'jurnal_piutang');
                $.ajax({
                type: "POST",
                url: "{{ url('api/get-jurnal') }}",
                data:{
                    nomor:nomor
                },
                success: function (response) {
                    let html = '';
                    $.each(response, function (idx, item) {
                        html += `<tr>
                                <td>${item.created_at}</td>
                                <td>${item.nomor}</td>
                                <td>${item.coa.kode}</td>
                                <td>${item.coa.nama}</td>
                                <td>${item.order_trucking.invoice ?? '-'}</td>
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



    $("#jqGrid1").jqGrid({
            url: '{{ route('jqgrid.ordertrucking') }}',
            mtype: 'GET',
            datatype: 'json',
            postData: { invNull:  true },
            colModel: [
                {search:true, frozen:true, name: 'id', label : 'ID', sorttype: 'number', width:50},
                {search:true, frozen:true, name: 'jurnal_piutang', label : 'Jurnal Otomatis', width:90},
                {search:true, frozen:true, name: 'tgl_muat', label : 'Tanggal Muat', sorttype: 'date', datefmt:'d/m/y', width:80},
                {search:true, frozen:true, name: 'invoice', label : 'Invoice', width:80},
                {search:true, frozen:true, name: 'tgl_invoice', label : 'Tgl Invoice', width:80},
                {search:true, frozen:true, name: 'tgl_total', label : 'Tanggal Totalan', width:80},
                {search:true, frozen:true, name: 'customer', label : 'Customer', width:80},
                {search:true, frozen:true, name: 'trucking', label : 'Trucking', width:80},
                {search:true, frozen:true, name: 'pembayar', label : 'Pembayar', width:80},
                {search:false, name: 'class', label : 'class', hidden:true},
                {search:false, name:'is_vendor', label:'#', hidden:true},
                {search:false, name:'ambil_empty_tambak_langon', label:'#', hidden:true},
                {search:false, name:'ambil_empty_teluk_langon', label:'#', hidden:true},
                {search:false, name:'bongkar_full_teluk_langon', label:'#', hidden:true},
                {search:false, name: 'order_id', label : 'order_id', hidden:true},
                {search:false, name: 'customer_id', label : 'customer_id', hidden:true},
                {search:false, name: 'kendaraan_id', label : 'kendaraan_id', hidden:true},
                {search:false, name: 'sopir_id', label : 'sopir_id', hidden:true},
                {search:false, name: 'sangu_id', label : 'sangu_id', hidden:true},
                {search:false, name: 'date_sj_kembali', label : 'SJ Kembali D', hidden:true},
                {search:false, name: 'date_sj_kembali_fa', label : 'SJ Diterima FA D', hidden:true},
                {search:false, name: 'date_tgl_muat', label : 'Tanggal Muat D', hidden:true},
                // {search:true, name: 'tanggal', label : 'Tanggal', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'job', label : 'Job'},
                {search:true, name: 'sopir', label : 'Sopir'},
                {search:true, name: 'nopol', label : 'Nopol'},
                {search:true, name: 'container', label : 'Container'},
                {search:true, name: 'seal', label : 'Seal'},
                {search:true, name: 'dari', label : 'Dari'},
                {search:true, name: 'tujuan', label : 'Tujuan'},
                {search:true, name: 'tipe', label : 'Tipe'},
                {search:true, name: 'sj_kembali', label : 'SJ Kembali'},
                {search:true, name: 'sj_kembali_fa', label : 'SJ Diterima FA'},
                {search:true, name: 'borongan', label : 'Borongan'},
                {search:true, name: 'sangu', label : 'Sangu Sopir'},
                {search:true, name: 'simpanan', label : 'Simpanan Sopir'},
                {search:true, name: 'borongan_kuli', label : 'Borongan Kuli'},
                {search:true, name: 'kuli', label : 'Sangu Kuli'},
                {search:true, name: 'simpanan_kuli', label : 'Simpanan Kuli'},
                {search:true, name: 'tambah_isi', label : 'Tambah Isi'},
                {search:true, name: 'tambah_solar', label : 'Tambah Solar'},
                {search:true, name: 'tb_tl', label : 'TB/TL'},
                {search:true, name: 'tally', label : 'Tally'},
                {search:true, name: 'uang_makan', label : 'Uang Makan'},
                {search:true, name: 'op', label : 'OP/naikkan Mty'},
                {search:true, name: 'cleaning', label : 'Cleaning'},
                {search:true, name: 'stappel', label : 'Stappel/Inap'},
                {search:true, name: 'lain_lain', label : 'Lain-lain Totalan'},
                {search:true, name: 'lain', label : 'Lain-lain'},
                {search:true, name: 'total_sopir', label : 'Totalan Sopir'},
                {search:true, name: 'tarif_vendor', label : 'Tarif vendor'},
                {search:true, name: 'tarif', label : 'Tarif'},
                {search:true, name: 'add_cost', label : 'Add Cost'},
                {search:true, name: 'pph_21', label : 'PPh 21-3%'},
                {search:true, name: 'pph_23', label : 'PPh 23-2%'},
                {search:true, name: 'total_invoice', label : 'Inv'},
                {search:true, name: 'margin', label : 'Margin'},
                {search:true, name: 'keterangan', label : 'Keterangan', width:450},
                {search:true, name: 'keterangan_lain', label : 'Keterangan Lain', width:450},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100],
			viewrecords: true,
            pager: "#jqGridPager1",
            caption: "Order Trucking belum invoice nopol R1",
            onCellSelect: function (rowId, iRow, iCol, e) {
                id = $(this).jqGrid('getCell', rowId, 'id');
                let nomor = $(this).jqGrid('getCell', rowId, 'jurnal_piutang');
            },
            rowattr: function (item) {
                return { "class": item.class };
            }
        });

    $('#jqGrid1').jqGrid('filterToolbar');
    $('#jqGrid1').jqGrid('navGrid',"#jqGridPager1", {
        search: false, // show search button on the toolbar
        add: false,
        edit: false,
        del: false,
        refresh: true
    });

    $("#jqGrid1").jqGrid('setFrozenColumns');
    </script>
@endsection
