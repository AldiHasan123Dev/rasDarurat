@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/selectize.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/selectize.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/awesomplete.css') }}">
<style>
    table.dataTable tbody th, table.dataTable tbody td{
        padding: 0px 10px !important;
    }
    .select2.select2-container.select2-container--default{
        width: 100% !important;
    }
    thead input {
        width: 100%;
    }
    .dataTables_scrollBody > table > thead > tr {
        visibility: collapse;
        height: 0px !important;
    }
</style>
@endsection
@section('content')

    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <div class="d-flex flex-wrap" style="gap:10px;">
                    <button class="py-2 px-3 btn btn-sm btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrder" aria-controls="offcanvasOrder">Tambah Order</button>
                    {{-- <button class="py-2 px-3 btn btn-sm btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-export-malindo">JOB Malindo</button>
                    <button class="py-2 px-3 btn btn-sm btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-export-cheiljedang">JOB PT. CJ. CHEILJEDANG</button>
                    <button class="py-2 px-3 btn btn-sm btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-export-fortuna">JOB FORTUNA LILY HALIM</button> --}}
                    <b>N0. JOB (selected): <span class="nojob"></span></b>
                </div>
                <div>
                    {{-- <button  data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-sm btn-info">Cetak SI</button> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsives">
                    {{-- <table class="table table-sm nowrap" id="table-order" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>Tools</th>
                                <th>ID.</th>
                                <th>Tanggal</th>
                                <th>Invoice</th>
                                <th>Group JOB</th>
                                <th>ID JOB</th>
                                <th>Asuransi</th>
                                <th>Pembayar</th>
                                <th>Marketing</th>
                                <th>CS</th>
                                <th>Pengirim</th>
                                <th>Penerima</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Shipment</th>
                                <th>Kondisi</th>
                                <th>Jenis Barang</th>
                                <th>Barang</th>
                                <th>Pelayaran</th>
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>ETD</th>
                                <th>TD</th>
                                <th>BA Kirim</th>
                                <th>Nopol</th>
                                <th>Trucking</th>
                                <th>No Container</th>
                                <th>No Seal</th>
                                <th>Stuffing</th>
                                <th>Tipe Stuffing</th>
                                <th>Tgl Full</th>
                                <th>Barang Diantar</th>
                                <th>BA Kembali</th>
                                <th>Koli</th>
                                <th>M3</th>
                                <th>Berat</th>
                                <th>Satuan</th>
                                <th>Unit</th>
                                <th>Tarif</th>
                                <th>Agen</th>
                                <th>Penerima BL</th>
                                <th>Keterangan</th>
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

        <div class="row">
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header" style="gap:10px" id="bttb-info">
                        <div class="d-flex justify-content-between">
                            <b>N0. JOB (selected): <b class="nojob"></b></b>
                            <b><b class="koli"></b> Koli</b>
                        </div>
                        <div class="p-2 d-flex" style="gap:10px" id="bttb-info">
                            {{-- @if (is_null($marketing))
                            <button class="py-2 px-3 btn btn-sm btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBTTBCreate"><i class="fas fa-plus"></i> Tambah BTTB</button>
                            <button onclick="printBttb()" class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-print"><i class="fas fa-print"></i> Print BTTB</button>
                            <button onclick="printBttb(true)" class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-print"><i class="fas fa-print"></i> Print BTTB Inc Berat</button>
                            <button onclick="printBttbKubikasi()" class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-kubikasi-print"><i class="fas fa-print"></i> Print BTTB Kubikasi</button>
                            <button onclick="printBttbKubikasi(true)" class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-kubikasi-print"><i class="fas fa-print"></i> Print BTTB Kubikasi Inc Berat</button>
                            <a class="py-2 px-3 btn btn-sm btn-info" style="font-size: .7rem" id="edit-bttb"><i class="fas fa-pencil"></i> Edit</a>
                            <button class="py-2 px-3 btn btn-sm btn-danger" style="font-size: .7rem" id="delete-bttb"><i class="fas fa-trash"></i> Hapus</button>
                            @endif --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm nowrap" id="table-bttb" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>No.</th>
                                        <th>Tanggal</th>
                                        <th>No. Gudang</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>P</th>
                                        <th>L</th>
                                        <th>T</th>
                                        <th>Vol</th>
                                        <th>Berat</th>
                                        <th>Tgl Masuk</th>
                                        <th>Pengirim</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasOrder" aria-labelledby="offcanvasOrderLabel"
    style="height:700px">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasOrderLabel">
            Form Order untuk Marketing
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('order.store') }}" method="post" id="create">
            @csrf
            @include('admin.order.form', ['order'=>[]])
            <div class="col-12 mb-2 px-1">
                <button type="button" id="add-order" class="btn btn-success btn-sm">{{ empty($order)?'Tambah':'Update' }} Data</button>
            </div>
        </form>
    </div>
</div>

<div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasBTTBEdit" aria-labelledby="offcanvasBTTBLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasBTTBLabel">Form BTTB</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="form-bttb">
            @csrf
            <div id="message" class="my-3 text-center text-white alert alert-success py-2 px-5"></div>
            <input type="hidden" name="order_id" id="order_id_bttb">
            <input type="hidden" id="bttb_id">
            @include('admin.bttb.form', ['bttb'=>[]])
            <div class="col-12 mb-2 px-1">
                <button type="button" class="btn btn-success btn-sm" id="update-bttb">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="offcanvas offcanvas-bottom" tabindex="-2" id="offcanvasBTTBCreate" aria-labelledby="offcanvasBTTBLabel" style="height: 700px">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasBTTBLabel">Form BTTB</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <b>*Harap Pastikan No Gudang, Nama Barang, Jumlah, Satuan & Pengirim Terisi. Lalu pada inputan pengirim, harap pilih data yang sudah disediakan!</b>
        <form id="form-bttb-create">
            @csrf
            <input type="hidden" name="order_id" id="order-id-create">
            {{-- @include('admin.bttb.form', ['bttb'=>[]]) --}}
            <table class="w-100 table-bordered" style="font-size: .7rem; table-layout:auto">
                <thead>
                    <tr class="text-center">
                        <td>No.Gudang</td>
                        <td style="width: 200px">Barang</td>
                        <td>Qty</td>
                        <td>Satuan</td>
                        <td>P</td>
                        <td>L</td>
                        <td>T</td>
                        <td>Vol Manual</td>
                        <td>Berat</td>
                        <td>Tgl Masuk</td>
                        <td>Pengirim</td>
                        <td>Keterangan</td>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 12; $i++)
                        <tr>
                            <td><input type="text" style="width: 100px" name="no_gudang-{{ $i }}" id="no_gudang-{{ $i }}"></td>
                            <td><input name="barang_id-{{ $i }}" id="barang_id-{{ $i }}" class="barang" style="width: 200px"/></td>
                            <td><input type="number" style="width: 70px" name="qty-{{ $i }}" id="qty-{{ $i }}"/></td>
                            <td><input name="satuan_id-{{ $i }}" id="satuan_id-{{ $i }}" class="satuan" style="width: 100px"/></td>
                            <td><input type="number" step="any" onkeyup="hitungVolCreate({{ $i }})" style="width: 70px" name="p-{{ $i }}" id="p-{{ $i }}"></td>
                            <td><input type="number" step="any" onkeyup="hitungVolCreate({{ $i }})" style="width: 70px" name="l-{{ $i }}" id="l-{{ $i }}"></td>
                            <td><input type="number" step="any" onkeyup="hitungVolCreate({{ $i }})" style="width: 70px" name="t-{{ $i }}" id="t-{{ $i }}"></td>
                            <td><input type="number" style="width: 70px" name="vol-{{ $i }}" id="vol-{{ $i }}"></td>
                            <td><input type="number" style="width: 70px" name="berat-{{ $i }}" id="berat-{{ $i }}"></td>
                            <td><input type="date" style="width: 100px" name="tgl_masuk-{{ $i }}" id="tgl_masuk-{{ $i }}"></td>
                            <td><input name="pengirim_id-{{ $i }}" id="pengirim_id-{{ $i }}" class="pengirim" style="width: 100px"/></td>
                            <td><input type="text" name="keterangan-{{ $i }}" id="keterangan-{{ $i }}"></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            <div class="col-12 mb-2 px-1">
                <button type="button" class="btn btn-success btn-sm mt-3" id="add-bttb">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="tagihan" tabindex="-1" aria-labelledby="tagihanLabel" aria-hidden="true">
    <form action="" class="modal-dialog modal-lg" method="post" id="form-tagihan">
        @csrf
        @method('PUT')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tagihan <span class="nojob"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-sm nowrap w-100" id="table-tagihan" style="font-size:.7rem">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Tagihan</th>
                                    <th>Jumlah</th>
                                    <th>Catatan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-4 mb-2">
                        <label for="nama">Nama Tagihan</label>
                        <input type="text" id="tagihan-nama" name="nama" class="form-control" required>
                    </div>
                    <div class="col-4 mb-2">
                        <label for="jumlah">Jumlah Tagihan</label>
                        <input type="number" name="jumlah" id="tagihan-jumlah" class="form-control" required>
                    </div>
                    <div class="col-4 mb-2">
                        <label for="catatan">Catatan</label>
                        <input type="text" name="catatan" id="tagihan-catatan" class="form-control">
                    </div>
                    <div class="col-12">

                        <button type="button" class="btn btn-primary btn-sm" id="add-tagihan">Simpan</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="modal-edit-order" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit <span class="nojob"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="iframe-order" style="width: 100%; height:100vh"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-add-bttb" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">BTTB <span class="nojob"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="iframe-bttb" style="width: 100%; height:100vh"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-pindah-kapal" tabindex="-1"  aria-hidden="true">
    <form action="{{ route('order.pindah_kapal') }}" method="POST" class="modal-dialog">
        @csrf
        <input type="hidden" name="order_id" id="order_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pindah Kapal JOB <span class="nojob"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <select name="jadwal_kapal_id" class="form-select" id="pindah-kapal-select">

                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure?')">Simpan</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Export-->
<div class="modal fade" id="modal-export" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{ route('order.export') }}" method="POST" class="modal-dialog">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export Data Tanggal JOB</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="from" class="form-label">From</label>
                    <input type="date" name="from" id="from" class="form-control">
                </div>
                <div class="mb-2">
                    <label for="to" class="form-label">To</label>
                    <input type="date" name="to" id="to" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Export Excel</button>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="modal-export-malindo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{ route('order.export.malindo') }}" method="POST" class="modal-dialog">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export Data Malindo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="month" class="form-label">Bulan</label>
                    <input type="month" name="month" id="month" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Export Excel</button>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" id="modal-export-fortuna" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{ route('order.export.fortuna') }}" method="POST" class="modal-dialog">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export Data Fortuna Lily Halim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="month" class="form-label">Bulan</label>
                    <input type="month" name="month" id="month" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Export Excel</button>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" id="modal-export-cheiljedang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{ route('order.export.cheiljedang') }}" method="POST" class="modal-dialog">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export Data JOB PT. CJ. CHEILJEDANG</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="month" class="form-label">Bulan</label>
                    <input type="month" name="month" id="month" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Export Excel</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script src="{{ asset('assets/js/awesomplete.js') }}"></script>
<script>
    $(document).ready(function() {
        topbar.show();
        document.oncontextmenu = new Function("return false");
        $('body').bind('cut copy paste', function(event) {
            event.preventDefault();
        });
    });
</script>
<script>
    $('#edit-order').hide();
    $('#btn-tagihan').hide();
    $('#delete-order').hide();
    $('#btn-pindah-kapal').hide();
</script>
<script>
    let customers = @json($customers);
    let barang = @json($barang);
    let satuan = @json($satuan);
    let agent = @json($agent);
    for (let i = 0; i < 12; i++) {
        new Awesomplete(document.getElementById("barang_id-"+i), {
            list: barang,
            minChars: 3,
            maxItems: 5
        });
        new Awesomplete(document.getElementById("satuan_id-"+i), {
            list: satuan,
            minChars: 2,
            maxItems: 5
        });
        new Awesomplete(document.getElementById("pengirim_id-"+i), {
            list: customers,
            minChars: 3,
            maxItems: 5,
            autoFirst:true
        });
    }
    new Awesomplete(document.getElementById("pengirim_id"), {
        list: customers,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("pengirim_bttb"), {
        list: customers,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("penerima_id"), {
        list: customers,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("penerima_bl_id"), {
        list: customers,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("barang_id"), {
        list: barang,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("barang_bttb"), {
        list: barang,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("satuan_id"), {
        list: satuan,
        minChars: 2,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("agen_id"), {
        list: agent,
        minChars: 3,
        maxItems: 5
    });
</script>
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script src="{{ asset('assets/js/jquery-serializeFields.js') }}"></script>
<script>
    $('#koli-info').hide();
    $('#bttb-info').hide();
    $('#ag').hide();
    $('#copy-order').hide();
    $('#packing-list').hide();
    $('#packing-list-kubikasi').hide();

    let data = [];
    let id;
    let tarif_id = null;
    let lock_biaya;
    let iframe_bttb = '';
    let iframe_order = '';
    $("#jqGrid").jqGrid({
        url: '{{ route('jqgrid.order') }}',
        mtype: 'GET',
        datatype: 'json',
        postData: { marketing_id:  @json($idMarketing) },
        colModel: [
            {search:true, width:100, name: 'job', label : 'job', frozen:true},
            {search:true, width:100, name: 'no', label : 'no', frozen:true, sortable: false},
            {search:true, width:100, name: 'created_at', label : 'tanggal'},
            {search:true, width:100, name: 'invoice', label : 'invoice'},
            {search:true, width:100, name: 'asuransi', label : 'asuransi'},
            {search:true, width:100, name: 'pembayar', label : 'pembayar',sortable: false},
            {search:true, width:100, name: 'id', label : 'id', hidden:true},
            {search:true, width:100, name: 'tarif_id', label : 'tarif_id', hidden:true},
            {search:true, width:100, name: 'lock_biaya', label : 'lock_biaya', hidden:true},
            {search:true, width:100, name: 'class', label : 'class', hidden:true},
            {search:true, width:100, name: 'marketing', label : 'marketing',sortable: false},
            {search:true, width:100, name: 'cs', label : 'cs',sortable: false},
            {search:true, width:100, name: 'pengirim', label : 'pengirim',sortable: false},
            {search:true, width:100, name: 'penerima', label : 'penerima',sortable: false},
            {search:true, width:100, name: 'dari', label : 'dari',sortable: false},
            {search:true, width:100, name: 'tujuan', label : 'tujuan',sortable: false},
            {search:true, width:100, name: 'shipment', label : 'shipment',sortable: false},
            {search:true, width:100, name: 'kondisi', label : 'kondisi',sortable: false},
            {search:true, width:100, name: 'barang', label : 'Jenis barang',sortable: false},
            {search:true, width:100, name: 'barang_detail', label : 'Barang',sortable: false},
            {search:true, width:100, name: 'pelayaran', label : 'pelayaran',sortable: false},
            {search:true, width:100, name: 'kapal', label : 'kapal',sortable: false},
            {search:true, width:100, name: 'voyage', label : 'voyage',sortable: false},
            {search:true, width:100, name: 'etd', label : 'etd',sorttype: 'date', datefmt:'d/m/y',sortable: false},
             {search:true, name: 'eta', label : 'eta',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, width:100, name: 'td', label : 'td',sorttype: 'date', datefmt:'d/m/y',sortable: false},
            {search:true, width:100, name: 'ba_kirim', label : 'ba_kirim',sorttype: 'date', datefmt:'d/m/y',sortable: false},
            {search:true, width:100, name: 'nopol', label : 'nopol'},
            {search:true, width:100, name: 'trucking', label : 'trucking'},
            {search:true, width:100, name: 'container', label : 'container'},
            {search:true, width:100, name: 'seal', label : 'seal'},
            {search:true, width:100, name: 'stuffing', label : 'stuffing'},
            {search:true, width:100, name: 'stuffing_type', label : 'stuffing_type',sortable: false},
            {search:true, width:100, name: 'full', label : 'full'},
            {search:true, width:100, name: 'barang_diantar', label : 'barang_diantar'},
            {search:true, name: 'ba_diantar_sby', label : 'ba_diantar_sby',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, width:100, name: 'syarat_ba', label : 'syarat_ba'},
            {search:true, width:100, name: 'ba_kembali', label : 'ba_kembali',sorttype: 'date', datefmt:'d/m/y'},
            {search:true, width:100, name: 'koli', label : 'koli',sortable: false},
            {search:true, width:100, name: 'm3', label : 'm3',sortable: false},
            {search:true, width:100, name: 'berat', label : 'berat',sortable: false},
            {search:true, width:100, name: 'satuan', label : 'satuan',sortable: false},
            {search:true, width:100, name: 'unit', label : 'unit',sortable: false},
            {search:true, width:100, name: 'tarif', label : 'tarif',sortable: false},
            {search:true, width:100, name: 'komisi', label : 'Fee Cust',sortable: false},
            {search:true, width:100, name: 'agen', label : 'agen'},
            {search:true, width:100, name: 'penerima_bl', label : 'penerima_bl',sortable: false},
            {search:true, width:100, name: 'keterangan', label : 'keterangan'},
            {search:true, width:100, name: 'add_cost', label : 'Add Cost'},
        ],
        autowidth: true,
        shrinkToFit: false,
        height: 250,
        oadonce: true,
        rowNum: 25,
        rowList:[10,25,50,100,250,500,1000],
        viewrecords: true,
        pager: "#jqGridPager",
        caption: "Order Job",
        onCellSelect: function (rowId, iRow, iCol, e) {
            id = $(this).jqGrid('getCell', rowId, 'id');
            tarif_id = $(this).jqGrid('getCell', rowId, 'tarif_id');
            var no_job = $(this).jqGrid('getCell', rowId, 'no');
            var koli = $(this).jqGrid('getCell', rowId, 'koli');
            var invoice = $(this).jqGrid('getCell', rowId, 'invoice');
            lock_biaya = $(this).jqGrid('getCell', rowId, 'lock_biaya');
            if (invoice && invoice !== "-") {
        $('#btn-pindah-kapal').hide(); // Sembunyikan tombol jika invoice ada
    } else {
        $('#btn-pindah-kapal').show(); // Tampilkan tombol jika invoice kosong
    }
            $('#btn-tagihan').show();
            $('#bttb-info').show();
            $('#koli-info').show();
            $('#edit-order').show();
            $('#delete-order').show();
            $('#copy-order').show();
            $('#packing-list').show();
            $('#packing-list-kubikasi').show();
            $('#order_id_bttb').val(id);
            $('#order-id-create').val(id);
            $('.nojob').html(no_job);
            $('.koli').html(koli);
            $('#edit-order').attr('href','{{ url('admin/order') }}/'+id+'/edit');
            $('#delete-order').attr('action','{{ url('admin/order') }}/'+id);
            $('#copy-order').attr('action','{{ url('admin/copy-orders') }}/'+id);
            iframe_bttb = '{{ url('admin/bttb/create') }}?order_id='+id;
            iframe_order = '{{ url('admin/order') }}/'+id+'/edit'
            // $('#iframe-order').attr('src','{{ url('admin/order') }}/'+id+'/edit');
            // $('#iframe-bttb').attr('src','{{ url('admin/bttb/create') }}?order_id='+id);
            $('#tarik-ba').attr('action','{{ url('admin/order') }}/'+id);
            if(lock_biaya!=1){
                $('#btn-tagihan').show();
                $('#btn-lock').html('Kunci Biaya');
            }else{
                $('#btn-tagihan').hide();
                $('#btn-lock').html('Buka Kunci Biaya');
            }
            // let role = "{{ Auth::user()->role_id }}";
            // if(parseInt(role)==1){
            //     $('#btn-tagihan').show();
            // }
            tablebttb.ajax.reload();
            tableTagihan.ajax.reload();
        },
        rowattr: function (item) {
            return { "class": item.class };
        }
    });

    $('#jqGrid').jqGrid('filterToolbar');
    $('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
        search: false,
        add: false,
        edit: false,
        del: false,
        refresh: true
    });
    $("#jqGrid").jqGrid('setFrozenColumns');


    function loadTable() {
        $('#jqGrid').jqGrid('clearGridData');
        $('#jqGrid').jqGrid('setGridParam', {data: data});
        $('#jqGrid').trigger('reloadGrid');
    }

    function getData(start) {
        $.ajax({
            type: "GET",
            url: "{{ url('api/get-order') }}",
            data:{start:start,limit:50},
            success: function (response) {
                $.each(response.data, function (idx, item) {
                    data.push(item)
                });
                loadTable();
                if(response.start<response.count){
                    getData(response.start)
                }else{
                    $('#loading').remove();
                    topbar.hide();
                }
            }
        });
    }

    // getData(0)

        let tablebttb = $('#table-bttb').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('bttb.data') }}',
                method:'POST',
                data:function( d) {
                    d.order_id = id;
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id', visible:false },
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                { data: 'created_at', name: 'created_at' },
                { data: 'no_gudang', name: 'no_gudang' },
                { data: 'barang_id', name: 'nama_barang' },
                { data: 'qty', name: 'qty' },
                { data: 'satuan_id', name: 'satuan.nama' },
                { data: 'p', name: 'p' },
                { data: 'l', name: 'l' },
                { data: 't', name: 't' },
                { data: 'vol', name: 'vol' },
                { data: 'berat', name: 'berat' },
                { data: 'tgl_masuk', name: 'tgl_masuk' },
                { data: 'pengirim_id', name: 'pengirim_id' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'action', name: 'action', orderable: false, searchable: false, visible:false },
            ],
            select:true
        });

        let tableTagihan = $('#table-tagihan').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('tagihan.data') }}',
                method:'POST',
                data:function( d) {
                    d.order_id = id;
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                { data: 'nama', name: 'nama' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'catatan', name: 'catatan' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });


        $("select[name=tarif_id]").select2({
            dropdownParent: $('#offcanvasOrder')
        });
        $("#jadwal_kapal_id-si").select2({
            dropdownParent: $('#exampleModal'),
        });
        $("#tujuan-si").select2({
            dropdownParent: $('#exampleModal'),
        });

        $(document).on('keyup', '.select2-search__field', function(e){
            e.target.value = e.target.value.toUpperCase()
        });

        $(document).on('keyup', '#no_gudang', function(e){
            e.target.value = e.target.value.toUpperCase()
        });

        $(document).on('keyup', '#barang_id', function(e){
            e.target.value = e.target.value.toUpperCase()
        });

        $(document).on('keyup', '#satuan_id', function(e){
            e.target.value = e.target.value.toUpperCase()
        });

        $(document).on('keyup', '#pengirim_bttb', function(e){
            e.target.value = e.target.value.toUpperCase()
        });


        $('#add-bttb').click(function (e) {
            if(confirm('are you sure?')){
                let data = $("#form-bttb-create").serializeFields();
                $.ajax({
                    type: "POST",
                    url: "{{ url('api/api-bttb-add') }}",
                    data:data,
                    success: function (response) {
                        $('#jqGrid').trigger( 'reloadGrid' );
                        tablebttb.ajax.reload();
                        $('.koli').html(response);
                        $('#form-bttb-create')[0].reset()
                        alert('Data berhasil ditambahkan! Jumlah Koli Sekarang adalah '+response);
                    }
                });
            }
        });

        $("select[name=tarif_id]").change(function (e) {
            var val = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('api.tarif.getOne') }}",
                data: {id:val},
                success: function (response) {
                    let data = response;
                    let tarif = data.tarif;
                    $('form#create #tarif').val('Rp. '+tarif.toLocaleString('en-US'));
                    $('form#create #dari').val(data.dari);
                    $('form#create #tujuan').val(data.tujuan);
                    $('form#create #shipment').val(data.shipment);
                    $('form#create #kondisi').val(data.kondisi);
                    $('form#create #satuan').val(data.satuan);
                }
            });
        });

        $('#tarif_id').change(function (e) {
            var val = $(this).val();
            $.ajax({
                type: "GET",
                url: "/api/get-jadwal-kapal-pelayaran/"+val,
                success: function (response) {
                    var data = response;
                    var html = '<option>Pilih Kapal</option>';
                    $.each(data, function (id, name) {
                        html += '<option value="'+id+'">'+name+'</option>'
                    });
                    $('select[name=jadwal_kapal_id]').html(html);
                }
            });
        });

        $('#agen').change(function (e) {
            var val = $(this).val();
            if (val=='AGEN') {
                $('#ag').show();
                $('#nag').hide();
            }else{
                $('#nag').show();
                $('#ag').hide();
            }
        });

        function hitungVol(){
            var p = $('#p').val();
            var l = $('#l').val();
            var t = $('#t').val();
            var vol = $('#vol').val();
            var qty = $('#qty').val();
            if(p>0&&l>0&&t>0){
                vol = ((p*l*t)/1000000) * qty;
                vol = vol.toFixed(3);
            }else{
                vol = 0;
            }
            $('#vol').val(vol);
        }

        function hitungVolCreate(i){
            var p = $('#p-'+i).val();
            var l = $('#l-'+i).val();
            var t = $('#t-'+i).val();
            var vol = $('#vol-'+i).val();
            var qty = $('#qty-'+i).val();
            if(p>0&&l>0&&t>0){
                vol = ((p*l*t)/1000000) * qty;
                vol = vol.toFixed(3);
            }
            $('#vol-'+i).val(vol);
        }

        $('#update-bttb').click(function (e) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.customer.getCustomer') }}",
                data: {nama:$('#pengirim_bttb').val()},
                success: function (response) {
                    if (response==0) {
                        alert('Pengirim tidak ditemukan di data Customer! silahkan cek data lagi')
                    }else{
                        var data = {
                            id : $('#bttb_id').val(),
                            order_id : $('#order_id_bttb').val(),
                            no_gudang : $('#no_gudang').val(),
                            barang_id : $('#barang_bttb').val(),
                            qty : $('#qty').val(),
                            satuan_id : $('#satuan_id').val(),
                            p : $('#p').val(),
                            l : $('#l').val(),
                            t : $('#t').val(),
                            vol : $('#vol').val(),
                            berat : $('#berat').val(),
                            tgl_masuk : $('#tgl_masuk').val(),
                            pengirim_id : response.id,
                            keterangan : $('#keterangan-bttb').val(),
                        }

                        $.ajax({
                            type: "POST",
                            url: "{{ route('api-bttb.store') }}",
                            data: data,
                            success: function (response) {
                                if (response.status=='success') {
                                    $('#no_gudang').val('');
                                    $('#qty').val('');
                                    $('#barang_bttb').val('');
                                    $('#satuan_id').val('');
                                    $('#p').val('');
                                    $('#l').val('');
                                    $('#t').val('');
                                    $('#vol').val('');
                                    $('#berat').val('');
                                    $('#keterangan-bttb').val('');
                                    $('#message').show();
                                    $('#message').html(response.message);
                                    tablebttb.ajax.reload();
                                    // tableOrder.ajax.reload();
                                    setTimeout(() => {
                                        $('#message').hide();
                                    }, 3000);
                                }
                            }
                        });
                    }
                }
            });
        });

        $('#edit-bttb').click(function (e) {
            var data = tablebttb.row({selected:true}).data();
            $('#bttb_id').val(data.id);
            $('#no_gudang').val(data.no_gudang);
            $('#qty').val(data.qty);
            $('#barang_bttb').val(data.barang_id);
            $('#satuan_id').val(data.satuan_id);
            $('#p').val(data.p);
            $('#l').val(data.l);
            $('#t').val(data.t);
            $('#vol').val(data.vol);
            $('#berat').val(data.berat);
            $('#keterangan-bttb').val(data.keterangan);
            $('#pengirim_bttb').val(data.pengirim_id);
            var tgl = data.tgl_masuk;
            var date = tgl.split("/").reverse().join("-");
            $('#tgl_masuk').val(date);
            var myOffcanvas = document.getElementById('offcanvasBTTBEdit');
            var offCanvas = new bootstrap.Offcanvas(myOffcanvas);
            offCanvas.show();
        });

        $('#delete-bttb').click(function (e) {
            if(confirm('Apa anda yakin?')){
                var data = tablebttb.row({selected:true}).data();
                $.ajax({
                    method: "DELETE",
                    url: "{{ url('api/api-bttb-delete') }}",
                    data:{id:data.id},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (response) {
                        if (response.status=='success') {
                            alert(response.message);
                            tablebttb.ajax.reload();
                        }
                    }
                });
            }
        });

        $('#tambah-bttb').click(function (e) {
            $('#bttb_id').val(0);
            $('#no_gudang').val('');
            $('#qty').val('');
            $('#barang_bttb').val('');
            $('#satuan_id').val('');
            $('#p').val('');
            $('#l').val('');
            $('#t').val('');
            $('#vol').val('');
            $('#berat').val('');
            $('#pengirim_bttb').val('');
            $('#keterangan-bttb').val('');
            var myOffcanvas = document.getElementById('offcanvasBTTBCreate');
            var offCanvas = new bootstrap.Offcanvas(myOffcanvas);
            offCanvas.show();
        });

        $('#add-order').click(function (e) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.customer.getCustomer') }}",
                data: {nama:[$('#pengirim_id').val(),$('#penerima_id').val()]},
                success: function (response) {
                    if (response==0) {
                        alert('Pengirim atau Penerima tidak ditemukan di data Customer! silahkan cek data lagi')
                    }else{
                        $('#create').submit();
                    }
                }
            });
        });

        $('#add-tagihan').click(function (e) {
            let nama = $('#tagihan-nama').val();
            let jumlah = $('#tagihan-jumlah').val();
            let catatan = $('#tagihan-catatan').val();
            if(nama==''||jumlah==''||jumlah=='0'){
                alert('Nama dan jumlah tidak boleh kosong!');
            }else{
                $.ajax({
                    type: "POST",
                    url: "{{ route('api.tagihan.store') }}",
                    data: {
                        order_id:id,
                        nama:nama,
                        jumlah:jumlah,
                        catatan:catatan,
                    },
                    success: function (response) {
                        $('#tagihan-nama').val('');
                        $('#tagihan-jumlah').val('');
                        $('#tagihan-catatan').val('');
                        tableTagihan.ajax.reload();
                    }
                });
            }
        });

        function editTagihan(id){
            $.ajax({
                type: "GET",
                url: "{{ url('api/tagihan') }}/"+id,
                success: function (response) {
                    $('#tagihan-nama').val(response.nama);
                    $('#tagihan-jumlah').val(response.jumlah);
                    $('#tagihan-catatan').val(response.catatan);
                }
            });
        }

        $('#copy-order-btn').click(function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: $('#copy-order').attr('action'),
                data:{
                    "_token": "{{ csrf_token() }}",
                },
                success: function (response) {
                    alert(response);
                    $('#jqGrid').trigger( 'reloadGrid' );
                }
            });
        });

        function deleteTagihan(id){
            $.ajax({
                type: "DELETE",
                url: "{{ url('api/tagihan') }}/"+id,
                success: function (response) {
                    tableTagihan.ajax.reload();
                }
            });
        }

        $('#message').hide();
        $('#p').keyup(function (e) {
            hitungVol()
        });
        $('#l').keyup(function (e) {
            hitungVol()
        });
        $('#t').keyup(function (e) {
            hitungVol()
        });
        var myModalEl = document.getElementById('modal-edit-order')
        myModalEl.addEventListener('hidden.bs.modal', function (event) {
            $("#jqGrid").trigger("reloadGrid");
        })
        var modalBTTB = document.getElementById('modal-add-bttb')
        modalBTTB.addEventListener('hidden.bs.modal', function (event) {
            $("#jqGrid").trigger("reloadGrid");
            tablebttb.ajax.reload();
        })

        $('#btn-lock').click(function (e) {
            let val = 1;
            if(lock_biaya==1){
                val = 0;
            }
            $.ajax({
                type: "POST",
                url: "{{ url('api/update-order-request') }}",
                data: {
                    lock_biaya:val,
                    id:id
                },
                success: function (response) {
                    alert('Data berhasil disimpan')
                    if(val!=1){
                        $('#btn-tagihan').show();
                        $('#btn-lock').html('Kunci Biaya');
                    }else{
                        $('#btn-tagihan').hide();
                        $('#btn-lock').html('Buka Kunci Biaya');
                    }
                    $("#jqGrid").trigger("reloadGrid");
                    tablebttb.ajax.reload();

                }
            });
        });

        const printPackingList = ()=>{
            let url = @json(url('admin/cetak/packing-list'))+'?order_id='+id+'&print=1';
            let params_ = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=800,height=500,left=100,top=100`;
            open(url, 'Cetak Packing List', params_);
        }

        const printBttb = (berat = false)=>{
            let url = @json(url('admin/cetak/bttb'))+'?order_id='+id+'&print=1';
            if (berat) {
                url += '&berat=kg';
            }
            let params_ = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=800,height=500,left=100,top=100`;
            open(url, 'Cetak BTTB', params_);
        }

        const printPackingListKubikasi = ()=>{
            let url = @json(url('admin/cetak/packing-list-kubikasi'))+'?order_id='+id+'&print=1';
            let params_ = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=800,height=500,left=100,top=100`;
            open(url, 'Cetak Packing List', params_);
        }

        const printBttbKubikasi = (berat = false)=>{
            let url = @json(url('admin/cetak/bttb-kubikasi'))+'?order_id='+id+'&print=1';
            if (berat) {
                url += '&berat=kg';
            }
            let params_ = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=800,height=500,left=100,top=100`;
            open(url, 'Cetak BTTB', params_);
        }

        function modalEditOrder(){
            var myModal = new bootstrap.Modal(document.getElementById('modal-edit-order'));
            $('#iframe-order').attr('src',iframe_order);
            myModal.show();
        }

        function modalAddBTTB(){
            var myModal = new bootstrap.Modal(document.getElementById('modal-add-bttb'));
            $('#iframe-bttb').attr('src',iframe_bttb);
            myModal.show();
        }
        function modalPindahKapal(){
            var myModal = new bootstrap.Modal(document.getElementById('modal-pindah-kapal'));
            $('#order_id').val(id);
            $.ajax({
                type: "GET",
                url: "/api/get-jadwal-kapal-pelayaran/"+tarif_id,
                success: function (response) {
                    var data = response;
                    var html = '<option>Pilih Kapal</option>';
                    $.each(data, function (id, name) {
                        html += '<option value="'+id+'">'+name+'</option>'
                    });
                    $('#pindah-kapal-select').html(html);
                }
            });
            myModal.show();
        }
</script>
@endsection
