@extends('layouts.admin')
@section('style')
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
<style>
    table.dataTable tbody th, table.dataTable tbody td{
        padding: 0px 10px !important;
    }
</style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJadwalKapal" aria-controls="offcanvasJadwalKapal">Tambah JadwalKapal</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>Tools</th>
                                <th>ID.</th>
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>Pelayaran</th>
                                <th>Rute</th>
                                <th>Closing</th>
                                <th>Etd</th>
                                <th>Td</th>
                                <th>BA Kirim</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasJadwalKapal" aria-labelledby="offcanvasJadwalKapalLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasJadwalKapalLabel">Form JadwalKapal</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('jadwalkapal.store') }}" method="post">
                @csrf
                @include('admin.jadwalkapal.form',['kapal'=>$kapal,'jadwalkapal'=>[]])
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
                url: '{{ route('jadwalkapal.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'tools', name: 'tools' },
                { data: 'id', name: 'id', visible:false },
                { data: 'kapal', name: 'kapal.nama' },
                { data: 'voyage', name: 'voyage' },
                { data: 'pelayaran', name: 'pelayaran.nama' },
                { data: 'rute', name: 'rute' },
                { data: 'closing', name: 'closing' },
                { data: 'etd', name: 'etd' },
                { data: 'td', name: 'td' },
                { data: 'ba_kirim', name: 'ba_kirim' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $("select[name=kapal_id]").select2({
            dropdownParent: $('#offcanvasJadwalKapal'),
            tags:true
        });
        $("select[name=pelayaran_id]").select2({
            dropdownParent: $('#offcanvasJadwalKapal')
        });
    </script>
@endsection
