@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                @if (!request('filter-order'))
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrder" aria-controls="offcanvasOrder">Tambah Order</button>
                @endif
                <form action="{{ route('order.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" id="file" onchange="submit()">
                </form>
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
                                <th>Marketing</th>
                                <th>CS</th>
                                <th>Pembayar</th>
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
                                <th>Tgl Full</th>
                                <th>Barang Diantar</th>
                                <th>BA Kembali</th>
                                <th>Satuan</th>
                                <th>Unit</th>
                                <th>Tarif</th>
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

        <div class="row">
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex" style="gap:10px" id="bttb-info">
                        <button class="py-2 px-3 btn btn-sm btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBTTB" aria-controls="offcanvasBTTB"><i class="fas fa-plus"></i> Tambah BTTB</button>
                        <a class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-print"><i class="fas fa-print"></i> Print BTTB</a>
                        <a class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" id="bttb-kubikasi-print"><i class="fas fa-print"></i> Print BTTB Kubikasi</a>
                        <b>N0. JOB : <span id="nojob"></span></b>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="table-bttb" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>ID.</th>
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


    <div class="offcanvas offcanvas-bottom" tabindex="-2" id="offcanvasOrder" aria-labelledby="offcanvasOrderLabel" style="height:700px">
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
            <form action="{{ route('bttb.store') }}" method="post">
                @csrf
                <input type="hidden" name="order_id" id="order_id_bttb">
                @include('admin.bttb.form', ['bttb'=>[]])
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
    <script>
        $('#bttb-info').hide();
        let id = null;
        let tableOrder = $('#table-order').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('order.data') }}',
                method:'POST',
                data:{filter:@json(request('filter-order'))},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'tools', name: 'tools', orderable: false, searchable: false },
                { data: 'id', name: 'id', visible:false },
                { data: 'invoice', name: 'invoice' },
                { data: 'job', name: 'job' },
                { data: 'no_job', name: 'no_job' },
                { data: 'marketing', name: 'marketing' },
                { data: 'cs', name: 'cs' },
                { data: 'pembayar', name: 'pembayar' },
                { data: 'pengirim', name: 'customers.nama' },
                { data: 'penerima', name: 'cus.nama' },
                { data: 'dari', name: 'dari' },
                { data: 'tujuan', name: 'tujuan' },
                { data: 'shipment', name: 'shipment' },
                { data: 'kondisi', name: 'kondisi' },
                { data: 'barang', name: 'barang' },
                { data: 'pelayaran', name: 'pelayaran' },
                { data: 'kapal', name: 'kapal' },
                { data: 'voyage', name: 'voyage' },
                { data: 'etd', name: 'etd' },
                { data: 'td', name: 'td' },
                { data: 'ba_kirim', name: 'ba_kirim' },
                { data: 'nopol', name: 'nopol' },
                { data: 'trucking', name: 'trucking' },
                { data: 'container', name: 'container' },
                { data: 'seal', name: 'seal' },
                { data: 'stuffing', name: 'stuffing' },
                { data: 'full', name: 'full' },
                { data: 'barang_diantar', name: 'barang_diantar' },
                { data: 'ba_kembali', name: 'ba_kembali' },
                { data: 'satuan', name: 'satuan' },
                { data: 'unit', name: 'unit' },
                { data: 'tarif', name: 'tarif' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
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
                { data: 'id', name: 'id' },
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
            id =  tableOrder.row( this ).data().id;
            var no_job =  tableOrder.row( this ).data().no_job;
            $('#order_id_bttb').val(id);
            $('#nojob').html(no_job);
            $('#bttb-print').attr('href','{{ route('cetak.bttb') }}?order_id='+id);
            $('#bttb-kubikasi-print').attr('href','{{ route('cetak.bttb.kubikasi') }}?order_id='+id);
            tablebttb.ajax.reload();
        })

        $("select[name=tarif_id]").select2({
            dropdownParent: $('#offcanvasOrder')
        });
        $("select[name=pengirim_id]").select2({
            dropdownParent: $('#offcanvasOrder')
        });
        $("select[name=penerima_id]").select2({
            dropdownParent: $('#offcanvasOrder')
        });
        $("select[name=barang_id]").select2({
            dropdownParent: $('#offcanvasOrder'),
            tags:true
        });

        $("select[name=pengirim_id]").select2({
            dropdownParent: $('#offcanvasBTTB')
        });
        $("select[name=satuan_id]").select2({
            dropdownParent: $('#offcanvasBTTB'),
            tags:true
        });
        $("select[name=barang_id]").select2({
            dropdownParent: $('#offcanvasBTTB'),
            tags:true
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
    </script>
@endsection
