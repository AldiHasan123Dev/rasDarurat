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
            {{-- <form action="{{ route('jurnal.exportJurnalBatch') }}" method="post">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" class="btn btn-sm btn-success my-2"><i class="fas fa-print"></i> Export Excel</button>
            </form> --}}
            <div id="print">
                {{-- <livewire:buku-besar :month="request('month')"/> --}}
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
                                        <td>#</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1 ;
                                    @endphp
                                    @foreach ($data as $idx => $jurnals)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $idx }}</td>
                                            <td>{{ number_format($jurnals->sum('debit')) }}</td>
                                            <td>{{ number_format($jurnals->sum('credit')) }}</td>
                                            <td>
                                                <!-- Button trigger modal -->
                                                <button type="button" class="btn-sm btn btn-primary" data-bs-toggle="modal" data-bs-target="#detail-{{ $loop->iteration }}">
                                                    Detail
                                                </button>

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
                                                                <table class="table table-sm table-bordered" style="font-size: .7rem">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Tgl</th>
                                                                            <th>Nomor</th>
                                                                            <th>COA</th>
                                                                            <th>Akun</th>
                                                                            <th>JOB</th>
                                                                            <th>INV</th>
                                                                            <th>Cont</th>
                                                                            <th>Nopol</th>
                                                                            <th>Keterangan</th>
                                                                            <th>Debit</th>
                                                                            <th>Credit</th>
                                                                            <th>#</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($jurnals->sortBy('created_at') as $jurnal)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ date('d/m/y',strtotime($jurnal->created_at)) }}</td>
                                                                            <td>{{ $jurnal->nomor }}</td>
                                                                            <td>{{ $jurnal->coa->kode }}</td>
                                                                            <td>{{ $jurnal->coa->nama }}</td>
                                                                            <td>{{ $jurnal->order->job }}-{{ sprintf('%02d',$jurnal->order->no_job) }}</td>
                                                                            <td>{{ $jurnal->invoice }}</td>
                                                                            <td>{{ $jurnal->container }}</td>
                                                                            <td>{{ $jurnal->nopol }}</td>
                                                                            <td>{{ $jurnal->nama }}</td>
                                                                            <td>{{ number_format($jurnal->debit) }}</td>
                                                                            <td>{{ number_format($jurnal->credit) }}</td>
                                                                            <td>#</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <td class="text-end" colspan="10"><b>TOTAL</b></td>
                                                                            <td class="text-end"><b id="debit-total">{{ number_format($jurnals->sum('debit')) }}</b></td>
                                                                            <td class="text-end"><b id="credit-total">{{ number_format($jurnals->sum('credit')) }}</b></td>
                                                                            <td></td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-primary">Save changes</button>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
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
