@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarifPelayaran" aria-controls="offcanvasTarifPelayaran">Tambah TarifPelayaran</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Pelayaran</th>
                                <th>Tanggal</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Shipment</th>
                                <th>Tarif</th>
                                <th>Kubikasi</th>
                                <th>Keterangan</th>
                                <th>Status</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTarifPelayaran" aria-labelledby="offcanvasTarifPelayaranLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTarifPelayaranLabel">Form Tarif Pelayaran</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('tarifpelayaran.store') }}" method="post">
                @csrf
                @include('admin.tarifpelayaran.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('tarifpelayaran.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'pelayaran_id', name: 'pelayaran_id' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'dari', name: 'dari' },
                { data: 'tujuan', name: 'tujuan' },
                { data: 'tipe', name: 'tipe' },
                { data: 'tarif', name: 'tarif' },
                { data: 'kubikasi', name: 'kubikasi' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection
