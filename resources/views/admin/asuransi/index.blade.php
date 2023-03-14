@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAsuransi" aria-controls="offcanvasAsuransi">Tambah Asuransi</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Pelayaran_id</th>
                                <th>Nama</th>
                                <th>Rate</th>
                                <th>Admin</th>
                                <th>Min</th>
                                <th>Max</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasAsuransi" aria-labelledby="offcanvasAsuransiLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasAsuransiLabel">Form Asuransi</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('asuransi.store') }}" method="post">
                @csrf
                @include('admin.asuransi.form')
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
                url: '{{ route('asuransi.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
            { data: 'pelayaran_id', name: 'pelayaran_id' },
            { data: 'nama', name: 'nama' },
            { data: 'rate', name: 'rate' },
            { data: 'admin', name: 'admin' },
            { data: 'min', name: 'min' },
            { data: 'max', name: 'max' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection