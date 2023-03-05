@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<style>
    td:hover {
        cursor: pointer;
    }
</style>
@endsection
@section('content')

    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button style="font-size: .7rem" class="btn-sm btn border-bottom border-dark" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCustomer" aria-controls="offcanvasCustomer">Tambah Customer <i class="fas fa-plus"></i></button>
                {{-- <form action="{{ route('customer.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" id="file" onchange="submit()">
                </form> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="customer" style="font-size:.7rem; white-space: nowrap;">
                        <thead>
                            <tr>
                                <th class="text-center">ID.</th>
                                <th>Nama</th>
                                <th>Marketing</th>
                                <th>CS</th>
                                <th>PIC</th>
                                <th>Alamat</th>
                                <th>Kota</th>
                                <th>Telp</th>
                                <th>HP</th>
                                <th>Fax</th>
                                <th>Email</th>
                                <th>NIK</th>
                                <th>NPWP</th>
                                <th>Nama NPWP</th>
                                <th>Alamat NPWP</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($customers as $cus)
                                <tr>
                                    <td>{{ $cus->id }}</td>
                                    <td>{{ $cus->nama }}</td>
                                    <td>{{ $cus->marketing->name ?? '-' }}</td>
                                    <td>{{ $cus->cs->name ?? '-' }}</td>
                                    <td>{{ $cus->pic ?? '-' }}</td>
                                    <td>{{ $cus->alamat ?? '-' }}</td>
                                    <td>{{ $cus->kota ?? '-' }}</td>
                                    <td>{{ $cus->telp ?? '-' }}</td>
                                    <td>{{ $cus->hp ?? '-' }}</td>
                                    <td>{{ $cus->fax ?? '-' }}</td>
                                    <td>{{ $cus->email ?? '-' }}</td>
                                    <td>{{ $cus->nik ?? '-' }}</td>
                                    <td>{{ $cus->npwp ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('customer.destroy',$cus) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure?')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                                            </form>
                                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCustomerUpdate{{ $cus->id }}" aria-controls="offcanvasCustomerUpdate{{ $cus->id }}"><i class="fas fa-pencil"></i></button>
                                        </div>

                                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCustomerUpdate{{ $cus->id }}" aria-labelledby="offcanvasCustomerUpdate{{ $cus->id }}Label">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="offcanvasCustomerUpdate{{ $cus->id }}Label">Form Customer</h5>
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
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="btn-sm btn border-bottom border-dark" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarif" aria-controls="offcanvasTarif" id="add-tarif">Tambah Tarif <i class="fas fa-plus"></i></button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" id="tarif" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Jadwal Kapal</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Shipment</th>
                                <th>Kondisi</th>
                                <th>Satuan</th>
                                <th>Tarif</th>
                                <th>Keterangan</th>
                                <th>Unit</th>
                                <th>Min qty</th>
                                <th>Customer</th>
                                <th>Status</th>
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
                <input type="file" name="file" id="file" onchange="submit()">
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
                            <th>HP</th>
                            <th>Fax</th>
                            <th>Email</th>
                            <th>NIK</th>
                            <th>NPWP</th>
                            <th>Nama NPWP</th>
                            <th>Alamat NPWP</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($customers as $cus)
                        <tr>
                            <td>{{ $cus->id }}</td>
                            <td>{{ $cus->nama }}</td>
                            <td>{{ $cus->marketing->name ?? '-' }}</td>
                            <td>{{ $cus->cs->name ?? '-' }}</td>
                            <td>{{ $cus->pic ?? '-' }}</td>
                            <td>{{ $cus->alamat ?? '-' }}</td>
                            <td>{{ $cus->kota ?? '-' }}</td>
                            <td>{{ $cus->telp ?? '-' }}</td>
                            <td>{{ $cus->hp ?? '-' }}</td>
                            <td>{{ $cus->fax ?? '-' }}</td>
                            <td>{{ $cus->email ?? '-' }}</td>
                            <td>{{ $cus->nik ?? '-' }}</td>
                            <td>{{ $cus->npwp ?? '-' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <form action="{{ route('customer.destroy',$cus) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')"
                                            class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                    <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvasCustomerUpdate{{ $cus->id }}"
                                        aria-controls="offcanvasCustomerUpdate{{ $cus->id }}"><i
                                            class="fas fa-pencil"></i></button>
                                </div>

                                <div class="offcanvas offcanvas-end" tabindex="-1"
                                    id="offcanvasCustomerUpdate{{ $cus->id }}"
                                    aria-labelledby="offcanvasCustomerUpdate{{ $cus->id }}Label">
                                    <div class="offcanvas-header">
                                        <h5 class="offcanvas-title" id="offcanvasCustomerUpdate{{ $cus->id }}Label">Form
                                            Customer</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                            aria-label="Close"></button>
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
                        @endforeach --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
            <button class="btn-sm btn border-bottom border-dark" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasTarif" aria-controls="offcanvasTarif">Tambah Tarif <i
                    class="fas fa-plus"></i></button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm nowrap" id="tarif" style="font-size:.7rem">
                    <thead>
                        <tr>
                            <th>ID.</th>
                            <th>Jadwal Kapal</th>
                            <th>Dari</th>
                            <th>Tujuan</th>
                            <th>Shipment</th>
                            <th>Kondisi</th>
                            <th>Satuan</th>
                            <th>Tarif</th>
                            <th>Keterangan</th>
                            <th>Unit</th>
                            <th>Min qty</th>
                            <th>Customer</th>
                            <th>Status</th>
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
            @include('admin.customer.form',['cus'=>[]])
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
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>

    <script>
        $('#add-tarif').hide();
        let id = null;
        let tablecus = $('#customer').DataTable({
            processing: true,
            serverSide: true,
            select:true,
            ajax:{
                url: '{{ route('customer.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nama', name: 'nama' },
                { data: 'marketing_id', name: 'users.name' },
                { data: 'cs_id', name: 'cs.name' },
                { data: 'pic', name: 'pic' },
                { data: 'alamat', name: 'alamat' },
                { data: 'kota', name: 'kota' },
                { data: 'telp', name: 'telp' },
                { data: 'hp', name: 'hp' },
                { data: 'fax', name: 'fax' },
                { data: 'email', name: 'email' },
                { data: 'nik', name: 'nik' },
                { data: 'npwp', name: 'npwp' },
                { data: 'nama_npwp', name: 'nama_npwp' },
                { data: 'alamat_npwp', name: 'alamat_npwp' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],"columnDefs": [
                { className: "text-center", "targets": [0] }
            ]
        });
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
                { data: 'tarif', name: 'tarif' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'unit', name: 'unit' },
                { data: 'min_qty', name: 'min_qty' },
                { data: 'customer_id', name: 'customer_id' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
        
        $(document).ready(function() {
            $("select[name=customer_id]").select2(
                {
                    dropdownParent: $('#offcanvasTarif'),
                    ajax: {
                        url: '/api/get-pengirim',
                        data: function (params) {
                            return {
                                cari: params.term, // text pencarian
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.items,
                                pagination: {
                                    more: (params.page * 20) < data.counts
                                }
                            };
                        },
                        minimumInputLength: 2,
                        delay: 400,
                    }
                }
            );
        });
        // $("select[name=customer_id]").select2({
        //     dropdownParent: $('#offcanvasTarif')
        // });
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
            dropdownParent: $('#offcanvasTarif')
        });
        $("select[name=satuan]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $('#customer tbody').on( 'click', 'tr', function () {
            id =  tablecus.row( this ).data().id;
            $('#add-tarif').show();
            tabletar.ajax.reload()
        });


</script>
@endsection