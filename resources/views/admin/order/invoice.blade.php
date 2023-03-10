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
                    <a href="" id="print-invoice" class="btn btn-sm btn-success">Print</a>
                    <b>N0. JOB (selected): <span class="nojob"></span></b>
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
                data:{filter:'invoice'},
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
            $('.nojob').html(no_job);
        })

</script>
@endsection
