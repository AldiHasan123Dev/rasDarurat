@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

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
    .autocomplete {
        position: relative;
        display: inline-block;
    }
    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }
    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }
    .autocomplete-items div:hover {
        /*when hovering an item:*/
        background-color: #e9e9e9;
    }
    .autocomplete-active {
        /*when navigating through the items using the arrow keys:*/
        background-color: DodgerBlue !important;
        color: #ffffff;
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
                <div class="d-flex" style="gap:10px">
                    @if (!request('filter-order'))
                    <button class="py-2 px-3 btn btn-sm btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrder" aria-controls="offcanvasOrder">Tambah Order</button>
                    @endif
                    <a href="" id="edit-order" class="py-2 px-3 btn btn-sm btn-primary">Edit Order</a>
                    <a href="" id="packing-list" class="py-2 px-3 btn btn-sm btn-warning">Packing List</a>
                    <a href="" id="packing-list-kubikasi" class="py-2 px-3 btn btn-sm btn-warning">Packing List Kubikasi</a>
                    <form action="" id="copy-order" method="post" enctype="multipart/form-data">
                        @csrf
                        <button class="py-2 px-3 btn btn-sm btn-secondary" type="submit" onclick="return confirm('Are you sure?')">Copy Order</button>
                    </form>
                    <form action="" id="delete-order" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('DELETE')
                        <button class="py-2 px-3 btn btn-sm btn-danger" type="submit" onclick="return confirm('Are you sure?')">Hapus Order</button>
                    </form>
                    <button data-bs-toggle="modal" data-bs-target="#tagihan" class="btn btn-sm btn-success" id="btn-tagihan">Tambah Tagihan</button>
                    <b>N0. JOB (selected): <span class="nojob"></span></b>
                </div>
                <div>
                    {{-- <button  data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-sm btn-info">Cetak SI</button> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" id="table-order" style="font-size:.7rem">
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
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header" style="gap:10px" id="bttb-info">
                        <div class="p-2 d-flex" style="gap:10px" id="bttb-info">
                            <button class="py-2 px-3 btn btn-sm btn-success" id="tambah-bttb"><i class="fas fa-plus"></i> Tambah
                                BTTB</button>
                            <a class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-print"><i class="fas fa-print"></i> Print BTTB</a>
                            <a class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-kubikasi-print"><i class="fas fa-print"></i> Print BTTB Kubikasi</a>
                            <a class="py-2 px-3 btn btn-sm btn-info" style="font-size: .7rem" id="edit-bttb"><i class="fas fa-pencil"></i> Edit</a>
                            <button class="py-2 px-3 btn btn-sm btn-danger" style="font-size: .7rem" id="delete-bttb"><i class="fas fa-trash"></i> Hapus</button>
                            <b>N0. JOB (selected): <span class="nojob"></span></b>
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
        <h5 class="offcanvas-title" id="offcanvasOrderLabel">Form Order</h5>
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

<div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasBTTB" aria-labelledby="offcanvasBTTBLabel">
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
                <button type="button" class="btn btn-success btn-sm" id="add-bttb">Simpan</button>
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
@endsection

@section('script')
<script src="{{asset('assets/js/autocomplete.js')}}"></script>
<script>
    $('#edit-order').hide();
    $('#btn-tagihan').hide();
    $('#delete-order').hide();
    $(document).ready(function() {
        $('#create select[name=pengirim_id]').select2(
            {
                dropdownParent: $('#offcanvasOrder'),
                ajax: {
                    url: '/api/get-pengirim',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );
        $('#form-bttb select[name=pengirim_id]').select2(
            {
                dropdownParent: $('#offcanvasBTTB'),
                ajax: {
                    url: '/api/get-pengirim',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );
    });
    $(document).ready(function() {
        $("#create select[name=penerima_id]").select2(
            {
                dropdownParent: $('#offcanvasOrder'),
                ajax: {
                    url: '/api/get-pengirim',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );
    });
    $(document).ready(function() {
        $("#create select[name=penerima_bl_id]").select2(
            {
                dropdownParent: $('#offcanvasOrder'),
                ajax: {
                    url: '/api/get-pengirim',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );
    });
    $(document).ready(function() {
        $("#create select[name=barang_id]").select2(
            {
                dropdownParent: $('#offcanvasOrder'),
                ajax: {
                    url: '/api/get-barang',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );
    });
</script>
<script>
    $(function() {
        var barang = @json($barang);
        var satuan = @json($satuan);
        var customers = @json($customers);
        autocomplete(document.getElementById("barang_id"), barang);
        autocomplete(document.getElementById("selectBarang"), barang);
        autocomplete(document.getElementById("satuan_id"), satuan);
        autocomplete(document.getElementById("pengirim_bttb"), customers);
        autocomplete(document.getElementById("pengirim_id"), customers);
        autocomplete(document.getElementById("penerima_id"), customers);
    });
</script>
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script>
    $('#bttb-info').hide();
    $('#ag').hide();
    $('#copy-order').hide();
    $('#packing-list').hide();
    $('#packing-list-kubikasi').hide();
        let id = null;
        $('#table-order thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#table-order thead');

        let tableOrder = $('#table-order').DataTable({
            processing: true,
            serverSide: true,
            scrollY:        200,
            deferRender:    true,
            scroller:       true,
            select:true,
            scrollX:true,
            ordering:false,
            ajax:{
                url: '{{ route('order.data') }}',
                method:'POST',
                data:{filter:@json(request('filter-order'))},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                // {data: '#', name:'search', orderable: false, searchable: false },
                // { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'tools', name: 'tools', orderable: false, searchable: false, visible:false },
                { data: 'id', name: 'id', visible:false },
                { data: 'created_at', name: 'created_at' },
                { data: 'invoice', name: 'order.invoice' },
                { data: 'job', name: 'order.job' },
                { data: 'no_job', name: 'no_job', searchable:false },
                { data: 'asuransi', name: 'order.asuransi' },
                { data: 'pembayar', name: 'pembayar.nama' },
                { data: 'marketing', name: 'marketing.name' },
                { data: 'cs', name: 'cs.name' },
                { data: 'pengirim', name: 'pengirim.nama' },
                { data: 'penerima', name: 'penerima.nama' },
                { data: 'dari', name: 'tarif.dari' },
                { data: 'tujuan', name: 'tarif.tujuan' },
                { data: 'shipment', name: 'shipments.nama' },
                { data: 'kondisi', name: 'kondisi.nama' },
                { data: 'barang', name: 'barang.nama' },
                { data: 'barang_bttb', name: 'barang_bttb', searchable:false },
                { data: 'pelayaran', name: 'pelayaran.nama' },
                { data: 'kapal', name: 'kapal.nama' },
                { data: 'voyage', name: 'jadwal_kapal.voyage' },
                { data: 'etd', name: 'jadwal_kapal.etd' },
                { data: 'td', name: 'jadwal_kapal.td' },
                { data: 'ba_kirim', name: 'order.ba_kirim' },
                { data: 'nopol', name: 'order.nopol' },
                { data: 'trucking', name: 'order.trucking' },
                { data: 'container', name: 'order.container' },
                { data: 'seal', name: 'order.seal' },
                { data: 'stuffing', name: 'order.stuffing' },
                { data: 'stuffing_t', name: 'tarif.stuffing' },
                { data: 'full', name: 'order.full' },
                { data: 'barang_diantar', name: 'order.barang_diantar' },
                { data: 'ba_kembali', name: 'order.ba_kembali' },
                { data: 'koli', name: 'koli', searchable:false },
                { data: 'vol', name: 'vol', searchable:false },
                { data: 'berat', name: 'berat', searchable:false },
                { data: 'satuan', name: 'satuan', searchable:false },
                { data: 'unit', name: 'satuan.nama' },
                { data: 'tarif', name: 'tarif.tarif' },
                { data: 'agen', name: 'order.agen' },
                { data: 'penerima_bl', name: 'penerima_bl.nama' },
                { data: 'keterangan', name: 'order.keterangan' },
            ],
            initComplete: function () {
                var api = this.api();

                // For each column
                api
                    .columns()
                    .eq(0)
                    .each(function (colIdx) {
                        // Set the header cell to contain the input element
                        var cell = $('.filters th').eq(
                            $(api.column(colIdx).header()).index()
                        );
                        var title = $(cell).text();
                        $(cell).html('<input type="text" placeholder="' + title + '" />');

                        // On every keypress in this input
                        $(
                            'input',
                            $('.filters th').eq($(api.column(colIdx).header()).index())
                        )
                            .off('keyup change')
                            .on('change', function (e) {
                                // Get the search value
                                $(this).attr('title', $(this).val());
                                var regexr = '({search})'; //$(this).parents('th').find('select').val();

                                var cursorPosition = this.selectionStart;
                                // Search the column for that value
                                api
                                    .column(colIdx)
                                    .search(
                                        this.value != ''
                                            ? regexr.replace('{search}', '(((' + this.value + ')))')
                                            : '',
                                        this.value != '',
                                        this.value == ''
                                    )
                                    .draw();
                            })
                            .on('keyup', function (e) {
                                e.stopPropagation();

                                $(this).trigger('change');
                                $(this)
                                    .focus()[0]
                                    // .setSelectionRange(cursorPosition, cursorPosition);
                            });
                    });
            },

        });

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
                { data: 'barang_id', name: 'barang_id' },
                { data: 'qty', name: 'qty' },
                { data: 'satuan_id', name: 'satuan_id' },
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

        $('#table-order tbody').on( 'click', 'tr', function () {
            $('#btn-tagihan').show();
            $('#bttb-info').show();
            $('#edit-order').show();
            $('#delete-order').show();
            $('#copy-order').show();
            $('#packing-list').show();
            $('#packing-list-kubikasi').show();
            id =  tableOrder.row( this ).data().id;
            var no_job =  tableOrder.row( this ).data().no_job;
            $('#order_id_bttb').val(id);
            $('.nojob').html(no_job);
            $('#bttb-print').attr('href','{{ route('cetak.bttb') }}?order_id='+id);
            $('#edit-order').attr('href','{{ url('admin/order') }}/'+id+'/edit');
            $('#packing-list').attr('href','{{ url('admin/cetak/packing-list') }}/?order_id='+id);
            $('#packing-list-kubikasi').attr('href','{{ url('admin/cetak/packing-list-kubikasi') }}/?order_id='+id);
            $('#delete-order').attr('action','{{ url('admin/order') }}/'+id);
            $('#copy-order').attr('action','{{ url('admin/copy-orders') }}/'+id);
            $('#bttb-kubikasi-print').attr('href','{{ route('cetak.bttb.kubikasi') }}?order_id='+id);
            tablebttb.ajax.reload();
            tableTagihan.ajax.reload();
        })

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
                $("select[name=agen_id]").select2({
                    dropdownParent: $('#offcanvasOrder')
                });
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
                vol = vol.toFixed(2);
            }
            $('#vol').val(vol);
        }

        $('#add-bttb').click(function (e) {
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
                            barang_id : $('#barang_id').val(),
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
                                    $('#barang_id').val('');
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
            $('#barang_id').val(data.barang_id);
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
            var myOffcanvas = document.getElementById('offcanvasBTTB');
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
            $('#barang_id').val('');
            $('#satuan_id').val('');
            $('#p').val('');
            $('#l').val('');
            $('#t').val('');
            $('#vol').val('');
            $('#berat').val('');
            $('#pengirim_bttb').val('');
            $('#keterangan-bttb').val('');
            var myOffcanvas = document.getElementById('offcanvasBTTB');
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
</script>
@endsection
