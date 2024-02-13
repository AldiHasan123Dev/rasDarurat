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
                        <a href="" id="cetak" class="btn btn-success"><i class="fas fa-print"></i> Cetak ulang</a>
                        <button data-bs-toggle="modal" data-bs-target="#invoice-modal" class="btn btn-sm btn-primary">Edit Tanggal Invoice</button>
                    </div>
                </div>
                <div class="table-responsives mt-3">
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
                </div>
                {{-- <div class="card-footer py-2">
                    <div class="d-flex gap-3 mt-2 justify-content-center">
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total Sub Total</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format($sub_total) }}</li>
                        </ul>
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total PPN</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format(round($ppn)) }}</li>
                        </ul>
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format($sub_total+ round($ppn)) }}</li>
                        </ul>
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total PPH</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format(round($pph)) }}</li>
                        </ul>

                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="invoice-modal" tabindex="-1" aria-labelledby="customerLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit <span class="invoice"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="transaksi_id" id="transaksi_id">
                    <div class="col-12 mb-2">
                        <label for="created_at">Tanggal Invoice</label>
                        <input type="date" name="created_at" id="created_at" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-update">Simpan</button>
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

        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: data,
            colModel: [
                {search:true, name: 'id', label : 'id', hidden:true},
                {search:true, name: 'jurnal_piutang', label : 'Piutang Trucking'},
                {search:true, name: 'jurnal_hutang', label : 'Hutang Trucking'},
                {search:true, name: 'tgl_invoice', label : 'Tanggal Invoice', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'invoice', label : 'Invoice'},
                {search:true, name: 'customer', label : 'Customer',},
                {search:true, name: 'rit', label : 'Rit',},
                {search:true, name: 'lain_lain', label : 'Biaya lain-lain',},
                {search:true, name: 'pph', label : 'PPH',},
                {search:true, name: 'total', label : 'Total',},
                {search:true, name: 'total_pph', label : 'Total - PPH',},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100],
			viewrecords: true,
            pager: "#jqGridPager",
            caption: "Invoice Trucking",
            onCellSelect: function (rowId, iRow, iCol, e) {
                var id = $(this).jqGrid('getCell', rowId, 'id');
                var invoice = $(this).jqGrid('getCell', rowId, 'invoice');
                $('.invoice').html(invoice);
                $('#transaksi_id').val(id);
                $('#cetak').attr('href',@json(url('admin/trucking/cetak-invoice/get'))+'?invoice='+invoice);
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

        $('#sub_total').keyup(function (e) {
            hitung();
        });

        $('#ppn').keyup(function (e) {
            hitung();
        });

        function hitung (){
            var sub_total = $('#sub_total').val().replace(/\./g, "");
            var ppn = $('#ppn').val().replace(/\./g, "");
            var total = parseInt(sub_total) + parseInt(ppn);
            $('#total').val(total.toLocaleString('en-US'));
        }

        $('#create-nsfp').click(function (e) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.nsfp.store') }}",
                data: {
                    nsfp:$('#nsfp').val(),
                    invoice:$('#invoice').val(),
                    pembayar_id:$('#pembayar_id').val(),
                    tujuan:$('#tujuan').val(),
                    keterangan:$('#keterangan').val(),
                    sub_total:$('#sub_total').val(),
                    ppn:$('#ppn').val(),
                    total:$('#total').val(),
                    pph:$('#pph').val(),
                },
                success: function (response) {
                    if(!response){
                        alert('Pembayar Tidak Ditemukan')
                    }else{
                        location.reload();
                    };
                }
            });
        });

        $('#btn-update').click(function (e) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.transaksi-trucking.update') }}",
                data: {
                    transaksi_id:$('#transaksi_id').val(),
                    created_at:$('#created_at').val(),
                },
                success: function (response) {
                    alert('Update berhasil!');
                    location.reload();
                }
            });
        });
    </script>
@endsection
