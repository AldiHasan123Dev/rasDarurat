@extends('layouts.admin')
<style>
    .table-reminder {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.70rem;
        margin-top: 10px;
        background-color: #fdf9f3;
        border: 3px solid #d6d6d6;
    }

    .table-reminder th {
        background-color: #f3c674;
        color: #000;
        padding: 8px;
        text-align: left;
        border-bottom: 4px solid #bbb;
    }

    .table-reminder td {
        padding: 4px;
        border-bottom: 3px solid #ddd;
        vertical-align: top;
    }

    .table-reminder tr:nth-child(even) {
        background-color: #fff8e1;
    }

    .table-reminder ul {
        padding-left: 18px;
        margin: 0;
    }

    .table-reminder li {
        margin-bottom: 3px;
    }
</style>

@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasKendaraan"
                    aria-controls="offcanvasKendaraan">Tambah Kendaraan</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Tanggal</th>
                                <th>Nopol</th>
                                <th>Milik</th>
                                <th>Status</th>
                                <th>Warna</th>
                                <th>Tahun</th>
                                {{-- <th>PKB</th> --}}
                                <th>Masa PKB</th>
                                <th>KIR</th>
                                <th>STID</th>
                                <th>No. Rangka</th>
                                <th>No. Mesin</th>
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


    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-4">Reminders Kendaraan</h4>

                @if (count($reminders) > 0)
                    <table class="table-reminder">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nopol</th>
                                <th>No Rangka</th>
                                <th>No Mesin</th>
                                <th>Masa PKB</th>
                                <th>KIR</th>
                                <th>STID</th>
                                <th>Milik</th>
                                <th>Pesan Reminder</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reminders as $index => $item)
                                <form action="{{ route('kendaraan.mass-update') }}" method="POST">
                                    @csrf
                                    <tr>
                                        <td>{{ $item['id'] }}</td>
                                        <td>{{ $item['nopol'] }}</td>
                                        <td>{{ $item['no_rangka'] }}</td>
                                        <td>{{ $item['no_mesin'] }}</td>

                                        {{-- input ID tersembunyi untuk update --}}
                                        <input type="hidden" name="items[{{ $index }}][id]"
                                            value="{{ $item['id'] }}">

                                        <td>
                                            <input type="date" name="items[{{ $index }}][masa_pkb]"
                                                value="{{ $item['masa_pkb'] }}">
                                        </td>
                                        <td>
                                            <input type="date" name="items[{{ $index }}][kir]"
                                                value="{{ $item['kir'] }}">
                                        </td>
                                        <td>
                                            <input type="date" name="items[{{ $index }}][stid]"
                                                value="{{ $item['stid'] }}">
                                        </td>
                                        <td>{{ $item['milik'] }}</td>
                                        <td>
                                            <ul class="mb-0">
                                                @foreach ($item['reminder'] as $r)
                                                    <li>{{ $r }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            <select name="items[{{ $index }}][is_active]" class="form-select form-select-sm status-dropdown" data-index="{{ $index }}">
                                                <option value="1" {{ $item['status'] == 1 ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ $item['status'] == 0 ? 'selected' : '' }}>Nonaktif</option>
                                            </select>
                                        </td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Update Reminder</button>
                    </div>
                @else
                    <div class="alert alert-success">Tidak ada kendaraan dengan reminder saat ini.</div>
                @endif
            </div>
        </div>
    </div>
    </form>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasKendaraan" aria-labelledby="offcanvasKendaraanLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasKendaraanLabel">Form Kendaraan</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('kendaraan.store') }}" method="post">
                @csrf
                @include('admin.kendaraan.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            scrollY: '50vh',
            ajax: {
                url: '{{ route('kendaraan.data') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'nopol',
                    name: 'nopol'
                },
                {
                    data: 'milik',
                    name: 'milik'
                },
                {
                    data: 'is_active',
                    name: 'is_active'
                },
                {
                    data: 'warna',
                    name: 'warna'
                },
                {
                    data: 'tahun',
                    name: 'tahun'
                },
                // { data: 'pkb', name: 'pkb' },
                {
                    data: 'masa_pkb',
                    name: 'masa_pkb'
                },
                {
                    data: 'kir',
                    name: 'kir'
                },
                {
                    data: 'stid',
                    name: 'stid'
                },
                {
                    data: 'no_rangka',
                    name: 'no_rangka'
                },
                {
                    data: 'no_mesin',
                    name: 'no_mesin'
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    </script>
@endsection
