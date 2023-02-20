@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCustomer" aria-controls="offcanvasCustomer">Tambah Customer</button>
                <form action="{{ route('customer.index') }}" method="get">
                    <span style="font-size: .8rem; margin-right:10px">Filter Tipe Customer:</span>
                    <div class="d-flex" style="gap:15px">
                        <label for="all">
                            <input type="radio" name="tipe" {{ $tipe=='all'?'checked':'' }} id="all" value="all" onchange="this.form.submit()"> Semua
                        </label>
                        <label for="pembayar">
                            <input type="radio" name="tipe" {{ $tipe=='pembayar'?'checked':'' }} id="pembayar" value="pembayar" onchange="this.form.submit()"> Pembayar
                        </label>
                        <label for="penerima">
                            <input type="radio" name="tipe" {{ $tipe=='penerima'?'checked':'' }} id="penerima" value="penerima" onchange="this.form.submit()"> Penerima
                        </label>
                        <label for="pengirim">
                            <input type="radio" name="tipe" {{ $tipe=='pengirim'?'checked':'' }} id="pengirim" value="pengirim" onchange="this.form.submit()"> Pengirim
                        </label>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="customer" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Nama</th>
                                <th>Marketing</th>
                                <th>CS</th>
                                <th>PIC</th>
                                <th>Alamat</th>
                                <th>Kota</th>
                                <th>Telp</th>
                                <th>Fax</th>
                                <th>Email</th>
                                <th>Tipe</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $cus)
                                <tr>
                                    <td>{{ $cus->id }}</td>
                                    <td>{{ $cus->nama }}</td>
                                    <td>{{ $cus->marketing->nama ?? '-' }}</td>
                                    <td>{{ $cus->cs->nama ?? '-' }}</td>
                                    <td>{{ $cus->pic ?? '-' }}</td>
                                    <td>{{ $cus->alamat ?? '-' }}</td>
                                    <td>{{ $cus->kota ?? '-' }}</td>
                                    <td>{{ $cus->telp ?? '-' }}</td>
                                    <td>{{ $cus->fax ?? '-' }}</td>
                                    <td>{{ $cus->email ?? '-' }}</td>
                                    <td>{{ $cus->tipe ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('customer.destroy',$cus) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure?')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                                            </form>
                                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCustomerUpdate" aria-controls="offcanvasCustomerUpdate"><i class="fas fa-pencil"></i></button>
                                        </div>

                                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCustomerUpdate" aria-labelledby="offcanvasCustomerUpdateLabel">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="offcanvasCustomerUpdateLabel">Form Customer</h5>
                                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                            </div>
                                            <div class="offcanvas-body">
                                                <form action="{{ route('customer.update',$cus) }}" method="post">
                                                    @csrf
                                                    @method('PUT')
                                                    @include('admin.customer.form', ['cus'=>$cus])
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarif" aria-controls="offcanvasTarif">Tambah Tarif</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="tarif" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Jadwal Kapal</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Shipment</th>
                                <th>Kondisi</th>
                                <th>Satuan</th>
                                <th>Keterangan</th>
                                <th>Unit</th>
                                <th>Min qty</th>
                                <th>Customer</th>
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


    {{-- Create --}}
    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCustomer" aria-labelledby="offcanvasCustomerLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasCustomerLabel">Form Customer</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('customer.store') }}" method="post">
                @csrf
                @include('admin.customer.form')
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTarif" aria-labelledby="offcanvasTarifLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTarifLabel">Form Tarif</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('tarif.store') }}" method="post">
                @csrf
                @include('admin.tarif.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let id = null;
        var tablecus = $('#customer').DataTable();
        let tabletar = $('#tarif').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('tarif.data') }}',
                method:'POST',
                data:function( d) {
                    d.customer_id = id;
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'jadwal_kapal_id', name: 'jadwal_kapal_id' },
                { data: 'dari', name: 'dari' },
                { data: 'tujuan', name: 'tujuan' },
                { data: 'shipment', name: 'shipment' },
                { data: 'kondisi', name: 'kondisi' },
                { data: 'satuan', name: 'satuan' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'unit', name: 'unit' },
                { data: 'min_qty', name: 'min_qty' },
                { data: 'customer_id', name: 'customer_id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
        $("select[name=customer_id]").select2({
            dropdownParent: $('#offcanvasTarif')
        });
        $("select[name=jadwal_kapal_id]").select2({
            dropdownParent: $('#offcanvasTarif')
        });
        $("select[name=dari]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=tujuan]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=shipment]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=kondisi]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=satuan]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $('#customer tbody').on( 'click', 'tr', function () {
            id =  tablecus.row( this ).data()[0];
            tabletar.ajax.reload()
            console.log(id);
        });


    </script>
@endsection
