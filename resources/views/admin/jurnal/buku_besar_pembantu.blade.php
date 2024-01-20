@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.dataTables.min.css">
    <style>
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');
            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
            }
            #print, #print * {
                visibility: visible;
                font-size: .7rem !important;
            }
            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -70px;
            }
            #table td, #table th{
                border: 1px solid black;
            }
            #print {
                color: #000;
            }
        }
        table.data th, td { white-space: nowrap; }
    </style>
@endsection
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div id="print">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width:200px">Akun :</th>
                                            <th>
                                                <form action="{{ route('jurnal.buku_besar_pembantu') }}" method="get">
                                                    <input type="hidden" name="month" value="{{ $month }}">
                                                    <input type="hidden" name="year" value="{{ $year }}">
                                                    <input type="hidden" name="coa_id" value="{{ $coa_id }}">
                                                    <select class="form-control px-3 py-1" name="coa_id" onchange="submit()" style="font-size:.8rem">
                                                        @foreach ($coas as $item)
                                                            <option {{ $coa_id == $item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width:200px">Tahun :</th>
                                            <th>
                                                <form action="{{ route('jurnal.buku_besar_pembantu',['month'=>$month,'coa_id'=>$coa_id,'year'=>$year]) }}" method="get">
                                                    <input type="hidden" name="month" value="{{ $month }}">
                                                    <input type="hidden" name="year" value="{{ $year }}">
                                                    <input type="hidden" name="coa_id" value="{{ $coa_id }}">
                                                    <select class="form-control px-3 py-1" name="year" onchange="submit()" style="font-size:.8rem">
                                                        <option {{ $year=='2023'?'selected':'' }} value="2023">2023</option>
                                                        <option {{ $year=='2024'?'selected':'' }} value="2024">2024</option>
                                                        <option {{ $year=='2025'?'selected':'' }} value="2025">2025</option>
                                                        <option {{ $year=='2026'?'selected':'' }} value="2026">2026</option>
                                                        <option {{ $year=='2027'?'selected':'' }} value="2027">2027</option>
                                                        <option {{ $year=='2028'?'selected':'' }} value="2028">2028</option>
                                                        <option {{ $year=='2029'?'selected':'' }} value="2029">2029</option>
                                                        <option {{ $year=='2030'?'selected':'' }} value="2030">2030</option>
                                                    </select>
                                                </form>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="d-flex gap-2">
                                    <b class="mt-2">Bulan: </b>
                                    @foreach ($months as $idx => $item)
                                        <a href="{{ route('jurnal.buku_besar_pembantu',['month'=>sprintf('%02d',$idx+1),'coa_id'=>$coa_id, 'year'=>$year]) }}" wire:click="changeMonth({{ $idx+1 }})" class="{{ $idx+1==(int)$month?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">{{ $item }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        {{-- <div class="d-flex gap-2">
                            <b class="mt-2">Bulan: </b>
                            @foreach ($months as $idx => $item)
                                <a href="{{ route('jurnal.buku_besar',['month'=>sprintf('%02d',$idx+1),'coa_id'=>$coa_id]) }}" wire:click="changeMonth({{ $idx+1 }})" class="{{ $idx+1==(int)$month?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">{{ $item }}</a>
                            @endforeach
                        </div> --}}
                        {{-- <div class="my-3">
                            <label for="search">Search</label>
                            <input type="text" wire:model="search" class="form-control" placeholder="Cari berdasarkan nomor jurnal/keterangan/akun/job/tanggal">
                        </div> --}}
                        <div class="table-responsive mt-3">
                            <table class="table data table-bordered table-sm mt-3 data-table" style="font-size: .7rem;">
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Customer</td>
                                        <td>Debit</td>
                                        <td>Credit</td>
                                        <td>Saldo</td>
                                        <td>#</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1 ;
                                        function monthName ($number){
                                            $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','July','Agustus','September','Oktober','November','Desember'];
                                            return $bulan[$number];
                                        }
                                    @endphp
                                    @foreach ($data as $idx => $jurnals)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $idx }}</td>
                                            <td>{{ number_format($jurnals->sum('debit')) }}</td>
                                            <td>{{ number_format($jurnals->sum('credit')) }}</td>
                                            <td>
                                                @php
                                                    if ($tipe=='D') {
                                                        $saldo = $jurnals->sum('debit') - $jurnals->sum('credit');
                                                    } else {
                                                        $saldo = $jurnals->sum('credit') - $jurnals->sum('debit');
                                                    }
                                                @endphp
                                                {{ number_format($saldo) }}
                                            </td>
                                            <td>
                                                <!-- Button trigger modal -->
                                                <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#detail-{{ $loop->iteration }}">
                                                    Detail
                                                </a>

                                                <!-- Modal -->
                                                <div class="modal fade" id="detail-{{ $loop->iteration }}" tabindex="-1" aria-labelledby="detail-{{ $loop->iteration }}Label" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="detail-{{ $loop->iteration }}Label">{{ $idx }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered table-detail" style="font-size: .7rem">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Tgl</th>
                                                                            <th>Nomor</th>
                                                                            <th>JOB</th>
                                                                            <th>INV</th>
                                                                            <th>Cont</th>
                                                                            <th>Nopol</th>
                                                                            <th>Keterangan</th>
                                                                            <th>Debit</th>
                                                                            <th>Credit</th>
                                                                            <th>Tanggal</th>
                                                                            <th>Nomor</th>
                                                                            <th>Keterangan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach (
                                                                            $jurnals->groupBy('order_id') as $id => $jurnal
                                                                        )
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $jurnal->where('debit','>',0)->first() ? date('d/m/y',strtotime($jurnal->where('debit','>',0)->first()->created_at)) : '-' }}</td>
                                                                            <td>{{ $jurnal->where('debit','>',0)->first()->nomor ?? '-'  }}</td>
                                                                            <td>{{ $jurnal->first()->order->job ?? '-'}}-{{ sprintf('%02d',$jurnal->first()->order->no_job ?? 0) }}</td>
                                                                            <td>{{ $jurnal->first()->invoice }}</td>
                                                                            <td>{{ $jurnal->first()->container }}</td>
                                                                            <td>{{ $jurnal->first()->nopol }}</td>
                                                                            <td>{{ $jurnal->where('debit','>',0)->first()->nama ?? '-' }}</td>
                                                                            <td>{{ number_format($jurnal->where('debit','>',0)->first()->debit ?? 0) }}</td>
                                                                            <td>{{ number_format($jurnal->where('credit','>',0)->first()->credit ?? 0) }}</td>
                                                                            <td>{{ $jurnal->where('credit','>',0)->first() ? date('d/m/y',strtotime($jurnal->where('credit','>',0)->first()->created_at)) : '-' }}</td>
                                                                            <td>{{ $jurnal->where('credit','>',0)->first()->nomor ?? '-'  }}</td>
                                                                            <td>{{ $jurnal->where('credit','>',0)->first()->nama ?? '-'  }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    {{-- <tfoot>
                                                                        <tr>
                                                                            <td class="text-end" colspan="8"><b>TOTAL</b></td>
                                                                            <td class="text-end"><b id="debit-total">{{ number_format($jurnals->sum('debit')) }}</b></td>
                                                                            <td class="text-end"><b id="credit-total">{{ number_format($jurnals->sum('credit')) }}</b></td>
                                                                            <td class="fw-bold">{{ number_format($saldo_) }}</td>
                                                                        </tr>
                                                                    </tfoot> --}}
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @php
                                            $no++;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td><b>TANPA JOB</b></td>
                                        <td>{{ number_format($no_data->sum('debit')) }}</td>
                                        <td>{{ number_format($no_data->sum('credit')) }}</td>
                                        <td>
                                            @php
                                                    if ($tipe=='D') {
                                                        $saldo_no_data = $no_data->sum('debit') - $no_data->sum('credit');
                                                    } else {
                                                        $saldo_no_data = $no_data->sum('credit') - $no_data->sum('debit');
                                                    }
                                                @endphp
                                                {{ number_format($saldo_no_data) }}
                                        </td>
                                        <td>-</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        {{-- {{ $data->links() }} --}}
                        {{-- @if($data->hasMorePages())
                            <button wire:click.prevent="loadMore" class="btn btn-sm btn-primary w-100">Load more</button>
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('assets/js/resize-column.js') }}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script>

    function load(){
        (function (window, ResizableTableColumns, undefined) {
            var store = window.store && window.store.enabled
                ? window.store
                : null;

            var els = document.querySelectorAll('table.data');
            for (var index = 0; index < els.length; index++) {
                var table = els[index];
                if (table['rtc_data_object']) {
                    continue;
                }

                var options = { store: store };
                if (table.querySelectorAll('thead > tr').length > 1) {
                    options.resizeFromBody = false;
                }

                new ResizableTableColumns(els[index], options);
            }

        })(window, window.validide_resizableTableColumns.ResizableTableColumns, void (0));
    }

    // load();
    $('.table-detail').dataTable()
    // $('table.data').dataTable({
    //     aLengthMenu: [
    //         [25, 50, 100, 200, -1],
    //         [25, 50, 100, 200, "All"]
    //     ],
    //     iDisplayLength: 25,
    //     fixedHeader: true,
    //     fixedColumns: {
    //         leftColumns: 2
    //     },
    // });
</script>
@endpush
@endsection
