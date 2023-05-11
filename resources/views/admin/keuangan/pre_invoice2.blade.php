@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
@endsection
@section('content')

    <div class="container mt-3">
        <a href="{{ route('keuangan.pre_invoice1') }}">Lihat Pre Invoice Versi 1 tabel</a>
        <div class="card mt-3">
            <div class="card-header py-2 px-5 d-flex justify-content-between" style="gap:10px">
                <div class="card-titles">List Pre Invoice (Read Only)</div>
                <div class="d-flex gap-2">
                    <a href="" class="btn btn-sm btn-success" id="cetak-invoice"><i class="fas fa-print"></i> Cetak Invoice Global</a>
                    <a href="" class="btn btn-sm btn-success" id="cetak-cont-invoice"><i class="fas fa-print"></i> Cetak Invoice Per Cont</a>
                </div>
                <p>No JOB: <span class="nojob"></span></p>
            </div>
            <div class="card-body">
                <table id="jqGrid1"></table>
                <div id="jqGridPager1"></div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header py-2 px-5 d-flex justify-content-between" style="gap:10px">
                <div class="card-titles">List Pre Invoice Tidak Perlu BA Kembali(Read Only)</div>
                <div class="d-flex gap-2">
                    <a href="" class="btn btn-sm btn-success" id="cetak-invoice2"><i class="fas fa-print"></i> Cetak Invoice Global</a>
                    <a href="" class="btn btn-sm btn-success" id="cetak-cont-invoice2"><i class="fas fa-print"></i> Cetak Invoice Per Cont</a>
                </div>
                <p>No JOB: <span class="nojob2"></span></p>
            </div>
            <div class="card-body">
                <table id="jqGrid2"></table>
                <div id="jqGridPager2"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script>
    let data1 = @json($data1);
    let data2 = @json($data2);
    $("#jqGrid1").jqGrid({
        datatype: 'local',
        data: data1,
        colModel: [
            {search:true, name: 'invoice', label : 'invoice', frozen:true, width:70},
            {search:true, name: 'job', label : 'job', frozen:true, width:70},
            {search:true, name: 'no', label : 'no', frozen:true, width:70},
            {search:true, name: 'asuransi', label : 'asuransi', frozen:true, width:70},
            {search:true, name: 'pembayar', label : 'pembayar', frozen:true, width:70},
            {search:true, name: 'id', label : 'id', hidden:true},
            {search:true, name: 'class', label : 'class', hidden:true},
            {search:true, name: 'marketing', label : 'marketing'},
            {search:true, name: 'cs', label : 'cs'},
            {search:true, name: 'pengirim', label : 'pengirim'},
            {search:true, name: 'penerima', label : 'penerima'},
            {search:true, name: 'dari', label : 'dari'},
            {search:true, name: 'tujuan', label : 'tujuan'},
            {search:true, name: 'shipment', label : 'shipment'},
            {search:true, name: 'kondisi', label : 'kondisi'},
            {search:true, name: 'barang', label : 'barang'},
            {search:true, name: 'pelayaran', label : 'pelayaran'},
            {search:true, name: 'kapal', label : 'kapal'},
            {search:true, name: 'voyage', label : 'voyage'},
            {search:true, name: 'etd', label : 'etd',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'td', label : 'td',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'ba_kirim', label : 'ba_kirim',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'nopol', label : 'nopol'},
            {search:true, name: 'trucking', label : 'trucking'},
            {search:true, name: 'container', label : 'container'},
            {search:true, name: 'seal', label : 'seal'},
            {search:true, name: 'stuffing', label : 'stuffing'},
            {search:true, name: 'stuffing_type', label : 'stuffing_type'},
            {search:true, name: 'full', label : 'full'},
            {search:true, name: 'barang_diantar', label : 'barang_diantar'},
            {search:true, name: 'ba_kembali', label : 'ba_kembali',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'satuan', label : 'satuan'},
            {search:true, name: 'unit', label : 'unit'},
            {search:true, name: 'tarif', label : 'tarif'},
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
        pager: "#jqGridPager1",
        caption: "Order Job Pre Invoice",
        onCellSelect: function (rowId, iRow, iCol, e) {
            var id = $(this).jqGrid('getCell', rowId, 'id');
            var no = $(this).jqGrid('getCell', rowId, 'no');
            $('.nojob').html(no);
            $('#cetak-invoice').attr('href','{{ route('cetak.invoice') }}?order_id='+id);
            $('#cetak-cont-invoice').attr('href','{{ route('cetak.invoice.cont') }}?order_id='+id);
        },
        rowattr: function (item) {
            return { "class": item.class };
        }
    });

    $('#jqGrid1').jqGrid('filterToolbar',{stringResult: true, searchOnEnter: false, defaultSearch: 'cn'});
    $('#jqGrid1').jqGrid('navGrid',"#jqGridPager", {
        search: false, // show search button on the toolbar
        add: false,
        edit: false,
        del: false,
        refresh: true
    });
    $("#jqGrid1").jqGrid('setFrozenColumns');

    $("#jqGrid2").jqGrid({
        datatype: 'local',
        data: data2,
        colModel: [
            {search:true, name: 'invoice', label : 'invoice', frozen:true, width:70},
            {search:true, name: 'job', label : 'job', frozen:true, width:70},
            {search:true, name: 'no', label : 'no', frozen:true, width:70},
            {search:true, name: 'asuransi', label : 'asuransi', frozen:true, width:70},
            {search:true, name: 'pembayar', label : 'pembayar', frozen:true, width:70},
            {search:true, name: 'id', label : 'id', hidden:true},
            {search:true, name: 'class', label : 'class', hidden:true},
            {search:true, name: 'marketing', label : 'marketing'},
            {search:true, name: 'cs', label : 'cs'},
            {search:true, name: 'pengirim', label : 'pengirim'},
            {search:true, name: 'penerima', label : 'penerima'},
            {search:true, name: 'dari', label : 'dari'},
            {search:true, name: 'tujuan', label : 'tujuan'},
            {search:true, name: 'shipment', label : 'shipment'},
            {search:true, name: 'kondisi', label : 'kondisi'},
            {search:true, name: 'barang', label : 'barang'},
            {search:true, name: 'pelayaran', label : 'pelayaran'},
            {search:true, name: 'kapal', label : 'kapal'},
            {search:true, name: 'voyage', label : 'voyage'},
            {search:true, name: 'etd', label : 'etd',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'td', label : 'td',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'ba_kirim', label : 'ba_kirim',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'nopol', label : 'nopol'},
            {search:true, name: 'trucking', label : 'trucking'},
            {search:true, name: 'container', label : 'container'},
            {search:true, name: 'seal', label : 'seal'},
            {search:true, name: 'stuffing', label : 'stuffing'},
            {search:true, name: 'stuffing_type', label : 'stuffing_type'},
            {search:true, name: 'full', label : 'full'},
            {search:true, name: 'barang_diantar', label : 'barang_diantar'},
            {search:true, name: 'ba_kembali', label : 'ba_kembali',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'satuan', label : 'satuan'},
            {search:true, name: 'unit', label : 'unit'},
            {search:true, name: 'tarif', label : 'tarif'},
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
        pager: "#jqGridPager2",
        caption: "Order Job Pre Invoice",
        onCellSelect: function (rowId, iRow, iCol, e) {
            var id = $(this).jqGrid('getCell', rowId, 'id');
            var no = $(this).jqGrid('getCell', rowId, 'no');
            $('.nojob2').html(no);
            $('#cetak-invoice2').attr('href','{{ route('cetak.invoice') }}?order_id='+id);
            $('#cetak-cont-invoice2').attr('href','{{ route('cetak.invoice.cont') }}?order_id='+id);
        },
        rowattr: function (item) {
            return { "class": item.class };
        }
    });

    $('#jqGrid2').jqGrid('filterToolbar',{stringResult: true, searchOnEnter: false, defaultSearch: 'cn'});
    $('#jqGrid2').jqGrid('navGrid',"#jqGridPager", {
        search: false, // show search button on the toolbar
        add: false,
        edit: false,
        del: false,
        refresh: true
    });
    $("#jqGrid2").jqGrid('setFrozenColumns');
</script>
@endsection
