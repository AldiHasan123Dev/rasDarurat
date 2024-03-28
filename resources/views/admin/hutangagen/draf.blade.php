@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <form action="{{ route('hutang-agen.store') }}" method="POST" class="card">
            @csrf
            <div class="card-header p-3 d-flex justify-content-between" style="gap:10px">
                <h5>Draf Hutang Agen | {{ $orders->first()->agent->nama }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>JOB</th>
                                <th>Pembayar</th>
                                <th>Container</th>
                                <th>Seal</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Tarif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            <input type="hidden" name="order_id[]" value="{{ $order->id }}">
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $order->job }}-{{ sprintf('%02d',$order->no_job) }}</td>
                                    <td>{{ $order->tarif->customer->nama }}</td>
                                    <td>{{ $order->container }}</td>
                                    <td>{{ $order->seal }}</td>
                                    <td>{{ $order->tarif->dari_lokasi->nama }}</td>
                                    <td>{{ $order->tarif->tujuan_lokasi->nama }}</td>
                                    <td>
                                        <select name="tarif[]" class="form-select form-select-sm" required>
                                            <option value="0">Rp. 0</option>
                                            @foreach ($tarif as $item)
                                                <option value="{{ $item->tarif }}">Rp. {{ number_format($item->tarif) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 row">
                    <div class="col-4">
                        <label for="Invoice" class="text-label">Invoice</label>
                        <input type="text" class="form-control" name="invoice" id="invoice" placeholder="Invoice" required autofocus autocomplete>
                    </div>
                    <div class="col-4">
                        <label for="tanggal" class="text-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col">
                        <button class="py-2 px-3 btn btn-success mt-4" type="submit" onclick="return confirm('Are you sure?')">Buat Jurnal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // $('table').dataTable()
    </script>
@endsection
