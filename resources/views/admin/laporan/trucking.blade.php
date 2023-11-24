@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
    @media print{
        @page {
            size: landscape
        }
        body * {
            visibility: hidden;
        }
        body{
            width: 100%;
        }
        #print, #print * {
            visibility: visible;
            font-family: 'Open Sans', sans-serif;
            font-size: .7rem !important;
            color: black !important;
        }
        #print{
            position: absolute;
            top: -80px;
        }
        tr th, tr{
            border: 1px solid black;
        }

    }
    thead{
        position: sticky;
        z-index: 12;
        top: 0px;
        background: white;
    }
    tfoot{
        position: sticky;
        z-index: 12;
        bottom: 0px;
        background: white;
    }
    th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
    #table th,
    #table td {
        vertical-align: middle;
        height: 20px;
        padding: 0 !important;
        border: 1px solid black;
        color: black;
    }
    .dataTables_scroll
    {
        overflow:auto;
        height: 400px;
    }
</style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-success" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                        <form action="{{ url()->current() }}" method="get">
                            <select name="year" id="year" class="form-select" onchange="submit()">
                                <option {{ $year=='2023'?'selected':'' }} value="2023">2023</option>
                                <option {{ $year=='2024'?'selected':'' }} value="2024">2024</option>
                                <option {{ $year=='2025'?'selected':'' }} value="2025">2025</option>
                                <option {{ $year=='2026'?'selected':'' }} value="2026">2026</option>
                                <option {{ $year=='2027'?'selected':'' }} value="2027">2027</option>
                            </select>
                        </form>
                    </div>
                    <div id="print">
                        <div class="mt-3">
                            <table class="table table-sm table-bordered mt-3" id="table" style="font-size: .7rem; white-space:nowrap">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center">NOPOL</th>
                                        <th class="text-center" colspan="2">Jan</th>
                                        <th class="text-center" colspan="2">Feb</th>
                                        <th class="text-center" colspan="2">Mar</th>
                                        <th class="text-center" colspan="2">Apr</th>
                                        <th class="text-center" colspan="2">Mei</th>
                                        <th class="text-center" colspan="2">Jun</th>
                                        <th class="text-center" colspan="2">Jul</th>
                                        <th class="text-center" colspan="2">Agu</th>
                                        <th class="text-center" colspan="2">Sep</th>
                                        <th class="text-center" colspan="2">Okt</th>
                                        <th class="text-center" colspan="2">Nov</th>
                                        <th class="text-center" colspan="2">Des</th>
                                        <th class="text-center" colspan="2">Total</th>
                                    </tr>
                                    <tr>
                                        {{-- <th>NOPOL</th> --}}
                                        @for ($i = 1; $i <=26; $i++)
                                        <th class="text-center" style="min-width:40px !important">{{ $i%2==0?'RIT':'M' }}</th>
                                        @endfor
                                        {{-- <th class="text-center">Sub Total</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sub = array();
                                        $total = 0;
                                    @endphp
                                    @foreach ($data as $idx => $item)
                                        <tr>
                                            <td class="text-dark">{{ $item->nopol }} ({{ $item->milik }})</td>
                                            @php
                                                $month = 1;
                                                $rit = 0;
                                                $m = 0;
                                            @endphp
                                            @for ($i = 1; $i <=24; $i++)
                                                @if ($i%2==0)
                                                    <th class="text-center">{{ $item->laporanRit($month,$year) }}</th>
                                                    @php
                                                        $rit += $item->laporanRit($month,$year);
                                                        $sub[$i] = ($sub[$i]??0) + $item->laporanRit($month,$year);
                                                        $month++;
                                                    @endphp
                                                @else
                                                    <th class="text-center">{{ formatNumber($item->laporanMargin($month,$year)) }}</th>
                                                    @php
                                                        $m += $item->laporanMargin($month,$year);
                                                        $sub[$i] = ($sub[$i]??0) + $item->laporanMargin($month,$year);
                                                    @endphp
                                                @endif
                                            @endfor
                                            <th class="text-center text-warning">{{ formatNumber($m) }}</th>
                                            <th class="text-center text-warning">{{ $rit }}</th>
                                            {{-- <th class="text-center text-warning">{{ $rit + $m }}</th> --}}
                                            @php
                                                $sub[25] = ($sub[25]??0) + $m;
                                                $sub[26] = ($sub[26]??0) + $rit;
                                                $total += $rit + $m;
                                            @endphp
                                        </tr>
                                    @endforeach
                                    {{-- <tr>
                                        @for ($i = 1; $i <= 26; $i+=2)
                                        <th class="text-center text-primary" colspan="2">{{ formatNumber($sub[$i] + $sub[$i+1]) }}</th>
                                        @endfor
                                    </tr> --}}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="align-middle text-center text-primary">Total</th>
                                        @for ($i = 1; $i <=26; $i++)
                                        @if ($i%2==0)
                                        <th class="text-center text-primary">{{ $sub[$i] }}</th>
                                        @else
                                        <th class="text-center text-primary">{{ formatNumber($sub[$i]) }}</th>
                                        @endif
                                        @endfor
                                        {{-- <th rowspan="2" class="align-middle text-center text-primary">{{ $total }}</th> --}}
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script>
        $('#table').DataTable({
            fixedColumns: {
                left: 1,
                right: 0
            },
            autoWidth:false,
            paging: false,
            scrollCollapse: true,
            fixedHeader: true,
            // scrollX:true,
            // scrollY: 400,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend:'excel'
                },
            ],
        });
        jQuery('.dataTable').wrap('<div class="dataTables_scroll" />');
    </script>
@endsection
