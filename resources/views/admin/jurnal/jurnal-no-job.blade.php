@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />

    <style>
          .table-responsie table{
            position: relative;
            overflow-y: scroll;
        }
        .table-responsive th{
            background-color: white !important;
            position: sticky !important;
            top: 0;
        }

        .container {
    max-width: 100%;
    padding-left: 1rem;
    padding-right: 1rem;
    margin-left: 0;
    margin-right: 0;
}


        .btn-bank {
            background-color: #1a532f !important;
            color: white !important;
            border-color: #1a532f !important;
        }

        .btn-active {
            background-color: #4ade80 !important;
            color: white !important;
            border-color: #4ade80 !important;
        }

.card {
     max-width: 100%;
    margin-left: 0;
    margin-right: 0;
}


        .section-title {
            font-weight: bold;
            font-size: 1rem;
            margin: 20px 0 10px;
        }

        .table-wrapper {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            margin-top: 15px;
        }
    </style>
@endsection
@section('content')
<div class="container">

    <div class="card">
        <div class="card-body">
            {{-- Bagian Atas --}}
            <div class="section-title">Cek Jurnal COA 1.6.1 yang tidak memiliki job</div>

            {{-- Filter Kedua --}}
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">No Jurnal Dari</label>
                    <input type="text" name="no-jnl-start" id="no-jnl-start" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">No Jurnal Sampai</label>
                    <input type="text" name="no-jnl-end" id="no-jnl-end" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun-no" id="tahun-no" class="form-select">
                        @for ($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 ms-auto text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-success btn-sm" type="button" onclick="searchJurnal1()">Search</button>
                        <a class="btn btn-sm btn-warning" target="_blank" id="edit-coa1">Edit JOB</a>
                    </div>
                </div>
            </div>


            {{-- Grid Kedua --}}
            <div class="table-wrapper">
                <table id="jqGrid1"></table>
                <div id="jqGridPager1"></div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script src="{{ asset('assets/js/resize-column.js') }}"></script>
    <script>
        let id;
                $("#jqGrid1").jqGrid({
            url: '{{ route('jqgrid.jurnal') }}',
            mtype: 'GET',
            datatype: 'json',
            postData: {
                noJob: true
            },
            colModel: [{
                    search: true,
                    width: 50,
                    name: 'created_at',
                    label: 'Tanggal',
                    frozen: true
                },
                {
                    search: true,
                    width: 100,
                    name: 'nomor',
                    label: 'Nomor Jurnal',
                    frozen: true,
                    sortable: false
                },
                {
                    search: true,
                    width: 50,
                    name: 'coa_kode',
                    label: 'Kode',
                    frozen: true,
                },
                {
                    search: true,
                    width: 100,
                    name: 'coa_nama',
                    label: 'Akun',
                    frozen: true,
                },
                {
                    search: true,
                    width: 100,
                    name: 'id',
                    label: 'id',
                    hidden: true
                },
                {
                    search: true,
                    width: 100,
                    name: 'invoice',
                    label: 'Invoice'
                },
                {
                    search: true,
                    width: 100,
                    name: 'job',
                    label: 'Group JOB'
                },
                {
                    search: true,
                    width: 100,
                    name: 'no_job',
                    label: 'ID JOB'
                },
                {
                    search: true,
                    width: 100,
                    name: 'container',
                    label: 'Container'
                },
                {
                    search: true,
                    width: 100,
                    name: 'nopol',
                    label: 'Nopol'
                },
                {
                    search: true,
                    width: 300,
                    name: 'nama',
                    label: 'Keterangan'
                },
                {
                    search: true,
                    width: 100,
                    name: 'debit',
                    label: 'Debit'
                },
                {
                    search: true,
                    width: 100,
                    name: 'credit',
                    label: 'Credit'
                },
            ],
            autowidth: true,
            shrinkToFit: true,
            height: 'auto',
            oadonce: true,
            rowNum: 25,
            rowList: [10, 25, 50, 100, 250, 500, 1000],
            viewrecords: true,
            pager: "#jqGridPager1",
            caption: "Jurnal List",
            onCellSelect: function(rowId, iRow, iCol, e) {
                id = $(this).jqGrid('getCell', rowId, 'id');
                let nomor = $(this).jqGrid('getCell', rowId, 'nomor');
                 $('#edit-coa1').attr('href', @json(route('jurnal.edit')) + '?jurnal=' + encodeURIComponent(nomor));

            },
            rowattr: function(item) {
                return {
                    "class": item.class
                };
            }
        });

        $('#jqGrid1').jqGrid('navGrid', "#jqGridPager1", {
            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true
        });
        $("#jqGrid1").jqGrid('setFrozenColumns');

        $('#search').keyup(function(e) {
            let val = $(this).val();
            $("#jqGrid1").jqGrid('setGridParam', {
                postData: {
                    month: @json($month),
                    search: val
                }
            }).trigger('reloadGrid');
        });

 function searchJurnal1() {
    const nomorS = $('#no-jnl-start').val();
    const nomorE = $('#no-jnl-end').val();
    const tahun = $('#tahun-no').val();

    // Reset filter jika keterangan dan container diisi

    // Jalankan pencarian di jqGrid
    $("#jqGrid1").jqGrid('setGridParam', {
        postData: {
            kategori: "real",
            tahun: tahun,
            nomorS: nomorS,
            nomorE: nomorE
        },
        page: 1
    }).trigger('reloadGrid');
}

    </script>
@endsection

