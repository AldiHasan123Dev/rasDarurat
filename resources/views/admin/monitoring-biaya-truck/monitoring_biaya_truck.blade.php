@extends('layouts.admin')

@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<style>
    .select2.select2-container.select2-container--default {
        width: 100% !important;
    }
    tr td {
        padding: 2px 10px;
    }
    .bg-light-dark {
        background-color: #5e5e5e9e !important;
    }
    .bg-purple {
        background-color: purple !important;
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="container mt-3">
    <div class="card-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsives">
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Legend/Keterangan --}}
    <div class="col-12 mt-3">
        <div class="card p-3 shadow">
            <p>Keterangan:</p>
            <table>
                <tr>
                    <td style="width: 30px"><div class="bg-light-dark" style="height: 10px; width:20px"></div></td>
                    <td>: Order JOB Kosong (Check No. Container dan Seal harus sama persis)</td>
                </tr>
                <tr>
                    <td><div class="bg-primary" style="height: 10px; width:20px"></div></td>
                    <td>: SJ Diterima FA (Belum Totalan Sopir)</td>
                </tr>
                <tr>
                    <td><div class="bg-warning" style="height: 10px; width:20px"></div></td>
                    <td>: Sudah Totalan Sopir (Belum Terbit Invoice)</td>
                </tr>
                <tr>
                    <td><div class="bg-danger" style="height: 10px; width:20px"></div></td>
                    <td>: Sudah Terbit Invoice</td>
                </tr>
                <tr>
                    <td><div class="bg-success" style="height: 10px; width:20px"></div></td>
                    <td>: Customer RAS Tipe R2 (Tanpa Invoice)</td>
                </tr>
                <tr>
                    <td><div class="bg-purple" style="height: 10px; width:20px"></div></td>
                    <td>: Trucking Vendor</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/jquery-serializeFields.js') }}"></script>
<script src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $("#jqGrid").jqGrid({
            url: '{{ route('jqgrid.ordertrucking') }}',
            mtype: 'GET',
            datatype: 'json',
            caption: "Data Order Trucking", // Caption tampil di atas grid
            colModel: [
                { name: 'id', label: 'ID', width: 50, sorttype: 'number', frozen: true },
                { name: 'tgl_muat', label: 'Tanggal Muat', width: 80, sorttype: 'date', datefmt: 'd/m/Y', frozen: true },
                { name: 'invoice', label: 'Invoice', width: 80 },
                { name: 'tgl_invoice', label: 'Tgl Invoice', width: 80 },
                { name: 'tgl_total', label: 'Tanggal Totalan', width: 80 },
                { name: 'customer', label: 'Customer', width: 100 },
                { name: 'trucking', label: 'Trucking', width: 100 },
                { name: 'pembayar', label: 'Pembayar', width: 100 },
                { name: 'is_seal', label: 'Is Seal', width: 50 },
                { name: 'job', label: 'Job', width: 80 },
                { name: 'sopir', label: 'Sopir', width: 100 },
                { name: 'nopol', label: 'Nopol', width: 100 },
                { name: 'container', label: 'Container', width: 100 },
                { name: 'seal', label: 'Seal', width: 100 },
                { name: 'dari_xpdc', label: 'Dari (XPDC)', width: 100 },
                { name: 'dari', label: 'Dari', width: 100 },
                { name: 'tujuan', label: 'Tujuan', width: 100 },
                { name: 'tipe', label: 'Tipe', width: 100 },
                { name: 'sj_kembali', label: 'SJ Kembali', width: 100 },
                { name: 'sj_kembali_fa', label: 'SJ Diterima FA', width: 100 },
                { name: 'borongan', label: 'Borongan', width: 100 },
                { name: 'total_sopir', label: 'Totalan Sopir', width: 100 },
                { name: 'tarif_vendor', label: 'Tarif vendor', width: 100 },
                { name: 'total_invoice', label: 'Inv', width: 100 },
                { name: 'margin', label: 'Margin', width: 100 },
                { name: 'keterangan', label: 'Keterangan', width: 450 },
                { name: 'keterangan_lain', label: 'Keterangan Lain', width: 450 },

                // Kolom tersembunyi (tetap dibutuhkan tapi tidak ditampilkan)
                { name: 'class', hidden: true },
                { name: 'is_vendor', hidden: true },
                { name: 'ambil_empty_tambak_langon', hidden: true },
                { name: 'ambil_empty_teluk_langon', hidden: true },
                { name: 'bongkar_full_teluk_langon', hidden: true },
                { name: 'order_id', hidden: true },
                { name: 'customer_id', hidden: true },
                { name: 'kendaraan_id', hidden: true },
                { name: 'sopir_id', hidden: true },
                { name: 'sangu_id', hidden: true },
                { name: 'date_sj_kembali', hidden: true },
                { name: 'date_sj_kembali_fa', hidden: true },
                { name: 'date_tgl_muat', hidden: true }
            ],
            autowidth: true,
            shrinkToFit: false,
            height: "auto",
            rowNum: 25,
            rowList: [10, 25, 50, 100],
            viewrecords: true,
            pager: "#jqGridPager",
            loadComplete: function () {
                $("#jqGrid").jqGrid('filterToolbar', {
                    searchOperators: false,
                    searchOnEnter: false,
                    defaultSearch: "cn"
                });
            },
            gridComplete: function () {
                $("#jqGrid").jqGrid('setFrozenColumns');
            }
        });
    });
</script>
@endsection
