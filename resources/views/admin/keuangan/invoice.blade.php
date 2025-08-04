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
                <div class="d-flex gap-2">
                    <input type="date" name="start_date" id="start_date" value="{{ $start_date }}">
                    <input type="date" name="end_date" id="end_date" value="{{ $end_date }}">
                    <button class="btn btn-sm btn-primary" id="filter">Filter</button>
                    <form action="{{ route('order.rekap_invoice') }}" method="post">
                        @csrf
                        <div class="btn-group">
                            <button class="btn btn-info btn-sm" type="submit" name="invoice" id="invoice">Rekap Invoice Excel</button>
                            <a href="" class="btn btn-sm btn-success" id="cetak-invoice" style="font-size: .7rem"><i class="fas fa-print"></i> Cetak Invoice Ulang</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <pre id="loading">Loading...</pre>
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
                {search:true, name: 'tipe_invoice', label : 'tipe_invoice', hidden:true},
                {search:true, name: 'order_id', label : 'order_id', hidden:true},
                {search:true, name: 'no', label : 'no', hidden:true},
                {search:true, name: 'id', label : 'id', hidden:true},
                {search:true, name: 'tanggal_format', label : 'Tanggal', hidden:true},
                {search:true, name: 'tanggal_kirim_format', label : 'Tanggal', hidden:true},
                {search:true, name: 'jurnal_piutang', label : 'No. Jurnal'},
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
            caption: "Order Job Invoice",
            onCellSelect: function (rowId, iRow, iCol, e) {
                var id = $(this).jqGrid('getCell', rowId, 'id');
                var invoice = $(this).jqGrid('getCell', rowId, 'invoice');
                var order_id = $(this).jqGrid('getCell', rowId, 'order_id');
                var tipe_invoice = $(this).jqGrid('getCell', rowId, 'tipe_invoice');
                var tanggal = $(this).jqGrid('getCell', rowId, 'tanggal_format');
                var tanggal_kirim = $(this).jqGrid('getCell', rowId, 'tanggal_kirim_format');
                if (tipe_invoice=='cont') {
                    $('#cetak-invoice').attr('href','{{ route('cetak.invoice.cont') }}?order_id='+order_id);
                } else {
                    $('#cetak-invoice').attr('href','{{ route('cetak.invoice') }}?order_id='+order_id);
                }
                $('#created_at').val(tanggal);
                $('#tanggal_kirim').val(tanggal_kirim);
                $('#invoice_id').val(id);
                $('#invoice').val(invoice);
                let jurnal_piutang = $(this).jqGrid('getCell', rowId, 'jurnal_piutang');
                let no = $(this).jqGrid('getCell', rowId, 'no');
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
    e.preventDefault(); // hindari submit default jika tombol dalam form

    // Disable tombol dan ubah teksnya
    const $btn = $(this);
    $btn.prop('disabled', true).text('Menyimpan...');

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
            location.reload();
        },
        error: function (xhr) {
            alert('Terjadi kesalahan. Silakan coba lagi.');
            // Enable kembali tombol jika gagal
            $btn.prop('disabled', false).text('Update');
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
                data:{
                    start:start,
                    limit:250,
                    start_date:$('#start_date').val(),
                    end_date:$('#end_date').val(),
                },
                success: function (response) {
                    $.each(response.data, function (idx, item) {
                        data.push(item)
                    });
                    loadTable();
                    if(response.start<response.count){
                        getData(response.start)
                    }else{
                        $('#loading').hide();
                    }
                }
            });
        }

        $('#filter').click(function (e) {
            e.preventDefault();
             $('#loading').show();
            data = [];
            getData(0);
        });

        const rp = (num)=>{
            return num.toLocaleString('en-US');
        }

        getData(0)

</script>
@endsection
