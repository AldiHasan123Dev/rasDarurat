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
                          <table class="table table-sm table-bordered table-detail" style="font-size: .7rem">
                              <thead>
                                  <tr>
                                      <th>No</th>
                                      <th>Tgl</th>
                                      <th>Nomor</th>
                                      <th>JOB</th>
                                      <th>INV</th>
                                      <th>NO BG</th>
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
                                  $jurnals->groupBy(['no_bg']) as $id => $jurnal
                                )
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @forelse ($jurnal->where('debit','>',0) as $tgl)
                                                <span>{{ date('d/m/y',strtotime($tgl->created_at)) }}; </span>
                                            @empty
                                            <span>-</span>
                                            @endforelse
                                        </td>
                                        <td>{{ implode('; ',$jurnal->where('debit','>',0)->pluck('nomor')->toArray()) }}</td>
                                        <td>
                                            {{ implode('; ',$jurnal->first()->bg()) }}
                                        </td>
                                        <td>{{ $jurnal->first()->invoice ?? '-' }}</td>
                                        <td>{{ $jurnal->first()->no_bg ?? '-' }}</td>
                                        <td>{{ $jurnal->first()->container ?? '-' }}</td>
                                        <td>{{ $jurnal->first()->nopol ?? '-' }}</td>
                                        <td>{{ implode('; ',$jurnal->where('debit','>',0)->pluck('nama')->toArray()) }}</td>
                                        <td>{{ number_format($jurnal->where('debit','>',0)->sum('debit')) }}</td>
                                        <td>{{ number_format($jurnal->where('credit','>',0)->sum('credit')) }}</td>
                                        <td>
                                            @forelse ($jurnal->where('credit','>',0) as $tgl)
                                                <span>{{ date('d/m/y',strtotime($tgl->created_at)) }}; </span>
                                            @empty
                                                <span>-</span>
                                            @endforelse
                                        </td>
                                        <td>{{ implode('; ',$jurnal->where('credit','>',0)->pluck('nomor')->toArray())  }}</td>
                                        <td>{{ implode('; ',$jurnal->where('credit','>',0)->pluck('nama')->toArray())  }}</td>
                                    </tr>
                                @endforeach
                              </tbody>
                              {{-- <tfoot>
                                  <tr>
                                      <td class="text-end" colspan="8"><b>TOTAL</b></td>
                                      <td class="text-end"><b id="debit-total">{{ number_format($jurnals->sum('debit')) }}</b></td>
                                      <td class="text-end"><b id="credit-total">{{ number_format($jurnals->sum('credit')) }}</b></td>
                                      <td colspan="3"></td>
                                  </tr>
                              </tfoot> --}}
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
