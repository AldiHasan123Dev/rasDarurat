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

        /* 🌊 Styling Khusus untuk Navbar Keuangan */
        .navbar-keuangan {
            background: linear-gradient(90deg, #6a7b94, #5f6a7a);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: background 0.3s ease-in-out;
            border-radius: 4px;
        }

        .navbar-keuangan .navbar-nav .nav-link {
            color: #f8f9fa !important;
            font-weight: 500;
            padding: 8px 18px;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .navbar-keuangan .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        .navbar-keuangan .navbar-nav .nav-link.active {
            background-color: #ffffff !important;
            color: #526d96 !important;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-keuangan .navbar-toggler {
            border: none;
        }

        .navbar-keuangan .navbar-toggler:focus {
            box-shadow: none;
        }

        @media (max-width: 991px) {
            .navbar-keuangan .navbar-nav .nav-link {
                margin-bottom: 6px;
            }
        }
    </style>
@endsection
@section('content')
    <div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">
                <i class="fas fa-sticky-note me-2"></i>Noted
            </h5>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Piutang Overdue adalah Piutang yang belum Lunas dan telah melewati tanggal bayar sesuai TOP-nya.</li>
                <li>Data bersifat UPDATED, namun memungkinkan belum Full Realtime karena faktor jeda/kebutuhan waktu di penginputan jurnal.</li>
            </ul>
        </div>
    </div>
</div>
        <div class="container mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="section-title">Rekap Piutang (Overdue 1 - 30 hari)</div>
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">
                                    Total Kurang Bayar (Overdue 1 - 30 hari)
                                </span>
                                <span id="total-kurang-bayar-overdue30" class="fw-bold text-danger fs-4">
                                    Rp 0
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table id="overdue30"></table>
                        <div id="overdue30Pager"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">

            <div class="card">
                <div class="card-body">
                    <div class="section-title">Rekap Piutang (Overdue 30 - 60 hari)</div>
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">
                                    Total Kurang Bayar (Overdue 30 - 60 hari)
                                </span>
                                <span id="total-kurang-bayar-overdue60" class="fw-bold text-danger fs-4">
                                    Rp 0
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table id="overdue60"></table>
                        <div id="overdue60Pager"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">

            <div class="card">
                <div class="card-body">
                    <div class="section-title">Rekap Piutang (Overdue 60 - 90 hari)</div>
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">
                                    Total Kurang Bayar (Overdue 60 - 90 hari)
                                </span>
                                <span id="total-kurang-bayar-overdue90" class="fw-bold text-danger fs-4">
                                    Rp 0
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table id="overdue90"></table>
                        <div id="overdue90Pager"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">

            <div class="card">
                <div class="card-body">
                    <div class="section-title">Rekap Piutang (Overdue 90 hari ++)</div>
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">
                                    Total Kurang Bayar (Overdue 90 hari ++)
                                </span>
                                <span id="total-kurang-bayar-overdue90-lebih" class="fw-bold text-danger fs-4">
                                    Rp 0
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table id="overdue90-lebih"></table>
                        <div id="overdue90-lebihPager"></div>
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
            $("#overdue30").jqGrid({
                url: '{{ route('data-rekap.piutang') }}',
                mtype: 'GET',
                postData: {
                    overdue30: true,
                    userId: '{{ auth()->id() }}'
                },
                datatype: 'json',

                footerrow: true,
                userDataOnFooter: true,

                colModel: [{
                        name: 'id',
                        hidden: true
                    },
                    {
                        label: 'Invoice',
                        name: 'invoice',
                        width: 80,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Nama Customer',
                        name: 'customer',
                        width: 120,
                        align: "left",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Harga (INC.PPN)',
                        name: 'jumlah_harga',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    },
                    {
                        label: 'Tanggal',
                        name: 'tanggal',
                        width: 50,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        hidden: true
                    },
                    {
                        label: 'TGL Kirim Inv',
                        name: 'ditagih_tgl',
                        width: 50,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'TOP',
                        name: 'top',
                        width: 30,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Jatuh Tempo TGL',
                        name: 'tempo',
                        width: 80,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Dibayar TGL',
                        name: 'dibayar_tgl',
                        width: 50,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Dibayar',
                        name: 'sebesar',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    },
                    {
                        label: 'PPH',
                        name: 'pph',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
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
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    }
                ],

                autowidth: true,
                shrinkToFit: true,
                height: 'auto',
                loadonce: false,
                rowNum: 150,
                rowList: [150, 500, 1000],
                viewrecords: true,
                pager: "#overdue30Pager",
                caption: "Rekap Piutang Belum Bayar",

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

                loadComplete: function(response) {

                    $('#total-kurang-bayar-overdue30').text(
                        'Rp ' + Number(response.total_kurang_bayar).toLocaleString('id-ID')
                    );

                },

                rowattr: function(rowData) {

                    if (!rowData.tempo) return {};

                    let today = new Date().toISOString().split('T')[0];
                    let tempoDate = new Date(rowData.tempo).toISOString().split('T')[0];

                    let selisih = parseFloat(rowData.pph || 0) -
                        parseFloat(rowData.kurang_bayar || 0);

                    let timeDiff = new Date(rowData.tempo) - new Date();
                    let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                    if (parseFloat(rowData.kurang_bayar) === 0) {
                        return {
                            style: "background-color:#3fae43;color:white;"
                        };
                    }

                    if (parseFloat(rowData.kurang_bayar) < 0) {
                        return {
                            style: "background-color:#0099ff;color:white;"
                        };
                    }

                    if (selisih === 0) {
                        return {
                            style: "background-color:#ff9d00;color:white;"
                        };
                    }

                    if (
                        parseInt(rowData.top) === 0 &&
                        tempoDate === today
                    ) {
                        return {};
                    }

                    if (daysDiff > 0 && daysDiff <= 4) {
                        return {
                            style: "background-color:#ffd503;color:white;"
                        };
                    }

                    if (daysDiff < 0) {
                        return {
                            style: "background-color:red;color:white;"
                        };
                    }

                    return {};
                }
            });


            // Navigation
            $('#overdue30').jqGrid('navGrid', "#overdue30Pager", {
                search: false,
                add: false,
                edit: false,
                del: false,
                refresh: true
            });

            // Frozen columns
            $("#overdue30").jqGrid('setFrozenColumns');

            // Live Search
            function filterWarna2(warna) {
                let grid = $("#overdue30");
                let postData = grid.jqGrid('getGridParam', 'postData');

                postData.filters = JSON.stringify({
                    groupOp: "AND",
                    rules: warna ? [{
                        field: "warna_status",
                        op: "eq",
                        data: warna
                    }] : []
                });

                grid.jqGrid('setGridParam', {
                    search: true,
                    postData: postData
                }).trigger("reloadGrid");
            }


            $("#overdue60").jqGrid({
                url: '{{ route('data-rekap.piutang') }}',
                mtype: 'GET',
                postData: {
                    overdue60: true,
                    userId: '{{ auth()->id() }}'
                },
                datatype: 'json',

                footerrow: true,
                userDataOnFooter: true,

                colModel: [{
                        name: 'id',
                        hidden: true
                    },
                    {
                        label: 'Invoice',
                        name: 'invoice',
                        width: 80,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Nama Customer',
                        name: 'customer',
                        width: 120,
                        align: "left",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Harga (INC.PPN)',
                        name: 'jumlah_harga',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    },
                    {
                        label: 'Tanggal',
                        name: 'tanggal',
                        width: 50,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        hidden: true
                    },
                    {
                        label: 'TGL Kirim Inv',
                        name: 'ditagih_tgl',
                        width: 50,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'TOP',
                        name: 'top',
                        width: 30,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Jatuh Tempo TGL',
                        name: 'tempo',
                        width: 80,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Dibayar TGL',
                        name: 'dibayar_tgl',
                        width: 50,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Dibayar',
                        name: 'sebesar',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    },
                    {
                        label: 'PPH',
                        name: 'pph',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
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
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    }
                ],

                autowidth: true,
                shrinkToFit: true,
                height: 'auto',
                loadonce: false,
                rowNum: 150,
                rowList: [150, 500, 1000],
                viewrecords: true,
                pager: "#overdue60Pager",
                caption: "Rekap Piutang Belum Bayar",

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

                loadComplete: function(response) {

                    $('#total-kurang-bayar-overdue60').text(
                        'Rp ' + Number(response.total_kurang_bayar).toLocaleString('id-ID')
                    );

                },

                rowattr: function(rowData) {

                    if (!rowData.tempo) return {};

                    let today = new Date().toISOString().split('T')[0];
                    let tempoDate = new Date(rowData.tempo).toISOString().split('T')[0];

                    let selisih = parseFloat(rowData.pph || 0) -
                        parseFloat(rowData.kurang_bayar || 0);

                    let timeDiff = new Date(rowData.tempo) - new Date();
                    let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                    if (parseFloat(rowData.kurang_bayar) === 0) {
                        return {
                            style: "background-color:#3fae43;color:white;"
                        };
                    }

                    if (parseFloat(rowData.kurang_bayar) < 0) {
                        return {
                            style: "background-color:#0099ff;color:white;"
                        };
                    }

                    if (selisih === 0) {
                        return {
                            style: "background-color:#ff9d00;color:white;"
                        };
                    }

                    if (
                        parseInt(rowData.top) === 0 &&
                        tempoDate === today
                    ) {
                        return {};
                    }

                    if (daysDiff > 0 && daysDiff <= 4) {
                        return {
                            style: "background-color:#ffd503;color:white;"
                        };
                    }

                    if (daysDiff < 0) {
                        return {
                            style: "background-color:red;color:white;"
                        };
                    }

                    return {};
                }
            });


            // Navigation
            $('#overdue60').jqGrid('navGrid', "#overdue60Pager", {
                search: false,
                add: false,
                edit: false,
                del: false,
                refresh: true
            });

            // Frozen columns
            $("#overdue60").jqGrid('setFrozenColumns');

            // Live Search
            function filterWarna2(warna) {
                let grid = $("#overdue60");
                let postData = grid.jqGrid('getGridParam', 'postData');

                postData.filters = JSON.stringify({
                    groupOp: "AND",
                    rules: warna ? [{
                        field: "warna_status",
                        op: "eq",
                        data: warna
                    }] : []
                });

                grid.jqGrid('setGridParam', {
                    search: true,
                    postData: postData
                }).trigger("reloadGrid");
            }

            $("#overdue90").jqGrid({
                url: '{{ route('data-rekap.piutang') }}',
                mtype: 'GET',
                postData: {
                    overdue90: true,
                    userId: '{{ auth()->id() }}'
                },
                datatype: 'json',

                footerrow: true,
                userDataOnFooter: true,

                colModel: [{
                        name: 'id',
                        hidden: true
                    },
                    {
                        label: 'Invoice',
                        name: 'invoice',
                        width: 80,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Nama Customer',
                        name: 'customer',
                        width: 120,
                        align: "left",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Harga (INC.PPN)',
                        name: 'jumlah_harga',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    },
                    {
                        label: 'Tanggal',
                        name: 'tanggal',
                        width: 50,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        hidden: true
                    },
                    {
                        label: 'TGL Kirim Inv',
                        name: 'ditagih_tgl',
                        width: 50,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'TOP',
                        name: 'top',
                        width: 30,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Jatuh Tempo TGL',
                        name: 'tempo',
                        width: 80,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Dibayar TGL',
                        name: 'dibayar_tgl',
                        width: 50,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Dibayar',
                        name: 'sebesar',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    },
                    {
                        label: 'PPH',
                        name: 'pph',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
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
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    }
                ],

                autowidth: true,
                shrinkToFit: true,
                height: 'auto',
                loadonce: false,
                rowNum: 150,
                rowList: [150, 500, 1000],
                viewrecords: true,
                pager: "#overdue90Pager",
                caption: "Rekap Piutang Belum Bayar",

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

                loadComplete: function(response) {

                    $('#total-kurang-bayar-overdue90').text(
                        'Rp ' + Number(response.total_kurang_bayar).toLocaleString('id-ID')
                    );

                },

                rowattr: function(rowData) {

                    if (!rowData.tempo) return {};

                    let today = new Date().toISOString().split('T')[0];
                    let tempoDate = new Date(rowData.tempo).toISOString().split('T')[0];

                    let selisih = parseFloat(rowData.pph || 0) -
                        parseFloat(rowData.kurang_bayar || 0);

                    let timeDiff = new Date(rowData.tempo) - new Date();
                    let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                    if (parseFloat(rowData.kurang_bayar) === 0) {
                        return {
                            style: "background-color:#3fae43;color:white;"
                        };
                    }

                    if (parseFloat(rowData.kurang_bayar) < 0) {
                        return {
                            style: "background-color:#0099ff;color:white;"
                        };
                    }

                    if (selisih === 0) {
                        return {
                            style: "background-color:#ff9d00;color:white;"
                        };
                    }

                    if (
                        parseInt(rowData.top) === 0 &&
                        tempoDate === today
                    ) {
                        return {};
                    }

                    if (daysDiff > 0 && daysDiff <= 4) {
                        return {
                            style: "background-color:#ffd503;color:white;"
                        };
                    }

                    if (daysDiff < 0) {
                        return {
                            style: "background-color:red;color:white;"
                        };
                    }

                    return {};
                }
            });


            // Navigation
            $('#overdue90').jqGrid('navGrid', "#overdue90Pager", {
                search: false,
                add: false,
                edit: false,
                del: false,
                refresh: true
            });

            // Frozen columns
            $("#overdue90").jqGrid('setFrozenColumns');

            // Live Search
            function filterWarna2(warna) {
                let grid = $("#overdue90");
                let postData = grid.jqGrid('getGridParam', 'postData');

                postData.filters = JSON.stringify({
                    groupOp: "AND",
                    rules: warna ? [{
                        field: "warna_status",
                        op: "eq",
                        data: warna
                    }] : []
                });

                grid.jqGrid('setGridParam', {
                    search: true,
                    postData: postData
                }).trigger("reloadGrid");
            }


            $("#overdue90-lebih").jqGrid({
                url: '{{ route('data-rekap.piutang') }}',
                mtype: 'GET',
                postData: {
                    overdue90_lebih: true,
                    userId: '{{ auth()->id() }}'
                },
                datatype: 'json',

                footerrow: true,
                userDataOnFooter: true,

                colModel: [{
                        name: 'id',
                        hidden: true
                    },
                    {
                        label: 'Invoice',
                        name: 'invoice',
                        width: 80,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Nama Customer',
                        name: 'customer',
                        width: 120,
                        align: "left",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Harga (INC.PPN)',
                        name: 'jumlah_harga',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    },
                    {
                        label: 'Tanggal',
                        name: 'tanggal',
                        width: 50,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        hidden: true
                    },
                    {
                        label: 'TGL Kirim Inv',
                        name: 'ditagih_tgl',
                        width: 50,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'TOP',
                        name: 'top',
                        width: 30,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Jatuh Tempo TGL',
                        name: 'tempo',
                        width: 80,
                        align: "center",
                        formatter: 'date',
                        formatoptions: {
                            newformat: 'Y-m-d'
                        },
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Dibayar TGL',
                        name: 'dibayar_tgl',
                        width: 50,
                        align: "center",
                        sortable: true,
                        search: true
                    },
                    {
                        label: 'Dibayar',
                        name: 'sebesar',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    },
                    {
                        label: 'PPH',
                        name: 'pph',
                        width: 100,
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
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
                        align: "right",
                        formatter: 'currency',
                        formatoptions: {
                            thousandsSeparator: ',',
                            decimalSeparator: '.',
                            prefix: ''
                        },
                        sortable: true
                    }
                ],

                autowidth: true,
                shrinkToFit: true,
                height: 'auto',
                loadonce: false,
                rowNum: 150,
                rowList: [150, 500, 1000],
                viewrecords: true,
                pager: "#overdue90-lebihPager",
                caption: "Rekap Piutang Belum Bayar",

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

                loadComplete: function(response) {

                    $('#total-kurang-bayar-overdue90-lebih').text(
                        'Rp ' + Number(response.total_kurang_bayar).toLocaleString('id-ID')
                    );

                },

                rowattr: function(rowData) {

                    if (!rowData.tempo) return {};

                    let today = new Date().toISOString().split('T')[0];
                    let tempoDate = new Date(rowData.tempo).toISOString().split('T')[0];

                    let selisih = parseFloat(rowData.pph || 0) -
                        parseFloat(rowData.kurang_bayar || 0);

                    let timeDiff = new Date(rowData.tempo) - new Date();
                    let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                    if (parseFloat(rowData.kurang_bayar) === 0) {
                        return {
                            style: "background-color:#3fae43;color:white;"
                        };
                    }

                    if (parseFloat(rowData.kurang_bayar) < 0) {
                        return {
                            style: "background-color:#0099ff;color:white;"
                        };
                    }

                    if (selisih === 0) {
                        return {
                            style: "background-color:#ff9d00;color:white;"
                        };
                    }

                    if (
                        parseInt(rowData.top) === 0 &&
                        tempoDate === today
                    ) {
                        return {};
                    }

                    if (daysDiff > 0 && daysDiff <= 4) {
                        return {
                            style: "background-color:#ffd503;color:white;"
                        };
                    }

                    if (daysDiff < 0) {
                        return {
                            style: "background-color:red;color:white;"
                        };
                    }

                    return {};
                }
            });


            // Navigation
            $('#overdue90-lebih').jqGrid('navGrid', "#overdue90-lebihPager", {
                search: false,
                add: false,
                edit: false,
                del: false,
                refresh: true
            });

            // Frozen columns
            $("#overdue90-lebih").jqGrid('setFrozenColumns');

            // Live Search
            function filterWarna2(warna) {
                let grid = $("#overdue90-lebih");
                let postData = grid.jqGrid('getGridParam', 'postData');

                postData.filters = JSON.stringify({
                    groupOp: "AND",
                    rules: warna ? [{
                        field: "warna_status",
                        op: "eq",
                        data: warna
                    }] : []
                });

                grid.jqGrid('setGridParam', {
                    search: true,
                    postData: postData
                }).trigger("reloadGrid");
            }
        </script>
    @endsection
