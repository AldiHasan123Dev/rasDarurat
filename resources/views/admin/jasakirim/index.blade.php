@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJasaKirim" aria-controls="offcanvasJasaKirim">Tambah JasaKirim</button> --}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>ID.</th>
                                        <th>Tujuan</th>
                                        <th>JOB</th>
                                        <th>Barcode</th>
                                        <th>Tgl Kirim</th>
                                        <th>Tgl Terima</th>
                                        <th>Nominal</th>
                                        <th>Ekspedisi</th>
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
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasJasaKirim" aria-labelledby="offcanvasJasaKirimLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasJasaKirimLabel">Form JasaKirim</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('jasakirim.store') }}" method="post">
                @csrf
                @include('admin.jasakirim.form')
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
                url: '{{ route('jasakirim.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'lokasi_id', name: 'lokasi_id' },
                { data: 'orders', name: 'orders' },
                { data: 'barcode', name: 'barcode' },
                { data: 'tgl_kirim', name: 'tgl_kirim' },
                { data: 'tgl_terima', name: 'tgl_terima' },
                { data: 'nominal', name: 'nominal' },
                { data: 'ekspedisi', name: 'ekspedisi' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection
