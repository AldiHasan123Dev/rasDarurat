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
                {{-- <div class="card-header">
                    <div class="d-flex gap-5">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#faktur">Tambah Faktur</button>
                        <form action="{{ route('keuangan.ppn.export') }}" method="post">
                            @csrf
                            <input type="hidden" name="start" value="{{ $start }}">
                            <input type="hidden" name="end" value="{{ $end }}">
                            <button type="submit" class="btn btn-success btn-sm">Export Excel</button>
                        </form>
                        <form method="get" action="{{ url()->current() }}" class="d-flex gap-3">
                            <div class="btn-group">
                                <input type="date" name="start" id="start" value="{{ $start }}" class="form-control">
                                <button disabled style="width: 70px" style="border:none; outline:none;"><i class="fas fa-arrow-right"></i></button>
                                <input type="date" name="end" id="end" value="{{ $end }}" class="form-control">
                                <button class="btn btn-sm btn-primary">Filter</button>
                            </div>
                        </form>
                    </div>
                </div> --}}
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
            caption: "Invoice Trucking"
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
    </script>
@endsection
