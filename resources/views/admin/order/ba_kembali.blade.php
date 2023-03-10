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
                <div class="d-flex" style="gap: 12px">
                    <button data-bs-toggle="modal" data-bs-target="#ba-kembali" class="btn btn-sm btn-success">BA Kembali</button>
                    <b>N0. JOB (selected): <span class="nojob"></span></b>
                </div>
                <div>
                    <button data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-sm btn-info">Cetak SI</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" id="table-order" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>Tools</th>
                                <th>ID.</th>
                                <th>Group JOB</th>
                                <th>ID JOB</th>
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
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>No Container</th>
                                <th>No Seal</th>
                                <th>ETD</th>
                                <th>TD</th>
                                <th>BA Kembali</th>
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

<div class="modal fade" id="ba-kembali" tabindex="-1" aria-labelledby="ba-kembaliLabel" aria-hidden="true">
    <form action="" class="modal-dialog" method="post" id="form-ba-kembali">
        @csrf
        @method('PUT')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ba-'.$data->id.'Label">BA Kembali <span class="nojob"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-2">
                        <label for="ba_kembali">BA Kembali</label>
                        <input type="date" name="ba_kembali" class="form-control">
                    </div>
                    <div class="col-12 mb-2">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="invoice" value="1" class="btn btn-primary" onclick="return confirm(\'are you sure?\')">Simpan</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('cetak.shipment') }}" method="GET" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Buat SI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="jadwal_kapal_id-si">Kapal</label><br>
                    <select name="jadwal_kapal_id" id="jadwal_kapal_id-si" class="form-control w-100">
                        @foreach ($jadwal_kapal as $kapal)
                            <option value="{{ $kapal->id }}">{{ $kapal->kapal->nama ?? '-' }} || Voy.{{ $kapal->voyage }} || {{ $kapal->pelayaran->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb">
                    <label for="tujuan-si">Tujuan</label><br>
                    <select name="tujuan" id="tujuan-si" class="form-control w-100">
                        @foreach ($data_lokasi as $lokasi)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Buat SI</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')

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
                { data: 'tools', name: 'tools',visible:false, orderable: false, searchable: false },
                { data: 'id', name: 'id', visible:false },
                { data: 'job', name: 'order.job' },
                { data: 'no_job', name: 'no_job', searchable:false },
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
                { data: 'kapal', name: 'kapal.nama' },
                { data: 'voyage', name: 'jadwal_kapal.voyage' },
                { data: 'container', name: 'order.container' },
                { data: 'seal', name: 'order.seal' },
                { data: 'etd', name: 'jadwal_kapal.etd' },
                { data: 'td', name: 'jadwal_kapal.td' },
                { data: 'ba_kembali', name: 'order.ba_kembali' },
                { data: 'keterangan', name: 'order.keterangan' },
            ],
            select:true
        });
        $("#jadwal_kapal_id-si").select2({
            dropdownParent: $('#exampleModal'),
        });
        $("#tujuan-si").select2({
            dropdownParent: $('#exampleModal'),
        });


        $('#table-order tbody').on( 'click', 'tr', function () {
            var id =  tableOrder.row( this ).data().id;
            var no_job =  tableOrder.row( this ).data().no_job;
            $('#order_id_bttb').val(id);
            $('.nojob').html(no_job);
            $('#form-ba-kembali').attr('action','{{ url('admin/order') }}/'+id);
        })

</script>
@endsection
