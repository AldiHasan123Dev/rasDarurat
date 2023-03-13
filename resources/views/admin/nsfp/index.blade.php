@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNSFP" aria-controls="offcanvasNSFP">Tambah NSFP</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>No.</th>
                                <th>NSFP</th>
                                <th>Keterangan</th>
                                <th>Available</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasNSFP" aria-labelledby="offcanvasNSFPLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNSFPLabel">Form NSFP</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('nsfp.store') }}" method="post">
                @csrf
                @include('admin.nsfp.form')
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
                url: '{{ route('nsfp.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id', visible:false },
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                { data: 'nomor', name: 'nomor' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'available', name: 'available' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection
