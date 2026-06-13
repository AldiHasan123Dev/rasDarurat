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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        <div class="d-flex" style="gap: 12px">
                            <button data-bs-toggle="modal" data-bs-target="#ba-kembali" class="btn btn-sm btn-success">Tambah Penanggungan</button>
                            <b>N0. JOB (selected): <span class="nojob"></span></b>
                        </div>
                        <p>List Order Asuransi Belum Diinput</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsives">
                            <table class="table table-sm nowrap" id="table-asuransi" style="font-size:.7rem; white-space:nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Pelayaran ID</th>
                                        <th>No.</th>
                                        <th>Group JOB</th>
                                        <th>ID JOB</th>
                                        <th>Asuransi</th>
                                        <th>Pembayar</th>
                                        <th>Pengirim</th>
                                        <th>Penerima</th>
                                        <th>Dari</th>
                                        <th>Tujuan</th>
                                        <th>Shipment</th>
                                        <th>Kondisi</th>
                                        <th>Jenis Barang</th>
                                        <th>Pelayaran</th>
                                        <th>TD</th>
                                        <th>Kapal</th>
                                        <th>Voyage</th>
                                        <th>No Container</th>
                                        <th>No Seal</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->jadwal_kapal->pelayaran_id }}</td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $order->job }}</td>
                                            <td>{{ $order->job }}-{{ sprintf('%02d',$order->no_job) }}</td>
                                            <td>{{ $order->asuransi }}</td>
                                            <td>{{ $order->tarif->customer->nama ?? '-' }}</td>
                                            <td>{{ $order->pengirim->nama ?? '-' }}</td>
                                            <td>{{ $order->penerima->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->dari_lokasi->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->tujuan_lokasi->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->shipmentInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->kondisiInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->barang->nama ?? '-' }}</td>
                                            <td>{{ $order->jadwal_kapal->pelayaran->nama }}</td>
                                            <td>{{ is_null($order->jadwal_kapal->td)?'-':date('d-m-y',strtotime($order->jadwal_kapal->td)) }}</td>
                                            <td>{{ $order->jadwal_kapal->kapal->nama ?? '-' }}</td>
                                            <td>{{ $order->jadwal_kapal->voyage }}</td>
                                            <td>{{ $order->container }}</td>
                                            <td>{{ $order->seal }}</td>
                                            <td>{{ $order->keterangan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        <div class="d-flex gap-2">
                            <form action="{{ route('asuransi.export') }}" method="post">
                                @csrf
                                <input type="hidden" name="orders_id" class="orders_id">
                                <button type="submit" class="btn btn-success btn-sm">Export Excel</button>
                            </form>
                            @if (Auth::id()==14)
                            <form action="{{ route('asuransi.tarik') }}" method="post">
                                @csrf
                                <input type="hidden" name="orders_id" class="orders_id">
                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('are you sure?')">Tarik Asuransi</button>
                            </form>
                            @endif
                            <form action="{{ route('asuransi.cetak') }}" method="post">
                                @csrf
                                <input type="hidden" name="asuransi_cetak" value="1">
                                <input type="hidden" name="orders_id" class="orders_id">
                                <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('are you sure?')">Masukan Sudah Cetak</button>
                            </form>
                        </div>
                        <p>List Order Dengan Asuransi (Belum Cetak)</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm nowrap" id="table-asuransi-before" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>Tools</th>
                                        <th>ID.</th>
                                        <th>Waktu Terakhir</th>
                                        <th>Group JOB</th>
                                        <th>ID JOB</th>
                                        <th>Asuransi Tipe</th>
                                        <th>Asuransi</th>
                                        <th>Pertanggungan Asuransi</th>
                                        <th>Pembayar</th>
                                        <th>Pengirim</th>
                                        <th>Penerima</th>
                                        <th>Penerima BL</th>
                                        <th>Dari</th>
                                        <th>Tujuan</th>
                                        <th>Shipment</th>
                                        <th>Kondisi</th>
                                        <th>Jenis Barang</th>
                                        <th>Pelayaran</th>
                                        <th>TD</th>
                                        <th>Kapal</th>
                                        <th>Voyage</th>
                                        <th>No Container</th>
                                        <th>No Seal</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        <div class="d-flex gap-2">
                            @if (Auth::id()==14)
                            <form action="{{ route('asuransi.cetak') }}" method="post">
                                @csrf
                                <input type="hidden" name="asuransi_cetak" value="0">
                                <input type="hidden" name="orders_id" class="orders_id">
                                <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('are you sure?')">Masukan Belum Cetak</button>
                            </form>
                            @endif
                        </div>
                        <p>List Order Dengan Asuransi (Sudah Cetak)</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm nowrap" id="table-asuransi-after" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>Tools</th>
                                        <th>ID.</th>
                                        <th>Waktu Terakhir</th>
                                        <th>Group JOB</th>
                                        <th>ID JOB</th>
                                        <th>Asuransi Tipe</th>
                                        <th>Asuransi</th>
                                        <th>Pertanggungan Asuransi</th>
                                        <th>Pembayar</th>
                                        <th>Pengirim</th>
                                        <th>Penerima</th>
                                        <th>Penerima BL</th>
                                        <th>Dari</th>
                                        <th>Tujuan</th>
                                        <th>Shipment</th>
                                        <th>Kondisi</th>
                                        <th>Jenis Barang</th>
                                        <th>Pelayaran</th>
                                        <th>TD</th>
                                        <th>Kapal</th>
                                        <th>Voyage</th>
                                        <th>No Container</th>
                                        <th>No Seal</th>
                                        <th>Keterangan</th>
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

<div class="modal fade" id="ba-kembali" tabindex="-1" aria-labelledby="ba-kembaliLabel" aria-hidden="true">
    <form action="" class="modal-dialog" method="post" id="form-asuransi">
        @csrf
        @method('PUT')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Asuransi <span class="nojob"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-2">
                        <label for="ba_kembali">Pelayaran</label>
                        <input type="text" id="pelayaran" class="form-control" disabled>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="tipe_asuransi">Tipe Asuransi</label>
                        <select name="tipe_asuransi" id="tipe_asuransi" class="form-control" required>
                            <option value="cont" selected>Per CONT (CONT)</option>
                            <option value="job">Group JOB (GLOBAL)</option>
                        </select>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="ba_kembali">Asuransi</label>
                        <select name="asuransi_id" id="asuransi_id" class="form-control" required>

                        </select>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="ba_kembali">Nilai Pertanggungan</label>
                        <input type="text" name="pertanggungan" id="pertanggungan" class="form-control rupiah" required>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="total_asuransi">Total</label>
                        <input type="text" name="total_asuransi" id="total_asuransi" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="asuransi_update" value="1" class="btn btn-primary" onclick="return confirm(\'are you sure?\')">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')

<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script>
    $('#bttb-info').hide();
    $('#ag').hide();
    let tableAsuransi = $('#table-asuransi').DataTable({
        select:true,
        scrollX:true,
        columnDefs: [
            { "visible": false, "targets": 0 },
            { "visible": false, "targets": 1 },
        ]
    });
        let id = null;
        let tableOrder = $('#table-asuransi-before').DataTable({
            processing: true,
            serverSide: true,
            // scrollY: '50vh',
            // scrollCollapse: true,
            ajax:{
                url: '{{ route('order.data') }}',
                method:'POST',
                data:{filter:'asuransi-before'},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                // { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'tools', name: 'tools',visible:false, orderable: false, searchable: false },
                { data: 'id', name: 'id', visible:false },
                { data: 'asuransi_date', name: 'order.asuransi_date'},
                { data: 'job', name: 'order.job' },
                { data: 'no_job', name: 'no_job', searchable:false },
                { data: 'asuransi', name: 'order.asuransi' },
                { data: 'asuransi_id', name: 'asuransi.nama' },
                { data: 'pertanggungan', name: 'order.pertanggungan' },
                { data: 'pembayar', name: 'pembayar.nama' },
                { data: 'pengirim', name: 'pengirim.nama' },
                { data: 'penerima', name: 'penerima.nama' },
                { data: 'penerima_bl', name: 'penerima_bl.nama' },
                { data: 'dari', name: 'tarif.dari' },
                { data: 'tujuan', name: 'tarif.tujuan' },
                { data: 'shipment', name: 'shipments.nama' },
                { data: 'kondisi', name: 'kondisi.nama' },
                { data: 'barang', name: 'barang.nama' },
                { data: 'pelayaran', name: 'pelayaran.nama' },
                { data: 'td', name: 'kapal.td', searchable:false },
                { data: 'kapal', name: 'kapal.nama' },
                { data: 'voyage', name: 'jadwal_kapal.voyage' },
                { data: 'container', name: 'order.container' },
                { data: 'seal', name: 'order.seal' },
                { data: 'keterangan', name: 'order.keterangan' },
            ],
            select:{
                style: 'multi'
            }
        });
        let tableOrderAfter = $('#table-asuransi-after').DataTable({
            processing: true,
            serverSide: true,
            // scrollY: '50vh',
            // scrollCollapse: true,
            ajax:{
                url: '{{ route('order.data') }}',
                method:'POST',
                data:{filter:'asuransi-after'},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                // { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'tools', name: 'tools',visible:false, orderable: false, searchable: false },
                { data: 'id', name: 'id', visible:false },
                { data: 'asuransi_date', name: 'order.asuransi_date'},
                { data: 'job', name: 'order.job' },
                { data: 'no_job', name: 'no_job', searchable:false },
                { data: 'asuransi', name: 'order.asuransi' },
                { data: 'asuransi_id', name: 'asuransi.nama' },
                { data: 'pertanggungan', name: 'order.pertanggungan' },
                { data: 'pembayar', name: 'pembayar.nama' },
                { data: 'pengirim', name: 'pengirim.nama' },
                { data: 'penerima', name: 'penerima.nama' },
                { data: 'penerima_bl', name: 'penerima_bl.nama' },
                { data: 'dari', name: 'tarif.dari' },
                { data: 'tujuan', name: 'tarif.tujuan' },
                { data: 'shipment', name: 'shipments.nama' },
                { data: 'kondisi', name: 'kondisi.nama' },
                { data: 'barang', name: 'barang.nama' },
                { data: 'pelayaran', name: 'pelayaran.nama' },
                { data: 'td', name: 'kapal.td', searchable:false },
                { data: 'kapal', name: 'kapal.nama' },
                { data: 'voyage', name: 'jadwal_kapal.voyage' },
                { data: 'container', name: 'order.container' },
                { data: 'seal', name: 'order.seal' },
                { data: 'keterangan', name: 'order.keterangan' },
            ],
            select:{
                style: 'multi'
            }
        });
        $("#jadwal_kapal_id-si").select2({
            dropdownParent: $('#exampleModal'),
        });
        $("#tujuan-si").select2({
            dropdownParent: $('#exampleModal'),
        });


        $('#table-asuransi-before tbody').on( 'click', 'tr', function () {
            let id = [];
            setTimeout(() => {
                var data =  tableOrder.rows( { selected: true } ).data();
                $.each(data, function (idx, item) {
                    id.push(item.id)
                });
                $('.orders_id').val(id);
            }, 1000);
        })

        $('#table-asuransi-after tbody').on( 'click', 'tr', function () {
            let id = [];
            setTimeout(() => {
                var data =  tableOrderAfter.rows( { selected: true } ).data();
                $.each(data, function (idx, item) {
                    id.push(item.id)
                });
                $('.orders_id').val(id);
            }, 1000);
        })

        $('#table-asuransi tbody').on( 'click', 'tr', function () {
            var id =  tableAsuransi.row( this ).data()[0];
            var pelayaran_id =  tableAsuransi.row( this ).data()[1];
            var no_job =  tableAsuransi.row( this ).data()[4];
            var pelayaran =  tableAsuransi.row( this ).data()[14];
            $('#order_id_bttb').val(id);
            $('#pelayaran').val(pelayaran);
            $('.nojob').html(no_job);
            $('#form-asuransi').attr('action','{{ url('admin/order') }}/'+id);
            $('#pertanggungan').val('');
            $('#total_asuransi').val('');
            $.ajax({
                type: "GET",
                url: "{{ url('api/get-asuransi-pelayaran') }}/"+pelayaran_id,
                success: function (response) {
                    $('#asuransi_id').html(' ');
                    let html = '';
                    $.each(response, function (idx, item) {
                        if (idx==0) {
                            html+='<option selected value="'+item.id+'" data-rate="'+item.rate+'" >'+item.nama+' ('+item.rate+'%)</option>'
                        }else{
                            html+='<option value="'+item.id+'" data-rate="'+item.rate+'" >'+item.nama+' ('+item.rate+'%)</option>'
                        }
                    });
                    $('#asuransi_id').append(html);
                }
            });
        })

        $('#asuransi_id').change(function (e) {
            hitung();
        });

        $('#pertanggungan').keyup(function (e) {
            hitung();
        });

        function hitung(){
            var rate = $('#asuransi_id').find(':selected').attr('data-rate');
            var pertanggungan = parseInt($('#pertanggungan').val().replace(/[^0-9]/g, ''));
            var total = (rate/100) * pertanggungan;
            $('#total_asuransi').val(total.toLocaleString('en-US'));
        }

</script>
@endsection
