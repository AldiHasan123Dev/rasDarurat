@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasHutangAgen" aria-controls="offcanvasHutangAgen">Tambah Hutang Agen</button> --}}
                <h5>List Hutang Agen</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Agen</th>
                                <th>JOB</th>
                                <th>Harga</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('hutangagen.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'agen_id', name: 'agen_id' },
                { data: 'order_id', name: 'order_id' },
                { data: 'harga', name: 'harga' },
                // { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection
