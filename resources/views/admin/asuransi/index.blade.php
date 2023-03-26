@extends('layouts.admin')
@section('content')
<div class="horizontal-menu">
    <div class="d-flex gap-2 flex-nowrap" style="overflow-x:auto">
        <div class="sub-menu">
            <a href="{{ route('agen.index') }}" class="btn-link p-3 text-dark">Agen <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('pelayaran.index') }}" class="btn-link p-3 text-dark">Pelayaran <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('truk.index') }}" class="btn-link p-3 text-dark">Truk <span class="nav-link-icon"></span></span></a>
        </div>
        <div class="sub-menu">
            <a href="{{ route('asuransi.index') }}" class="btn-link p-3">Asuransi <span class="nav-link-icon"></span></span></a>
        </div>
    </div>
</div>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAsuransi" aria-controls="offcanvasAsuransi">Tambah Asuransi</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Pelayaran</th>
                                <th>Nama</th>
                                <th>Rate</th>
                                <th>Admin</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Keterangan</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasAsuransi" aria-labelledby="offcanvasAsuransiLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasAsuransiLabel">Form Asuransi</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('asuransi.store') }}" method="post">
                @csrf
                @include('admin.asuransi.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        document.oncontextmenu = new Function("return false");
        $('body').bind('cut copy paste', function(event) {
            event.preventDefault();
        });
    });
</script>
    <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('asuransi.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'pelayaran', name: 'pelayaran.nama' },
                { data: 'nama', name: 'nama' },
                { data: 'rate', name: 'rate' },
                { data: 'admin', name: 'admin' },
                { data: 'min', name: 'min' },
                { data: 'max', name: 'max' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection
