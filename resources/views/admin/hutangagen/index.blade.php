@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <form action="{{ route('hutang-agen.draf') }}" method="POST" class="card">
            @csrf
            <div class="card-header p-3 d-flex justify-content-between" style="gap:10px">
                <div class="d-flex gap-3">
                    <button class="py-2 px-3 btn btn-success" type="submit">Buat Draf</button>
                    <a href="{{ route('hutang-agen.list') }}" class="py-2 px-3 btn btn-primary">List Jurnal</a>
                </div>
                <h5>List Hutang Agen</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="height: 450px">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>Agen</th>
                                <th>#</th>
                                <th>JOB</th>
                                <th>Invoice</th>
                                <th>Pembayar</th>
                                <th>Container</th>
                                <th>Seal</th>
                                <th>Tujuan</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $agen_id)
                                @foreach ($agen_id as $order)
                                    <tr>
                                        @if ($loop->first)
                                            <td class="fw-bold" rowspan="{{ $agen_id->count() }}">
                                                {{ $agen_id->first()->agent->nama }}
                                            </td>
                                        @endif
                                        <td>
                                            <input type="checkbox" name="order_id[]" id="order-{{ $order->id }}" value="{{ $order->id }}">
                                        </td>
                                        <td>{{ $order->job }}-{{ sprintf('%02d', $order->no_job) }}</td>
                                        <td>{{ $order->invoice ?? '-' }}</td>
                                        <td>{{ $order->tarif->customer->nama }}</td>
                                        <td>{{ $order->container }}</td>
                                        <td>{{ $order->seal }}</td>
                                        <td>{{ $order->tarif->tujuan_lokasi->nama }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
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
