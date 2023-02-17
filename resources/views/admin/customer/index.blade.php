@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCustomer" aria-controls="offcanvasCustomer">Tambah Customer</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="table" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>No.</th>
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
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->nama }}</td>
                                    <td>{{ $customer->marketing->nama ?? '-' }}</td>
                                    <td>{{ $customer->cs->nama ?? '-' }}</td>
                                    <td>{{ $customer->pic ?? '-' }}</td>
                                    <td>{{ $customer->alamat ?? '-' }}</td>
                                    <td>{{ $customer->kota ?? '-' }}</td>
                                    <td>{{ $customer->telp ?? '-' }}</td>
                                    <td>{{ $customer->fax ?? '-' }}</td>
                                    <td>{{ $customer->email ?? '-' }}</td>
                                    <td>{{ $customer->tipe ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('customer.destroy',$customer) }}" method="post">
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
                                                <form action="{{ route('customer.update',$customer) }}" method="post">
                                                    @csrf
                                                    @method('PUT')
                                                    @include('admin.customer.form', ['cus'=>$customer])
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
@endsection
