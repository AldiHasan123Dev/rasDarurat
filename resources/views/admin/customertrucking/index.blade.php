@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCustomerTrucking" aria-controls="offcanvasCustomerTrucking">Tambah CustomerTrucking</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Hp</th>
                                <th>Nik</th>
                                <th>Npwp</th>
                                <th>Nama_npwp</th>
                                <th>Alamat_npwp</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCustomerTrucking" aria-labelledby="offcanvasCustomerTruckingLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasCustomerTruckingLabel">Form CustomerTrucking</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('customertrucking.store') }}" method="post">
                @csrf
                @include('admin.customertrucking.form')
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
                url: '{{ route('customertrucking.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
            { data: 'nama', name: 'nama' },
            { data: 'alamat', name: 'alamat' },
            { data: 'hp', name: 'hp' },
            { data: 'nik', name: 'nik' },
            { data: 'npwp', name: 'npwp' },
            { data: 'nama_npwp', name: 'nama_npwp' },
            { data: 'alamat_npwp', name: 'alamat_npwp' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection