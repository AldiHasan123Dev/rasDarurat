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
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark navbar-keuangan mb-4 shadow-sm">
            <div class="container-fluid">
                {{-- Toggle button (mobile) --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                    aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                {{-- Menu --}}
                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                        {{-- Rekap Piutang --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/rekap-piutang*') ? 'active' : '' }}"
                                href="{{ route('rekap.piutang') }}">
                                💰 Rekap Piutang
                            </a>
                        </li>

                        {{-- Lap Outstanding --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/rekap-blum-bayar*') ? 'active' : '' }}"
                                href="{{ route('rekap_piutang.blum_inv') }}">
                                📘 Lap Outstanding
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/piutang-overdue*') ? 'active' : '' }}"
                                href="{{ route('piutang.overdue') }}">
                                🚨 Piutang Overdue
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <div class="container mt-5">

        <div class="card">
            <div class="card-body">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-muted">
                                Total Kurang Bayar (Overdue Total Customer)
                            </span>
                            <span id="total-kurang-bayar-customer" class="fw-bold text-danger fs-4">
                                Rp 0
                            </span>
                        </div>
                    </div>
                </div>
                <div class="section-title">Overdue Grouping By Customer</div>
                <div class="table-wrapper">
                    <table id="overdue30"></table>
                    <div id="overdue30Pager"></div>
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
    url: '{{ route('data-rekap-grouping-customer.piutang') }}',
    mtype: 'GET',
    datatype: 'json',

    footerrow: true,
    userDataOnFooter: true,

    colModel: [
        {
            name: 'id',
            hidden: true
        },
        {
            label: 'Customer',
            name: 'customer',
            width: 200,
            align: 'left',
            search: true
        },
        {
            label: 'Marketing',
            name: 'marketing',
            width: 120,
            align: 'left',
            search: true
        },
        {
            label: 'CS',
            name: 'cs',
            width: 120,
            align: 'left',
            search: true
        },
        {
            label: 'TOP',
            name: 'top',
            width: 50,
            align: 'center',
            search: true
        },
        {
            label: 'PPH',
            name: 'pph',
            width: 120,
            align: 'right',
            search: false,
            formatter: 'currency',
            formatoptions: {
                thousandsSeparator: ',',
                decimalSeparator: '.',
                prefix: ''
            }
        },
        {
            label: 'Harga (INC.PPN)',
            name: 'jumlah_harga',
            width: 150,
            align: 'right',
            search: false,
            formatter: 'currency',
            formatoptions: {
                thousandsSeparator: ',',
                decimalSeparator: '.',
                prefix: ''
            }
        },
        {
            label: 'Sudah Dibayar',
            name: 'sebesar',
            width: 150,
            align: 'right',
            search: false,
            formatter: 'currency',
            formatoptions: {
                thousandsSeparator: ',',
                decimalSeparator: '.',
                prefix: ''
            }
        },
        {
            label: 'Kurang Bayar',
            name: 'kurang_bayar',
            width: 150,
            align: 'right',
            search: false,
            formatter: 'currency',
            formatoptions: {
                thousandsSeparator: ',',
                decimalSeparator: '.',
                prefix: ''
            }
        },
        {
            name: 'warna_status',
            hidden: true
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

    rowattr: function(rowData) {

        switch (rowData.warna_status) {

            case 'merah':
                return {
                    style: 'background-color:red;color:white;'
                };

            case 'oranye':
                return {
                    style: 'background-color:#ff9d00;color:white;'
                };

            case 'kuning':
                return {
                    style: 'background-color:#ffd503;color:black;'
                };

            default:
                return {};
        }
    },
    loadComplete: function(response) {

                $('#total-kurang-bayar-customer').text(
                    'Rp ' + Number(response.total_kurang_bayar).toLocaleString('id-ID')
                );

            },
});

$('#overdue30').jqGrid('navGrid', '#overdue30Pager', {
    search: false,
    add: false,
    edit: false,
    del: false,
    refresh: true
});

$('#overdue30').jqGrid('setFrozenColumns');
$("#overdue30").jqGrid('filterToolbar', {
    stringResult: true,
    searchOnEnter: false,
    defaultSearch: 'cn'
});

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
        postData: postData,
        page: 1
    }).trigger('reloadGrid');
}
    </script>
    @endsection