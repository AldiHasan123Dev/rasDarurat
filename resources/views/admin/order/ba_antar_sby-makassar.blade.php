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
</style>
@endsection
@section('content')

    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <div class="d-flex" style="gap: 12px">
                    @if (Auth::user()->role_id==1 || Auth::id()==5)
                        <form action="{{ route('order.export.ba_kembali') }}" method="post">
                            @csrf
                            <button class="py-2 px-3 btn btn-sm btn-success" type="submit">Export Excel</button>
                        </form>
                    @endif
                    <button data-bs-toggle="modal" data-bs-target="#ba-kembali" class="btn btn-sm btn-success">BA Diantar SBY
                    </button>
                    <b>N0. JOB (selected): <span class="nojob"></span></b>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsives">
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="modal fade" id="ba-kembali" tabindex="-1" aria-labelledby="ba-kembaliLabel" aria-hidden="true">
    <form action="" class="modal-dialog" method="post" id="form-ba-kembali">
        @csrf
        @method('PUT')
        <input type="hidden" name="order_id" id="order_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ba-'.$data->id.'Label">BA Diantar SBY (Makassar)<span class="nojob"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-2">
                        <label for="ba_kirim">BA Diantar SBY (Makassar)</label>
                        <input type="date" name="ba_diantar_sby" class="form-control" id="ba_diantar_sby">
                    </div>
                    {{-- <div class="col-12 mb-2">
                        <label for="ba_kembali">Barang Diantar</label>
                        <input type="date" name="barang_diantar" class="form-control" id="barang_diantar">
                    </div>  --}}
                    {{-- <div class="col-12 mb-2">
                        <label for="ba_kembali">BA Kembali</label>
                        <input type="date" name="ba_kembali" class="form-control" id="ba_kembali">
                    </div> --}}
                    <div class="col-12 mb-2">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" name="ba" value="1" class="btn btn-primary" id="simpan">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')

<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script>
    $('#bttb-info').hide();
    $('#ag').hide();
    var modal = new bootstrap.Modal(document.getElementById('ba-kembali'))
    let id = null;
    let row_id = null;

    $("#jqGrid").jqGrid({
        url: '{{ route('jqgrid.order') }}',
        mtype: 'GET',
        datatype: 'json',
        postData: { ba_diantar_sby_makassar_null: true },
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
             {search:true, name: 'eta', label : 'eta',sorttype: 'date', datefmt:'d/m/y'},
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
            {search:true, name: 'ba_diantar_sby', label : 'ba_diantar_sby',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'ba_kembali', label : 'ba_kembali',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, name: 'satuan', label : 'satuan'},
            {search:true, name: 'unit', label : 'unit'},
            {search:true, name: 'tarif', label : 'tarif'},
            {search:true, name: 'agen', label : 'agen'},
            {search:true, name: 'penerima_bl', label : 'penerima_bl'},
            {search:true, name: 'keterangan', label : 'keterangan'},
            {hidden:true, name: 'ba_kirim_date', label : 'ba_kirim_date'},
            {hidden:true, name: 'barang_diantar_date', label : 'barang_diantar_date'},
        ],
        autowidth: true,
        shrinkToFit: false,
        height: 250,
        oadonce: true,
        rowNum: 25,
        rowList:[10,25,50,100,250,500,1000],
        viewrecords: true,
        pager: "#jqGridPager",
        caption: "Order Job BA Diantar SBY (Makassar)",
        onCellSelect: function (rowId, iRow, iCol, e) {
            row_id = rowId;
            id = $(this).jqGrid('getCell', rowId, 'id');
            var no = $(this).jqGrid('getCell', rowId, 'no');
            var ba_kirim = $(this).jqGrid('getCell', rowId, 'ba_kirim_date');
             var keterangan = $(this).jqGrid('getCell', rowId, 'keterangan');
            var barang_diantar = $(this).jqGrid('getCell', rowId, 'barang_diantar_date');
            console.log(ba_kirim, barang_diantar);
            $('#order_id_bttb').val(id);
            $('#keterangan').val(keterangan);
            $('#order_id').val(id);
            $('#barang_diantar').val(barang_diantar);
            $('#ba_kirim').val(ba_kirim);
            $('.nojob').html(no);
            $('#form-ba-kembali').attr('action','{{ url('admin/order') }}/'+id);
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

    $('#simpan').click(function (e) {
        if (confirm('are you sure?')) {
            $.ajax({
                type: "POST",
                url: "{{ url('api/update-order') }}",
                data: {
                    ba:1,
                    order_id:$('#order_id').val(),
                    barang_diantar:$('#barang_diantar').val(),
                    ba_kembali:$('#ba_kembali').val(),
                    ba_kirim:$('#ba_kirim').val(),
                    ba_diantar_sby:$('#ba_diantar_sby').val(),
                    keterangan:$('#keterangan').val(),
                },
                success: function (response) {
                    $('#jqGrid').trigger( 'reloadGrid' );
                    alert('Data berhasil disimpan');
                    modal.hide();
                    $('#order_id').val('');
                    $('#ba_kembali').val('');
                    $('#barang_diantar').val('');
                    $('#keterangan').val('');
                }
            });
        }
    });
</script>
@endsection
