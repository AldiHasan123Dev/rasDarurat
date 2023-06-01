@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJurnal" aria-controls="offcanvasJurnal">Tambah Jurnal</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Coa_id</th>
                                <th>Order_id</th>
                                <th>Nomor</th>
                                <th>Nama</th>
                                <th>Debit</th>
                                <th>Credit</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasJurnal" aria-labelledby="offcanvasJurnalLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasJurnalLabel">Form Jurnal</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('jurnal.store') }}" method="post">
                @csrf
                @include('admin.jurnal.form')
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
                url: '{{ route('jurnal.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
            { data: 'coa_id', name: 'coa_id' },
            { data: 'order_id', name: 'order_id' },
            { data: 'nomor', name: 'nomor' },
            { data: 'nama', name: 'nama' },
            { data: 'debit', name: 'debit' },
            { data: 'credit', name: 'credit' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection