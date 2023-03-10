@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
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
                <div class="d-flex" style="gap:10px">
                    @if (!request('filter-order'))
                    <button class="py-2 px-3 btn btn-sm btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrder" aria-controls="offcanvasOrder">Tambah Order</button>
                    @endif
                    <a href="" id="edit-order" class="py-2 px-3 btn btn-sm btn-primary">Edit Order</a>
                    <form action="" id="delete-order" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('DELETE')
                        <button class="py-2 px-3 btn btn-sm btn-danger" type="submit" onclick="return confirm('Are you sure?')">Hapus Order</button>
                    </form>
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
                    <div class="card-header p-2 d-flex" style="gap:10px" id="bttb-info">
                        <button class="py-2 px-3 btn btn-sm btn-success" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasBTTB" aria-controls="offcanvasBTTB"><i class="fas fa-plus"></i> Tambah
                            BTTB</button>
                        <a class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-print"><i
                                class="fas fa-print"></i> Print BTTB</a>
                        <a class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-kubikasi-print"><i
                                class="fas fa-print"></i> Print BTTB Kubikasi</a>
                        <b>N0. JOB (selected): <span class="nojob"></span></b>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm nowrap" id="table-bttb" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>No.</th>
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
        </form>
    </div>
</div>

<div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasBTTB" aria-labelledby="offcanvasBTTBLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasBTTBLabel">Form BTTB</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('bttb.store') }}" method="post" id="form-bttb">
            @csrf
            <div id="message" class="my-3 text-center text-white alert alert-success py-2 px-5"></div>
            <input type="hidden" name="order_id" id="order_id_bttb">
            @include('admin.bttb.form', ['bttb'=>[]])
            <div class="col-12 mb-2 px-1">
                <button type="button" class="btn btn-success btn-sm" id="add-bttb">Tambah Data</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
    $('#edit-order').hide();
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

<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script>
    $('#bttb-info').hide();
    $('#ag').hide();
        let id = null;
        let tableOrder = $('#table-order').DataTable({
            processing: true,
            serverSide: true,
            // scrollY: '50vh',
            // scrollCollapse: true,
            ajax:{
                url: '{{ route('order.data') }}',
                method:'POST',
                data:{filter:@json(request('filter-order'))},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                // { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'tools', name: 'tools', orderable: false, searchable: false },
                { data: 'id', name: 'id', visible:false },
                { data: 'invoice', name: 'order.invoice' },
                { data: 'job', name: 'order.job' },
                { data: 'no_job', name: 'no_job', searchable:false },
                { data: 'asuransi', name: 'order.asuransi' },
                { data: 'pembayar', name: 'pembayar.nama' },
                { data: 'marketing', name: 'name', searchable:false },
                { data: 'cs', name: 'name', searchable:false },
                { data: 'pengirim', name: 'pengirim.nama' },
                { data: 'penerima', name: 'penerima.nama' },
                { data: 'dari', name: 'tarif.dari' },
                { data: 'tujuan', name: 'tarif.tujuan' },
                { data: 'shipment', name: 'shipments.nama' },
                { data: 'kondisi', name: 'kondisi.nama' },
                { data: 'barang', name: 'barang.nama' },
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
                { data: 'satuan', name: 'satuan', searchable:false },
                { data: 'unit', name: 'satuan.nama' },
                { data: 'tarif', name: 'tarif.tarif' },
                { data: 'agen', name: 'order.agen' },
                { data: 'penerima_bl', name: 'penerima_bl.nama' },
                { data: 'keterangan', name: 'order.keterangan' },
            ],
            select:true
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
                // { data: 'id', name: 'id' },
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
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
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $('#table-order tbody').on( 'click', 'tr', function () {
            $('#bttb-info').show();
            $('#edit-order').show();
            $('#delete-order').show();
            id =  tableOrder.row( this ).data().id;
            var no_job =  tableOrder.row( this ).data().no_job;
            $('#order_id_bttb').val(id);
            $('.nojob').html(no_job);
            $('#bttb-print').attr('href','{{ route('cetak.bttb') }}?order_id='+id);
            $('#edit-order').attr('href','{{ url('admin/order') }}/'+id+'/edit');
            $('#delete-order').attr('action','{{ url('admin/order') }}/'+id);
            $('#bttb-kubikasi-print').attr('href','{{ route('cetak.bttb.kubikasi') }}?order_id='+id);
            tablebttb.ajax.reload();
        })

        $("select[name=tarif_id]").select2({
            dropdownParent: $('#offcanvasOrder')
        });
        // $("select[name=satuan]").select2({
        //     dropdownParent: $('#offcanvasOrder'),
        //     tags:true
        // });
        // $("select[name=pengirim_id]").select2({
        //     dropdownParent: $('#offcanvasOrder')
        // });
        // $("select[name=penerima_id]").select2({
        //     dropdownParent: $('#offcanvasOrder')
        // });
        // $("select[name=barang_id]").select2({
        //     dropdownParent: $('#offcanvasOrder'),
        //     tags:true
        // });

        // $("#form-bttb #pengirim_id").select2({
        //     dropdownParent: $('#offcanvasBTTB')
        // });
        $("select[name=satuan_id]").select2({
            dropdownParent: $('#offcanvasBTTB'),
            tags:true
        });
        $("select[name=barang_id]").select2({
            dropdownParent: $('#offcanvasBTTB'),
            tags:true
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
            if(p>0&&l>0&&t>0){
                vol = (p*l*t)/1000000
            }
            $('#vol').val(vol);
        }

        $('#add-bttb').click(function (e) {
            var data = {
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
                pengirim_id : $('#pengirim_id').val(),
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
        });

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
