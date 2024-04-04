@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card ">
            <div class="card-header p-3 d-flex justify-content-between" style="gap:10px">
                <a href="{{ route('hutang-agen.index') }}" class="py-2 px-3 btn btn-primary">Kembali</a>
                <h5>List Hutang Agen</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="height: 450px">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jurnal</th>
                                <th>Invoice</th>
                                <th>#</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ date('d/m/y', strtotime($item->first()->created_at)) }}</td>
                                    <td>{{ $item->first()->jurnal }}</td>
                                    <td>{{ $item->first()->invoice }}</td>
                                    <td>
                                        {{-- bootstrap 5 modal --}}
                                        <button type="button" class="py-2 px-3 btn btn-primary btn-sm" style="font-size: .7rem" data-bs-toggle="modal" data-bs-target="#show{{ $loop->iteration }}">
                                            <i class="fas fa-list"></i> Detail
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="show{{ $loop->iteration }}" tabindex="-1" aria-labelledby="show{{ $loop->iteration }}Label" aria-hidden="true">
                                            <form action="{{ route('hutang-pelayaran.index') }}" method="GET" class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="show{{ $loop->iteration }}Label">{{ $item->first()->jurnal }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body row">
                                                        <div class="col-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No.</th>
                                                                            <th>ID JOB</th>
                                                                            <th>Pembayar</th>
                                                                            <th>Container</th>
                                                                            <th>Seal</th>
                                                                            <th>Dari</th>
                                                                            <th>Tujuan</th>
                                                                            <th>Tarif Agen</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($item as $hutang_agen)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $hutang_agen->order->job }}-{{ sprintf('%02d',$hutang_agen->order->no_job) }}</td>
                                                                            <td>{{ $hutang_agen->order->tarif->customer->nama }}</td>
                                                                            <td>{{ $hutang_agen->order->container }}</td>
                                                                            <td>{{ $hutang_agen->order->seal }}</td>
                                                                            <td>{{ $hutang_agen->order->tarif->dari_lokasi->nama }}</td>
                                                                            <td>{{ $hutang_agen->order->tarif->tujuan_lokasi->nama }}</td>
                                                                            <td>{{ number_format($hutang_agen->tarif) }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <h5>Add Cost</h5>
                                                            <hr>
                                                            @php
                                                                $tagihan = \App\Models\TagihanAgen::where('invoice',$item->first()->invoice)->get();
                                                            @endphp
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No.</th>
                                                                            <th>ID JOB</th>
                                                                            <th>Keterangan</th>
                                                                            <th>Beban Tagihan</th>
                                                                            <th>Jumlah</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($tagihan as $tagihan_agen)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $tagihan_agen->order->job }}-{{ sprintf('%02d',$tagihan_agen->order->no_job) }}</td>
                                                                            <td>{{ $tagihan_agen->nama}}</td>
                                                                            <td class=" text-uppercase">{{ $tagihan_agen->beban }}</td>
                                                                            <td>{{ number_format($tagihan_agen->jumlah) }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
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
@endsection

@section('script')
    <script>
        // $('table').dataTable()
    </script>
@endsection
