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
                            <div class="col-12 col-md-4">
                                <label for="subjek">Subjek</label>
                                <form action="{{ route('jurnal.buku_besar_pembantu') }}" method="get">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <input type="hidden" name="coa_id" value="{{ $coa_id }}">
                                    <select class="form-control px-3 py-1" name="subjek" onchange="submit()" style="font-size:.8rem">
                                        <option {{ $subjek=='customer_xpdc' ? 'selected' : '' }} value="customer_xpdc">Customer XPDC</option>
                                        <option {{ $subjek=='customer_trucking' ? 'selected' : '' }} value="customer_trucking">Customer Trucking</option>
                                        <option {{ $subjek=='pelayaran' ? 'selected' : '' }} value="pelayaran">Pelayaran</option>
                                        <option {{ $subjek=='agen' ? 'selected' : '' }} value="agen">Agen</option>
                                        <option {{ $subjek=='kendaraan' ? 'selected' : '' }} value="kendaraan">Vendor</option>
                                    </select>
                                </form>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="akun">Akun</label>
                                <form action="{{ route('jurnal.buku_besar_pembantu') }}" method="get">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <input type="hidden" name="subjek" value="{{ $subjek }}">
                                    <select class="form-control px-3 py-1" name="coa_id" onchange="submit()" style="font-size:.8rem">
                                        @foreach ($coas as $item)
                                            <option {{ $coa_id == $item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="tahub">Tahun</label>
                                <form action="{{ route('jurnal.buku_besar_pembantu',['month'=>$month,'coa_id'=>$coa_id,'year'=>$year]) }}" method="get">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="coa_id" value="{{ $coa_id }}">
                                    <input type="hidden" name="subjek" value="{{ $subjek }}">
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
                            </div>
                            <div class="col-12 mt-3">
                                <hr>
                                <div class="d-flex gap-2">
                                    <b class="mt-2">Bulan: </b>
                                    @foreach ($months as $idx => $item)
                                        <a href="{{ route('jurnal.buku_besar_pembantu',['month'=>sprintf('%02d',$idx+1),'coa_id'=>$coa_id, 'year'=>$year, 'subjek'=>$subjek]) }}" wire:click="changeMonth({{ $idx+1 }})" class="{{ $idx+1==(int)$month?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">{{ $item }}</a>
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
                            <div id="response">Loading...!</div>
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

    $.ajax({
        type: "POST",
        url: "{{ url('api/render-bb-pembantu') }}",
        data: {
            coa_id:@json($coa_id),
            month:@json($month),
            year:@json($year),
            subjek:@json($subjek)
        },
        success: function (response) {
            var data = `<div>${response.data}</div>`;
            $('#response').html(data);
        }
    });
</script>
@endpush
@endsection
