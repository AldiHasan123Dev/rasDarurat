@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card ">
            <div class="card-header p-3 d-flex justify-content-between" style="gap:10px">
                <a href="{{ route('hutang-agen.index') }}" class="py-2 px-3 btn btn-primary">Kembali</a>
                <h5>List Hutang Agen</h5>
            </div>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body py-3">
                            <form method="GET" action="{{ route('hutang-agen.list') }}">

                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-calendar-alt text-primary me-1"></i>
                                    Filter Tahun
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar"></i>
                                    </span>

                                    <select name="year" class="form-select">
                                        @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                            <option value="{{ $i }}"
                                                {{ ($year ?? date('Y')) == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>

                                    <button class="btn btn-primary px-4">
                                        <i class="fas fa-filter me-1"></i>
                                        Klik
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="height: 450px">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th style="width: 90px">Jurnal</th>
                                <th>Invoice</th>
                                <th>Agent</th>
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
                                    <td>{{ implode(', ', $item->pluck('invoice')->toArray()) }}</td>
                                    <td>{{ $item->first()->agen() }}</td>
                                    <td>
                                        {{-- bootstrap 5 modal --}}
                                        <div class="d-flex gap-2">
                                            <button type="button" class="py-2 px-3 btn btn-warning btn-sm"
                                                style="font-size: .7rem" data-bs-toggle="modal"
                                                data-bs-target="#show{{ $loop->iteration }}">
                                                <i class="fas fa-list"></i> Detail
                                            </button>
                                            <a href="{{ route('hutang-agen.print', ['draf' => $item->first()->draf, 'print' => 1]) }}"
                                                class="py-2 px-3 btn btn-success btn-sm" style="font-size: .7rem">
                                                <i class="fas fa-print"></i> Print
                                            </a>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="show{{ $loop->iteration }}" tabindex="-1"
                                            aria-labelledby="show{{ $loop->iteration }}Label" aria-hidden="true">
                                            <form action="{{ route('hutang-pelayaran.index') }}" method="GET"
                                                class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="show{{ $loop->iteration }}Label">
                                                            {{ $item->first()->jurnal }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
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
                                                                            <th>Penerima</th>
                                                                            <th>Tipe</th>
                                                                            <th>Tarif</th>
                                                                            <th>Container / Seal</th>
                                                                            <th>Dari</th>
                                                                            <th>Tujuan</th>
                                                                            <th>Tarif Agen</th>
                                                                            <th>PPN (1.1%)</th>
                                                                            <th>Pot. PPH (2%)</th>
                                                                            <th>Total</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($item as $hutang_agen)
                                                                            <tr>
                                                                                <td>{{ $loop->iteration }}</td>
                                                                                <td>{{ $hutang_agen->order->job }}-{{ sprintf('%02d', $hutang_agen->order->no_job) }}
                                                                                </td>
                                                                                <td>{{ $hutang_agen->order->tarif->customer->nama }}
                                                                                </td>
                                                                                <td>{{ $hutang_agen->order->penerima->nama }}
                                                                                </td>
                                                                                <td>{{ $hutang_agen->order->tarif->shipmentInfo->nama }}
                                                                                </td>
                                                                                <td>{{ number_format($hutang_agen->order->tarif->tarif) }}
                                                                                </td>
                                                                                <td>{{ $hutang_agen->order->container }} /
                                                                                    {{ $hutang_agen->order->seal }}</td>
                                                                                <td>{{ $hutang_agen->order->tarif->dari_lokasi->nama }}
                                                                                </td>
                                                                                <td>{{ $hutang_agen->order->tarif->tujuan_lokasi->nama }}
                                                                                </td>
                                                                                <td>{{ number_format($hutang_agen->tarif) }}
                                                                                </td>
                                                                                <td>{{ number_format($hutang_agen->ppn) }}
                                                                                </td>
                                                                                <td>-
                                                                                    {{ number_format($hutang_agen->pph) }}
                                                                                </td>
                                                                                <td>{{ number_format($hutang_agen->tarif + $hutang_agen->ppn - $hutang_agen->pph) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <td colspan="12"
                                                                                class="font-bold text-center"><b>TOTAL</b>
                                                                            </td>
                                                                            <td><b>{{ number_format($item->sum('tarif') + $item->sum('ppn') - $item->sum('pph')) }}</b>
                                                                            </td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <h5>Add Cost</h5>
                                                            <hr>
                                                            @php
                                                                $tagihan = collect();

                                                                foreach ($item as $hutang) {
                                                                    if (isset($tagihanAll[$hutang->order_id])) {
                                                                        $tagihan = $tagihan->merge(
                                                                            $tagihanAll[$hutang->order_id],
                                                                        );
                                                                    }
                                                                }
                                                            @endphp
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No.</th>
                                                                            <th>JOB ORDER</th>
                                                                            <th>Tipe</th>
                                                                            <th>Keterangan</th>
                                                                            <th>Beban Tagihan</th>
                                                                            <th>Jumlah</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($tagihan as $tagihan_agen)
                                                                            <tr>
                                                                                <td>{{ $loop->iteration }}</td>
                                                                                @if ($tagihan_agen->tipe == 'satuan')
                                                                                    <td>{{ $tagihan_agen->order->job }}-{{ sprintf('%02d', $tagihan_agen->order->no_job) }}
                                                                                    </td>
                                                                                @else
                                                                                    <td>{{ $tagihan_agen->order->job }}
                                                                                        01-{{ sprintf('%02d', $tagihan_agen->order->sum_cont()) }}
                                                                                    </td>
                                                                                @endif
                                                                                <td>{{ $tagihan_agen->tipe }}</td>
                                                                                <td>{{ $tagihan_agen->nama }}</td>
                                                                                <td class=" text-uppercase">
                                                                                    {{ $tagihan_agen->beban }}</td>
                                                                                <td>{{ number_format($tagihan_agen->jumlah) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <td colspan="5"
                                                                                class="font-bold text-center"><b>TOTAL</b>
                                                                            </td>
                                                                            <td><b>{{ number_format($tagihan->sum('jumlah')) }}</b>
                                                                            </td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <h5>TOTAL</h5>
                                                            <hr>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">

                                                                    <body>
                                                                        <tr>
                                                                            <td>Total Tarif Agen</td>
                                                                            <td class="text-right text-end fw-bold">
                                                                                {{ number_format($item->sum('tarif') + $item->sum('ppn') - $item->sum('pph')) }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Total Tagihan </td>
                                                                            <td class="text-right text-end fw-bold">
                                                                                {{ number_format($tagihan->sum('jumlah')) }}
                                                                            </td>
                                                                        </tr>
                                                                    </body>
                                                                    <tfoot>
                                                                        <tr class="table-light">
                                                                            <td>Jumlah</td>
                                                                            <td class="text-right text-end fw-bold">
                                                                                {{ number_format($item->sum('tarif') + $item->sum('ppn') - $item->sum('pph') + $tagihan->sum('jumlah')) }}
                                                                            </td>
                                                                        </tr>
                                                                    </tfoot>
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
