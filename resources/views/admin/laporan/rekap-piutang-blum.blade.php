@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <!-- CSS Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />

    <style>
        .ui-jqgrid .ui-jqgrid-ftable td {
            font-size: 0.75rem !important;
            /* Ubah sesuai kebutuhan, misal 12px atau 10px */
            padding: 4px 8px;
        }

        .table-responsie table {
            position: relative;
            overflow-y: scroll;
        }

        .table-responsive th {
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
            background-color: #ffd503 !important;
            color: white !important;
            border-color: #00fce3 !important;
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
            max-width: 100%;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .ui-jqgrid .ui-jqgrid-htable th[id*="kurang_bayar"],
        .ui-jqgrid .ui-jqgrid-htable th[id*="sebesar"],
        .ui-jqgrid .ui-jqgrid-htable th[id*="jumlah_harga"],
        .ui-jqgrid .ui-jqgrid-htable th[id*="pph"],
        .ui-jqgrid .ui-jqgrid-htable th[id*="tf_masuk"] {
            padding: 5px;
            text-align: right !important;
        }

        .ui-jqgrid .ui-jqgrid-htable th[id*="invoice"] {
            padding: 5px;
            text-align: center !important;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <div class="section-title">Lap Rekap Outstanding</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                {{-- Filter Kedua --}}
                <div class="table-wrapper">
                    <table id="jqGrid10"></table>
                    <div id="jqGridPager10"></div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <!-- JS Select2 dan jQuery (jika belum ada) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
    <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script src="{{ asset('assets/js/resize-column.js') }}"></script>
    <script>
        $("#jqGrid10").jqGrid({
            url: '{{ route('data-rekap.piutang') }}',
            mtype: 'GET',
            postData: {
                job: true
            },
            datatype: 'json',
            colModel: [{
                    name: 'id',
                    hidden: true
                },
                {
                    label: 'Invoice',
                    align: "center",
                    name: 'invoice',
                    width: 80,
                    sortable: true,
                    search: true
                },
                {
                    label: 'Marketing',
                    align: "center",
                    name: 'marketing',
                    width: 120,
                    align: "left",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Nama Customer',
                    align: "center",
                    name: 'customer',
                    width: 120,
                    align: "left",
                    sortable: true,
                    search: true
                },
                {
                    label: 'No Job',
                    align: "center",
                    name: 'no_job',
                    width: 120,
                    align: "left",
                    sortable: true,
                    search: true
                },
                {
                    label: 'No Cont',
                    align: "center",
                    name: 'container',
                    width: 120,
                    align: "left",
                    sortable: true,
                    search: true
                },
                {
                    name: 'td',
                    align: "center",
                    label: 'TD',
                    width: 50,
                    align: "center",
                    sortable: true
                },
                {
                    label: 'Harga (INC.PPN)',
                    align: "right",
                    name: 'jumlah_harga',
                    width: 100,
                    align: "right",
                    sortable: true
                },
                {
                    label: 'Dibayar',
                    align: "right",
                    name: 'sebesar',
                    width: 100,
                    align: "right",
                    sortable: true
                },
                {
                    label: 'PPH',
                    align: "right",
                    name: 'pph',
                    width: 100,
                    align: "right",
                    sortable: true
                },
                {
                    name: 'warna_status',
                    hidden: true
                },
                {
                    label: 'Kurang Bayar',
                    name: 'kurang_bayar',
                    width: 100,
                    align: "right", // isi cell rata kanan
                    labelAlign: "right", // label header rata kanan
                    sortable: true
                }

            ],
            autowidth: true,
            shrinkToFit: true,
            height: 'auto',
            loadonce: false,
            rowNum: 9999,
            viewrecords: true,
            pager: "#jqGridPager10",
            caption: "Rekap Piutang Periode Bulan",
            jsonReader: {
                repeatitems: false,
                root: "rows",
                page: "page",
                total: "total",
                records: "records"
            },
            onCellSelect: function(rowId, iRow, iCol, e) {
                let nomor = $(this).jqGrid('getCell', rowId, 'nomor');
            },
            rowattr: function(rowData) {
                if (!rowData.tempo) return {}; // Jika tidak ada tempo, tidak ada warna

                let today = new Date().toISOString().split('T')[0]; // Format YYYY-MM-DD
                let tempoDate = new Date(rowData.tempo).toISOString().split('T')[0];

                let selisih = rowData.pph - rowData.kurang_bayar;

                let timeDiff = new Date(rowData.tempo) - new Date();
                let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                // Jika kurang bayar = 0, semua kondisi tetap hijau
                if (parseFloat(rowData.kurang_bayar) === 0) {
                    return {
                        "style": "background-color: #3fae43; color: white;"
                    };
                }

                if (parseFloat(rowData.kurang_bayar) < 0) {
                    return {
                        "style": "background-color: #0099ff;; color: white;"
                    };
                }

                if (selisih === 0) {
                    return {
                        "style": "background-color: #ff9d00; color: white;"
                    };
                }

                // Jika TOP = 0 dan jatuh tempo hari ini, tidak diberi warna
                if (parseInt(rowData.top) === 0 && tempoDate === today) {
                    return {};
                }

                // Warna oranye untuk jatuh tempo dalam 1-3 hari
                if (daysDiff > 0 && daysDiff <= 4) {
                    return {
                        "style": "background-color: #ffd503; color: white;"
                    };
                }

                // Warna merah jika sudah jatuh tempo atau jatuh tempo hari ini
                if (daysDiff < 0) {
                    return {
                        "style": "background-color: red; color: white;"
                    };
                }

                return {};
            }
        });


        // Navigation
        $('#jqGrid10').jqGrid('navGrid', "#jqGridPager10", {
            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true
        });

        // Frozen columns
        $("#jqGrid10").jqGrid('setFrozenColumns');

        // Live Search
    </script>
@endsection
