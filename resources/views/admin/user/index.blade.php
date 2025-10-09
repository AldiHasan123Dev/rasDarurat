@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasUser" aria-controls="offcanvasUser">Tambah User</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Role</th>
                                <th>Nama</th>
                                <th>Kota Lahir</th>
                                <th>Tgl Lahir</th>
                                <th>Usia</th>
                                <th>Tgl Masuk</th>
                                <th>Lama Masuk</th>
                                <th>Email</th>
                                <th>Telp</th>
                                <th>Alamat</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasUser" aria-labelledby="offcanvasUserLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasUserLabel">Form User</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('uservaleg55.store') }}" method="post">
                @csrf
                @include('admin.user.form1',['uservaleg55'=>[]])
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
                url: '{{ route('uservaleg55.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'role', name: 'roles.name' },
                { data: 'name', name: 'name' },
                { data: 'kota_lahir', name: 'kota_lahir' },
                { data: 'tgl_lahir', name: 'tgl_lahir' },
                { data: 'usia', name: 'usia' },
                { data: 'tgl_masuk', name: 'tgl_masuk' },
                { data: 'lama_masuk', name: 'lama_masuk' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'address', name: 'address' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        function edit(id){
            console.log(id);
        }
    </script>
@endsection
