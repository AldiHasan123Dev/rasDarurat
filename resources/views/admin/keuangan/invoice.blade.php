@extends('layouts.admin')
@section('style')
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css"> --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<style>
    table.dataTable tbody th, table.dataTable tbody td{
        padding: 0px 10px !important;
    }
    .select2.select2-container.select2-container--default{
        width: 100% !important;
    }
</style>
@endsection
@section('content')

    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <div class="d-flex" style="gap: 12px">
                    {{-- <a href="" id="print-invoice" class="btn btn-sm btn-success">Print</a> --}}
                    <button data-bs-toggle="modal" data-bs-target="#invoice-modal" class="btn btn-sm btn-success">Edit Tanggal</button>
                    <p>List Semua Invoice</p>
                    <b>INVOICE (selected): <span class="invoice"></span></b>
                </div>
                {{-- <form action="{{ route('invoice.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" id="file" onchange="submit()">
                </form> --}}
                <a href="" class="btn btn-sm btn-success" id="cetak-invoice"><i class="fas fa-print"></i> Cetak Invoice Ulang</a>
            </div>
            <div class="card-body">
                <div class="table-responsives">
                    {{-- <table class="table table-sm nowrap" id="table-order" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice Tipe</th>
                                <th>Order ID</th>
                                <th>Invoice</th>
                                <th>Tanggal Invoice</th>
                                <th>Group Job</th>
                                <th>Job ID</th>
                                <th>Pembayar</th>
                                <th>Tanggal Kirim Invoice</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table> --}}
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
                </div>
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
                    <input type="hidden" name="invoice_id" id="invoice_id">
                    <div class="col-12 mb-2">
                        <label for="created_at">Tanggal Invoice</label>
                        <input type="date" name="created_at" id="created_at" class="form-control">
                    </div>
                    <div class="col-12 mb-2">
                        <label for="tanggal_kirim">Tanggal Kirim</label>
                        <input type="date" name="tanggal_kirim" id="tanggal_kirim" class="form-control">
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

{{-- <script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script> --}}
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script>
    let data = [];
        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: data,
            colModel: [
                {search:true, name: 'order_id', label : 'order_id', hidden:true},
                {search:true, name: 'id', label : 'id', hidden:true},
                {search:true, name: 'invoice', label : 'Invoice'},
                {search:true, name: 'tanggal', label : 'Tanggal',sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'job', label : 'Group Job'},
                {search:true, name: 'no_job', label : 'ID Job'},
                {search:true, name: 'pembayar', label : 'Pembayar'},
                {search:true, name: 'tanggal_kirim', label : 'Tanggal Kirim',sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'total', label : 'Total'},
            ],
            autowidth: true,
            shrinkToFit: true,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100,250,500,1000],
            viewrecords: true,
            pager: "#jqGridPager",
            caption: "Order Job Pre Invoice",
            onCellSelect: function (rowId, iRow, iCol, e) {
                var id = $(this).jqGrid('getCell', rowId, 'order_id');
                $('#cetak-invoice').attr('href','{{ route('cetak.invoice') }}?order_id='+id);
                $('#cetak-cont-invoice').attr('href','{{ route('cetak.invoice.cont') }}?order_id='+id);
                // var order_id = $(this).jqGrid('getCell', rowId, 'order_id');
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
        $('#jqGrid').jqGrid('filterToolbar',{stringResult: true, searchOnEnter: false, defaultSearch: 'cn'});
        $('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
            search: false, // show search button on the toolbar
            add: false,
            edit: false,
            del: false,
            refresh: true
        });

        $("#jadwal_kapal_id-si").select2({
            dropdownParent: $('#exampleModal'),
        });
        $("#tujuan-si").select2({
            dropdownParent: $('#exampleModal'),
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

        function convertDate(dateString) {
            var dateArray = dateString.split('/');
            var year = dateArray[2];
            var month = dateArray[1];
            var day = dateArray[0];
            var newDate = year + '-' + month + '-' + day;
            return newDate;
        }

        $('#btn-update').click(function (e) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.transaksi.update') }}",
                data: {
                    id: $('#invoice_id').val(),
                    created_at: $('#created_at').val(),
                    tanggal_kirim: $('#tanggal_kirim').val(),
                },
                success: function (response) {
                    alert('Data berhasil di update!');
                    tableInvoice.ajax.reload();
                }
            });
        });

        function loadTable() {
            $('#jqGrid').jqGrid('clearGridData');
            $('#jqGrid').jqGrid('setGridParam', {data: data});
            $('#jqGrid').trigger('reloadGrid');
        }

        function getData(start) {
            $.ajax({
                type: "GET",
                url: "{{ url('api/get-transaksi') }}",
                data:{start:start,limit:250},
                success: function (response) {
                    $.each(response.data, function (idx, item) {
                        data.push(item)
                    });
                    loadTable();
                    if(response.start<response.count){
                        getData(response.start)
                    }else{
                        $('#loading').remove();
                    }
                }
            });
        }

        getData(0)

</script>
@endsection
