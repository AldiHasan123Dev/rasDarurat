@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <style>
        td:hover {
            cursor: pointer;
        }
        table.dataTable tbody th, table.dataTable tbody td{
            padding: 0px 10px !important;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasInvBayar" aria-controls="offcanvasInvBayar">Input Tanggal Inv Dibayar</button>
            </div>
            <div class="card-body">
                <div class="table-responsives" id="jtable">
                    <table id="jqGrid1"></table>
                    <div id="jqGridPager1"></div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasKomisi" aria-controls="offcanvasKomisi">Input Tanggal Komisi</button>
            </div>
            <div class="card-body">
                <div class="table-responsives" id="jtable">
                    <table id="jqGrid2"></table>
                    <div id="jqGridPager2"></div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <span>List Sudah Terbit Tanggal, Belum Transfer</span>
                <div class="d-flex gap-1">
                    <form action="{{ route('keuangan.fee_cust.bayar') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Apa anda yakin? order yang ada dilist akan ditandai sebagai sudah dibayar pd tgl hari ini {{ date('d/m/Y') }}')">Tandai Sudah dibayar</button>
                    </form>
                    <button class="btn btn-sm btn-success" id="export">Export to PDF</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsives" id="jtable">
                    <table id="jqGrid3"></table>
                    <div id="jqGridPager3"></div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="m-3">
                <span>List Sudah Transfer</span>
                <hr>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm no-wrap nowrap" id="table-payment" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID JOB</th>
                                <th>Pembayar</th>
                                <th>Pelayaran</th>
                                <th>Shippment</th>
                                <th>No Cont</th>
                                <th>Seal</th>
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>Fee</th>
                                <th>Tgl diberikan</th>
                                <th>Tgl inv dibayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->whereNotNull('tgl_komisi')->whereNotNull('komisi_print') as $item)
                                <tr>
                                    <td>{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }}</td>
                                    <td>{{ $item->tarif->customer->nama ?? '-' }}</td>
                                    <td>{{ $item->jadwal_kapal->pelayaran->nama ?? '-' }}</td>
                                    <td>{{ $item->tarif->shipmentInfo->nama ?? '-' }}</td>
                                    <td>{{ $item->container }}</td>
                                    <td>{{ $item->seal }}</td>
                                    <td>{{ $item->jadwal_kapal->kapal->nama ?? '-' }}</td>
                                    <td>{{ $item->jadwal_kapal->voyage ?? '-' }}</td>
                                    <td>{{ number_format($item->komisi) }}</td>
                                    <td>{{ is_null($item->invoice_bayar) ? '-' : date('d/m/y',strtotime($item->invoice_bayar)) }}</td>
                                    <td>{{ is_null($item->komisi_print) ? '-' : date('d/m/y',strtotime($item->komisi_print)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasInvBayar" aria-labelledby="offcanvasInvBayarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasInvBayarLabel">Input Tanggal Inv dibayar</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="#" id="form-update1" method="post">
                @csrf
                @method('PUT')
                <div class="mb-2">
                    <label for="id_job">ID JOB</label>
                    <input type="text" disabled name="id_job" class="form-control id_job">
                </div>
                <div class="mb-2">
                    <label for="invoice_bayar">Tanggal Inv Dibayar</label>
                    <input type="date" name="invoice_bayar" id="invoice_bayar" class="form-control">
                </div>
                <button type="button" onclick="simpanInv()" class="btn btn-sm btn-primary">Simpan</button>
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasKomisi" aria-labelledby="offcanvasKomisiLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasKomisiLabel">Input Tanggal Komisi</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="#" id="form-update" method="post">
                @csrf
                @method('PUT')
                <div class="mb-2">
                    <label for="id_job">ID JOB</label>
                    <input type="text" disabled name="id_job" class="form-control id_job">
                </div>
                <div class="mb-2">
                    <label for="tgl_komisi">Tanggal Komisi</label>
                    <input type="date" name="tgl_komisi" id="tgl_komisi" class="form-control">
                </div>
                <button type="button" onclick="simpanKomisi()" class="btn btn-sm btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
    <script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
    <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.20/build/pdfmake.min.js">	</script>
	<script type="text/javascript" language="javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.20/build/vfs_fonts.js"></script>
    <script>
        let table = $('#table-print').DataTable({
            scrollX : true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'print',
                    title:'',
                    customize: function(win) {
                        $(win.document.body)
                        .css('font-size', '.7rem');

                        $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '.7rem');
                    },
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                'colvis'
            ]
        });

        let table2 = $('#table-payment').DataTable({
            scrollX : true,
            select:true,
            columnDefs: [
                {
                    target: 0,
                    visible: false,
                    searchable: false,
                },
            ],
        });

        let order_id;
        let row_id;
        $("#jqGrid1").jqGrid({
            url: '{{ route('jqgrid.order') }}',
            mtype: 'GET',
            datatype: 'json',
            postData: { input_invoice_bayar: true },
            colModel: [
                {search:true, name: 'no', label : 'ID JOB', frozen:true, width:100},
                {search:true, name: 'pembayar', label : 'pembayar', frozen:true, width:100},
                {search:true, name: 'id', label : 'id', hidden:true},
                {search:true, name: 'pelayaran', label : 'pelayaran'},
                {search:true, name: 'shipment', label : 'shipment'},
                {search:true, name: 'container', label : 'container'},
                {search:true, name: 'seal', label : 'seal'},
                {search:true, name: 'kapal', label : 'kapal'},
                {search:true, name: 'voyage', label : 'voyage'},
                {search:true, name: 'komisi', label : 'komisi'},
                {search:true, name: 'invoice_bayar', label : 'Tgl Inv Dibayar',sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'tgl_komisi', label : 'Tgl Komisi',sorttype: 'date', datefmt:'d/m/y'},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100,250,500,1000],
            viewrecords: true,
            pager: "#jqGridPager1",
            caption: "List Order Belum Input Tgl Inv Dibayar",
            onCellSelect: function (rowId, iRow, iCol, e) {
                row_id = rowId;
                order_id = $(this).jqGrid('getCell', rowId, 'id');
                var no = $(this).jqGrid('getCell', rowId, 'no');
                $('.id_job').val(no);
            },
            rowattr: function (item) {
                return { "class": item.class };
            }
        });

        $('#jqGrid1').jqGrid('filterToolbar');
        $('#jqGrid1').jqGrid('navGrid',"#jqGridPager", {
            search: false, // show search button on the toolbar
            add: false,
            edit: false,
            del: false,
            refresh: true,
        });
        $("#jqGrid1").jqGrid('setFrozenColumns');

        $("#jqGrid2").jqGrid({
            url: '{{ route('jqgrid.order') }}',
            mtype: 'GET',
            datatype: 'json',
            postData: { input_komisi: true },
            colModel: [
                {search:true, name: 'no', label : 'ID JOB', frozen:true, width:100},
                {search:true, name: 'pembayar', label : 'pembayar', frozen:true, width:100},
                {search:true, name: 'id', label : 'id', hidden:true},
                {search:true, name: 'pelayaran', label : 'pelayaran'},
                {search:true, name: 'shipment', label : 'shipment'},
                {search:true, name: 'container', label : 'container'},
                {search:true, name: 'seal', label : 'seal'},
                {search:true, name: 'kapal', label : 'kapal'},
                {search:true, name: 'voyage', label : 'voyage'},
                {search:true, name: 'komisi', label : 'komisi'},
                {search:true, name: 'invoice_bayar', label : 'Tgl Inv Dibayar',sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'tgl_komisi', label : 'Tgl Komisi',sorttype: 'date', datefmt:'d/m/y'},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100,250,500,1000],
            viewrecords: true,
            pager: "#jqGridPager2",
            caption: "List Order Belum Input Tgl Komisi",
            onCellSelect: function (rowId, iRow, iCol, e) {
                row_id = rowId;
                order_id = $(this).jqGrid('getCell', rowId, 'id');
                var no = $(this).jqGrid('getCell', rowId, 'no');
                $('.id_job').val(no);
            },
            rowattr: function (item) {
                return { "class": item.class };
            }
        });

        $('#jqGrid2').jqGrid('filterToolbar');
        $('#jqGrid2').jqGrid('navGrid',"#jqGridPager", {
            search: false, // show search button on the toolbar
            add: false,
            edit: false,
            del: false,
            refresh: true
        });
        $("#jqGrid2").jqGrid('setFrozenColumns');

        $("#jqGrid3").jqGrid({
            url: '{{ route('jqgrid.order') }}',
            mtype: 'GET',
            datatype: 'json',
            postData: { komisi_print: true },
            colModel: [
                {search:true, name: 'no', label : 'ID JOB', frozen:true, width:100},
                {search:true, name: 'pembayar', label : 'pembayar', frozen:true, width:100},
                {search:true, name: 'id', label : 'id', hidden:true},
                {search:true, name: 'pelayaran', label : 'pelayaran'},
                {search:true, name: 'shipment', label : 'shipment'},
                {search:true, name: 'container', label : 'container'},
                {search:true, name: 'seal', label : 'seal'},
                {search:true, name: 'kapal', label : 'kapal'},
                {search:true, name: 'voyage', label : 'voyage'},
                {search:true, name: 'komisi', label : 'komisi'},
                {search:true, name: 'invoice_bayar', label : 'Tgl Inv Dibayar',sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'tgl_komisi', label : 'Tgl Komisi',sorttype: 'date', datefmt:'d/m/y'},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100,250,500,1000],
            viewrecords: true,
            loadonce: true,
            pager: "#jqGridPager3",
            caption: "List Order Belum Sudah Terbit Tgl Invoice",
            onCellSelect: function (rowId, iRow, iCol, e) {
                row_id = rowId;
                order_id = $(this).jqGrid('getCell', rowId, 'id');
                var no = $(this).jqGrid('getCell', rowId, 'no');
                $('.id_job').val(no);
            },
            rowattr: function (item) {
                return { "class": item.class };
            }
        });

        $('#jqGrid3').jqGrid('filterToolbar');
        $('#jqGrid3').jqGrid('navGrid',"#jqGridPager", {
            search: false, // show search button on the toolbar
            add: false,
            edit: false,
            del: false,
            refresh: true
        });
        $("#jqGrid3").jqGrid('setFrozenColumns');

        function simpanInv() {
            $.ajax({
                type: "POST",
                url: "{{ url('api/update-order-request') }}",
                data: {
                    id:order_id,
                    invoice_bayar:$('#invoice_bayar').val()
                },
                success: function (response) {
                    alert('Data berhasil disimpan!');
                    $('#jqGrid1').trigger( 'reloadGrid' );
                    $('#jqGrid2').trigger( 'reloadGrid' );
                    var myOffcanvas = document.getElementById('offcanvasInvBayar');
                    var offCanvas = new bootstrap.Offcanvas(myOffcanvas);
                    offCanvas.hide();
                    $('#invoice_bayar').val('');
                }
            });
        }
        function simpanKomisi() {
            $.ajax({
                type: "POST",
                url: "{{ url('api/update-order-request') }}",
                data: {
                    id:order_id,
                    tgl_komisi:$('#tgl_komisi').val()
                },
                success: function (response) {
                    alert('Data berhasil disimpan!');
                    location.reload();
                    $('#jqGrid2').trigger( 'reloadGrid' );
                    $('#jqGrid3').trigger( 'reloadGrid' );
                    var myOffcanvas = document.getElementById('offcanvasKomisi');
                    var offCanvas = new bootstrap.Offcanvas(myOffcanvas);
                    offCanvas.hide();
                    $('#tgl_komisi').val('');
                }
            });
        }

        $("#export").on("click", function(){
			$("#jqGrid3").jqGrid("exportToPdf",{
				title: '',
				orientation: 'landscape',
				pageSize: 'A4',
				customSettings: null,
				download: 'download',
				includeLabels : true,
				includeGroupHeader : true,
				includeFooter: true,
				fileName : "print_komisi.pdf"
			})
		})
    </script>
@endsection
