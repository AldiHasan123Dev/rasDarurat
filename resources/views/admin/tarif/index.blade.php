@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarif" aria-controls="offcanvasTarif">Tambah Tarif</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Jadwal Kapal</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Shipment</th>
                                <th>Kondisi</th>
                                <th>Satuan</th>
                                <th>Tarif</th>
                                <th>Keterangan</th>
                                <th>Unit</th>
                                <th>Min qty</th>
                                <th>Customer</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTarif" aria-labelledby="offcanvasTarifLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTarifLabel">Form Tarif</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('tarif.store') }}" method="post">
                @csrf
                @include('admin.tarif.form',['tarif'=>[]])
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('tarif.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'jadwal_kapal_id', name: 'jadwal_kapal_id' },
                { data: 'dari', name: 'dari' },
                { data: 'tujuan', name: 'tujuan' },
                { data: 'shipment', name: 'shipment' },
                { data: 'kondisi', name: 'kondisi' },
                { data: 'satuan', name: 'satuan' },
                { data: 'tarif', name: 'tarif' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'unit', name: 'unit' },
                { data: 'min_qty', name: 'min_qty' },
                { data: 'customer_id', name: 'customer_id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
        $("select[name=customer_id]").select2({
            dropdownParent: $('#offcanvasTarif')
        });
        $("select[name=jadwal_kapal_id]").select2({
            dropdownParent: $('#offcanvasTarif')
        });
        $("select[name=dari]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=tujuan]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=shipment]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=kondisi]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=satuan]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
    </script>
@endsection
