@extends('layouts.admin')
@section('content')
<div class="horizontal-menu">
    <div class="d-flex gap-2 flex-nowrap" style="overflow-x:auto">
        <div class="sub-menu">
            <a href="{{ route('agen.index') }}" class="btn-link p-3">Agen <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('pelayaran.index') }}" class="btn-link p-3">Pelayaran <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('truk.index') }}" class="btn-link p-3">Truk <span class="nav-link-icon"></span></span></a>
        </div>
    </div>
</div>
<div class="content-main">
    <div class="card">
        <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
            <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAgen" aria-controls="offcanvasAgen">Tambah Agen</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm" style="font-size:.7rem">
                    <thead>
                        <tr>
                            <th>ID.</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Pic</th>
                            <th>Alamat</th>
                            <th>Kota</th>
                            <th>Telp</th>
                            <th>HP</th>
                            <th>Fax</th>
                            <th>Email</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasAgen" aria-labelledby="offcanvasAgenLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasAgenLabel">Form Agen</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('agen.store') }}" method="post">
                @csrf
                @include('admin.suplier.agen.form',['agen'=>[]])
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
                url: '{{ route('agen.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
            { data: 'kode', name: 'kode' },
            { data: 'nama', name: 'nama' },
            { data: 'pic', name: 'pic' },
            { data: 'alamat', name: 'alamat' },
            { data: 'kota', name: 'kota' },
            { data: 'telp', name: 'telp' },
            { data: 'hp', name: 'hp' },
            { data: 'fax', name: 'fax' },
            { data: 'email', name: 'email' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection
