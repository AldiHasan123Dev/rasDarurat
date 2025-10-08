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
        max-height: 400px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card p-3">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-success" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <form action="{{ url()->current() }}" method="get">
                        <select name="year" id="year" class="form-select" onchange="submit()">
                            @for ($y = 2023; $y <= 2027; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>

                <div id="print">
                    <div class="mt-3">
                        <table class="table table-sm table-bordered mt-3" id="table" style="font-size: .7rem">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center">Marketing</th>
                                    @foreach (['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $bulan)
                                        <th class="text-center" colspan="2">{{ $bulan }}</th>
                                    @endforeach
                                    <th class="text-center" colspan="3">Total</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 26; $i++)
                                        <th class="text-center" style="min-width:40px !important">{{ $i%2==0 ? '20' : '40' }}</th>
                                    @endfor
                                    <th class="text-center">Sub Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $sub = [];
                                    $total = 0;
                                @endphp
                                @foreach ($data as $item)
                                    @php
                                        $month = 1;
                                        $fit20 = 0;
                                        $fit40 = 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        @for ($i = 1; $i <= 24; $i++)
                                            @if ($i % 2 == 0)
                                                @php
                                                   $val = App\Models\User::find($item['id'])->laporanMarketing20Fit($month, $year);

                                                    $fit20 += $val;
                                                    $sub[$i] = ($sub[$i] ?? 0) + $val;
                                                    $month++;
                                                @endphp
                                                <td class="text-center">{{ $val }}</td>
                                            @else
                                                @php
                                                    $val = App\Models\User::find($item['id'])->laporanMarketing40Fit($month, $year);
                                                    $fit40 += $val;
                                                    $sub[$i] = ($sub[$i] ?? 0) + $val;
                                                @endphp
                                                <td class="text-center">{{ $val }}</td>
                                            @endif
                                        @endfor
                                        <td class="text-center text-warning">{{ $fit40 }}</td>
                                        <td class="text-center text-warning">{{ $fit20 }}</td>
                                        <td class="text-center text-warning">{{ $fit20 + $fit40 }}</td>

                                        @php
                                            $sub[25] = ($sub[25] ?? 0) + $fit40;
                                            $sub[26] = ($sub[26] ?? 0) + $fit20;
                                            $total += $fit20 + $fit40;
                                        @endphp
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th rowspan="2" class="align-middle text-center text-primary">Total</th>
                                    @for ($i = 1; $i <= 26; $i++)
                                        <th class="text-center text-primary">{{ $sub[$i] ?? 0 }}</th>
                                    @endfor
                                    <th rowspan="2" class="align-middle text-center text-primary">{{ $total }}</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 26; $i+=2)
                                        <th class="text-center text-primary" colspan="2">{{ ($sub[$i] ?? 0) + ($sub[$i+1] ?? 0) }}</th>
                                    @endfor
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
    fixedColumns: { left: 1 },
    autoWidth:false,
    paging: false,
    scrollCollapse: true,
    fixedHeader: true,
    dom: 'Bfrtip',
    buttons: [{ extend:'excel' }],
});
jQuery('.dataTable').wrap('<div class="dataTables_scroll" />');
</script>
@endsection
