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
                        <div class="d-flex justify-content-between">
                            <b>List Nominal Belum Keluar</b>
                            <form action="{{ route('jasakirim.sync') }}" method="post">
                                @csrf
                                <button class="btn btn-sm btn-success" type="submit">Sinkronisasi Harga</button>
                            </form>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-sm" id="table-1" style="font-size:.7rem">
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
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJasaKirim" aria-controls="offcanvasJasaKirim">Tambah JasaKirim</button> --}}
                        <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Filter</button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <form action="{{ route('jasakirim.index') }}" method="get" class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Filter Pencarian Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <label for="start_date">Tanggal Kirim Dari</label>
                                                    <input type="date" class="form-control" name="start_date" value="{{ $start_date }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label for="end_date">Tanggal Kirim Ke</label>
                                                    <input type="date" class="form-control" name="end_date" value="{{ $end_date }}">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label for="tujuan">Tujuan</label>
                                                <select name="tujuan" id="tujuan" class="form-select">
                                                    <option value=""></option>
                                                    @foreach ($lokasi as $loc)
                                                    <option value="{{ $loc->id }}" {{ $tujuan==$loc->id?'selected':'' }}>{{ $loc->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="table-2" style="font-size:.7rem">
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
        let table = $('#table-1').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('jasakirim.data') }}',
                method:'POST',
                data:{
                    nominal:0,
                },
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
        let table1 = $('#table-2').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('jasakirim.data') }}',
                method:'POST',
                data:{
                    nominal:1,
                    start_date:@json($start_date),
                    end_date:@json($end_date),
                    tujuan:@json($tujuan),
                },
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
