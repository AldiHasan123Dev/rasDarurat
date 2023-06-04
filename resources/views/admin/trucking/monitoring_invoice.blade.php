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
    .bg-light-dark{
        background-color: #5e5e5e9e !important;
    }
</style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card-12">
            <div class="card">
                <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">

                </div>
                <div class="card-body">
                    <div class="table-responsives">
                        <table id="jqGrid"></table>
                        <div id="jqGridPager"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3">
            <div class="card p-3 shadow">
                <p>Keterangan:</p>
                <table>
                    <tr>
                        <td style="width: 30px"><div class="bg-light-dark" style="height: 10px; width:20px"></div></td>
                        <td>: Order JOB Kosong (Check No. Container dan Seal harus sama persis)</td>
                    </tr>
                    <tr>
                        <td style="width: 30px"><div class="bg-primary" style="height: 10px; width:20px"></div></td>
                        <td>: SJ Diterima FA (Belum Totalan Sopir)</td>
                    </tr>
                    <tr>
                        <td style="width: 30px"><div class="bg-warning" style="height: 10px; width:20px"></div></td>
                        <td>: Sudah Totalan Sopir (Belum Terbit Invoice)</td>
                    </tr>
                    <tr>
                        <td style="width: 30px"><div class="bg-danger" style="height: 10px; width:20px"></div></td>
                        <td>: Sudah Terbit Invoice</td>
                    </tr>
                    <tr>
                        <td style="width: 30px"><div class="bg-success" style="height: 10px; width:20px"></div></td>
                        <td>: Customer RAS Tipe R2 (Tanpa Invoice)</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script>
        var data = @json($orders);

        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: data,
            colModel: [
                {search:true, frozen:true, name: 'id', label : 'ID', sorttype: 'number', width:50},
                {search:true, frozen:true, name: 'tgl_muat', label : 'Tanggal Muat', sorttype: 'date', datefmt:'d/m/y', width:80},
                {search:true, frozen:true, name: 'invoice', label : 'Invoice', width:80},
                {search:true, frozen:true, name: 'tgl_invoice', label : 'Tgl Invoice', width:80},
                {search:true, frozen:true, name: 'customer', label : 'Customer', width:80},
                {search:true, frozen:true, name: 'trucking', label : 'Trucking', width:80},
                {search:false, name: 'class', label : 'class', hidden:true},
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
                {search:true, name: 'pembayar', label : 'Pembayar'},
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
                {search:true, name: 'lain_lain', label : 'Lain-lain'},
                {search:true, name: 'total_sopir', label : 'Totalan Sopir'},
                {search:true, name: 'tgl_total', label : 'Tanggal Totalan'},
                {search:true, name: 'tarif', label : 'Tarif'},
                {search:true, name: 'pph_21', label : 'PPh 21-3%'},
                {search:true, name: 'pph_23', label : 'PPh 23-2%'},
                {search:true, name: 'total_invoice', label : 'Inv'},
                {search:true, name: 'margin', label : 'Margin'},
                {search:true, name: 'keterangan', label : 'Keterangan', width:450},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100],
			viewrecords: true,
            pager: "#jqGridPager",
            caption: "Order Trucking (Belum Terbit Invoice)",
            onCellSelect: function (rowId, iRow, iCol, e) {
                id = $(this).jqGrid('getCell', rowId, 'id');
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
