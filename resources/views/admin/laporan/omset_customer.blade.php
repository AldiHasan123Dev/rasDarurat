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
                        <div class="mt-3" style="height: 400px">
                            <table class="table table-sm table-bordered mt-3" id="table" style="font-size: .7rem">
                                <thead>
                                    <tr>
                                        <th class="text-center">Customer</th>
                                        <th class="text-center">Jan</th>
                                        <th class="text-center">Feb</th>
                                        <th class="text-center">Mar</th>
                                        <th class="text-center">Apr</th>
                                        <th class="text-center">Mei</th>
                                        <th class="text-center">Jun</th>
                                        <th class="text-center">Jul</th>
                                        <th class="text-center">Agu</th>
                                        <th class="text-center">Sep</th>
                                        <th class="text-center">Okt</th>
                                        <th class="text-center">Nov</th>
                                        <th class="text-center">Des</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grand_total = 0;
                                        $sub = [0,0,0,0,0,0,0,0,0,0,0,0];
                                    @endphp
                                    @foreach ($data as $idx => $item)
                                    @php
                                        $total = 0;
                                    @endphp
                                        <tr>
                                            <td>{{ $item->nama }}</td>
                                            @for ($i = 1; $i <=12; $i++)
                                            <th class="text-end">{{ number_format($item->laporanOmset($i,$year),2,'.',',') }}</th>
                                            @php
                                                $total += $item->laporanOmset($i,$year);
                                                $sub[$i-1] += $item->laporanOmset($i,$year);
                                            @endphp
                                            @endfor
                                            <th class="text-end text-warning">{{ number_format($total,2,'.',',') }}</th>
                                        </tr>
                                        @php
                                            $grand_total += $total;
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="align-middle text-center text-primary">Total</th>
                                        @for ($i = 1; $i <=12; $i++)
                                        <th class="text-center text-primary">{{ number_format($sub[$i-1],2,'.',',') }}</th>
                                        @endfor
                                        <th class="align-middle text-center text-primary">{{ number_format($grand_total,2,'.',',') }}</th>
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
