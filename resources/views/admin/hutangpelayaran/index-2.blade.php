@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasHutangPelayaran" aria-controls="offcanvasHutangPelayaran">Tambah HutangPelayaran</button> --}}
                <h5>List Hutang Pelayaran</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Pelayaran</th>
                                <th>JOB</th>
                                <th>Jumlah</th>
                                <th>Status</th>
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
            ajax: {
                url: '{{ route('hutangpelayaran.data') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'tarif_pelayaran_id',
                    name: 'tarif_pelayaran_id'
                },
                {
                    data: 'order_id',
                    name: 'order_id'
                },
                {
                    data: 'jumlah',
                    name: 'jumlah'
                },
                {
                    data: 'status',
                    name: 'status'
                },
            ]
        });
    </script>
@endsection
