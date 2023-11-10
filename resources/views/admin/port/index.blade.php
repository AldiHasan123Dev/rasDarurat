@extends('layouts.admin')
@section('content')
<div class="horizontal-menu">
    <div class="d-flex gap-2 flex-nowrap" style="overflow-x:auto">
        <div class="sub-menu">
            <a href="{{ route('shipment.index') }}" class="btn-link p-3 text-dark">Data Shipment <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('port.index') }}" class="btn-link p-3">Data Port <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('kapal.index') }}" class="btn-link p-3 text-dark">Data Kapal <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('kondisi.index') }}" class="btn-link p-3 text-dark">Data Kondisi <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('satuan.index') }}" class="btn-link p-3 text-dark">Data Satuan <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('barang.index') }}" class="btn-link p-3 text-dark">Data Barang <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('lokasi.index') }}" class="btn-link p-3 text-dark">Data Lokasi <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('pengirim.index') }}" class="btn-link p-3 text-dark">Pengirim <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('lss.index') }}" class="btn-link p-3 text-dark">LSS <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('thc.index') }}" class="btn-link p-3 text-dark">THC Tujuan <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('lain.index') }}" class="btn-link p-3 active">Lain <span class="nav-link-icon"></span></span></a>
        </div>
    </div>
</div>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasPort" aria-controls="offcanvasPort">Tambah Port</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Name</th>
                                {{-- <th>Status</th> --}}
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasPort" aria-labelledby="offcanvasPortLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasPortLabel">Form Port</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('port.store') }}" method="post">
                @csrf
                @include('admin.port.form')
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
                url: '{{ route('port.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
            // { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection
