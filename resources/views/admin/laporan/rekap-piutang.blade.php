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
            </ul>
        </div>
    </div>
</nav>


    <div class="card">
        <div class="card-body">
                {{-- <a href="{{ route('rekap_piutang.blum') }}" class="btn btn-success mb-3">
                         <i class="fa fa-download"></i> Export Outstanding All (Tidak Termasuk Inv Manual)
                     </a> --}}
                <div class="section-title">Rekap Piutang (Belum Bayar)</div>
                {{-- Filter Kedua --}}
                <div class="col-md-3">


                    {{-- <div class="col-md-6 mb-5 text-end">
                        <label class="form-label d-block">&nbsp;</label> 
                        <div class="d-flex gap-2 mb-2">
                            <button class="btn btn-sm btn-danger" onclick="filterWarna2('merah')">Merah</button>
                            <button class="btn btn-sm btn-warning" onclick="filterWarna2('kuning')">Kuning</button>
                            <button class="btn btn-sm btn-orange text-white" style="background-color: #ff9d00;"
                                onclick="filterWarna2('oranye')">Oranye</button>
                            <button class="btn btn-sm btn-success" onclick="filterWarna2('hijau')">Hijau</button>
                            <button class="btn btn-sm btn-primary" onclick="filterWarna2('biru')">Biru</button>
                            <button class="btn btn-sm btn-secondary" onclick="filterWarna2('')">Reset</button>
                        </div>
                    </div> --}}
                </div>


                {{-- Grid Kedua --}}
                <div class="table-wrapper">
                    <table id="jqGrid2"></table>
                    <div id="jqGridPager2"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">

        <div class="card">
            <div class="card-body">
                <div class="section-title">Rekap Piutang Add Cost (Invoice External)</div>
                {{-- Filter Kedua --}}
                <div class="col-md-3">

                    <div class="col-md-6 mb-5 text-end">
                        <label class="form-label d-block">&nbsp;</label> {{-- spacing --}}
                        <div class="d-flex gap-2 mb-2">
                            <button class="btn btn-sm btn-danger" onclick="filterWarna3('merah')">Merah</button>
                            <button class="btn btn-sm btn-warning" onclick="filterWarna3('kuning')">Kuning</button>
                            <button class="btn btn-sm btn-orange text-white" style="background-color: #ff9d00;"
                                onclick="filterWarna3('oranye')">Oranye</button>
                            <button class="btn btn-sm btn-success" onclick="filterWarna3('hijau')">Hijau</button>
                            <button class="btn btn-sm btn-primary" onclick="filterWarna3('biru')">Biru</button>
                            <button class="btn btn-sm btn-secondary" onclick="filterWarna3('')">Reset</button>
                        </div>
                    </div>
                </div>


                {{-- Grid Kedua --}}
                <div class="table-wrapper">
                    <table id="jqGrid3"></table>
                    <div id="jqGridPager3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">

        <div class="card">
            <div class="card-body">
                {{-- Filter Kedua --}}
                <div class="section-title">Rekap Piutang Invoice (Berdasarkan Customer)</div>
                <div class="col-md-3">
                    <div class="col-md-12">
                        <label class="form-label">Cari Berdasarkan Customers</label>
                        <select id="customers" name="customers" class="form-control" style="width: 100%;">
                            <option value="">-- Pilih Customer --</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->nama }}"
                                    {{ request('customers') == $c->nama ? 'selected' : '' }}>
                                    {{ $c->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-5 text-end">
                        <label class="form-label d-block">&nbsp;</label> {{-- spacing --}}
                        <div class="d-flex gap-2 mb-2">
                            <button class="btn btn-sm btn-danger" onclick="filterWarna1('merah')">Merah</button>
                            <button class="btn btn-sm btn-warning" onclick="filterWarna1('kuning')">Kuning</button>
                            <button class="btn btn-sm btn-orange text-white" style="background-color: #ff9d00;"
                                onclick="filterWarna1('oranye')">Oranye</button>
                            <button class="btn btn-sm btn-success" onclick="filterWarna1('hijau')">Hijau</button>
                            <button class="btn btn-sm btn-primary" onclick="filterWarna1('biru')">Biru</button>
                            <button class="btn btn-sm btn-secondary" onclick="filterWarna1('')">Reset</button>
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


    <div class="container mt-5">

        <div class="card">
            <div class="card-body">
                {{-- Filter Kedua --}}
                <div class="section-title">Rekap Piutang Invoice (Berdasarkan TF Masuk)</div>
                <div class="col-md-3">
                    <label class="form-label">Cari Nominal TF Masuk</label>
                    <input type="text" id="tf-masuk" name="tf-masuk" class="form-control" />
                </div>
                {{-- Grid Kedua --}}
                <div class="table-wrapper">
                    <table id="jqGrid5"></table>
                    <div id="jqGridPager5"></div>
                </div>
            </div>
        </div>
    </div>



    <div class="container mt-5">

        <div class="card">
            <div class="card-body">
                {{-- Filter Kedua --}}
                <div class="section-title">Rekap Piutang Invoice Perbulan</div>
                <div class="row g-3">
                    <div class="mt-3">
                        <div class="card shadow-sm border-0" style="background-color: #f8f9fa;">
                            <div class="card-body">
                                <strong class="mb-2 d-block">Keterangan Warna:</strong>
                                <ul style="list-style: none; padding-left: 0; font-size: 0.85rem; margin: 0;">
                                    <li class="mb-1">
                                        <span
                                            style="display:inline-block;width:15px;height:15px;background-color:#3fae43;border-radius:3px;margin-right:5px;"></span>
                                        <span>Hijau - Lunas</span>
                                    </li>
                                    <li class="mb-1">
                                        <span
                                            style="display:inline-block;width:15px;height:15px;background-color:#007bff;border-radius:3px;margin-right:5px;"></span>
                                        <span>Biru - Lebih Bayar</span>
                                    </li>
                                    <li class="mb-1">
                                        <span
                                            style="display:inline-block;width:15px;height:15px;background-color:#ff9d00;border-radius:3px;margin-right:5px;"></span>
                                        <span>Oranye - PPh Saja yang Belum Dibayar</span>
                                    </li>
                                    <li class="mb-1">
                                        <span
                                            style="display:inline-block;width:15px;height:15px;background-color:#ffd503;border-radius:3px;margin-right:5px;"></span>
                                        <span>Kuning - Jatuh Tempo Dalam 1-4 Hari</span>
                                    </li>
                                    <li>
                                        <span
                                            style="display:inline-block;width:15px;height:15px;background-color:red;border-radius:3px;margin-right:5px;"></span>
                                        <span>Merah - Lewat Jatuh Tempo</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <label class="form-label">Pilih Periode Bulan Inv</label>
                        <input type="month" id="tgl_inv" class="form-control" name="tgl_inv" autocomplete="off"
                            value="{{ date('Y-m') }}" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cari Invoice di bulan tersebut</label>
                        <input type="text" id="inv" name="inv" class="form-control" autocomplete="off" />
                    </div>
                    <div class="col-md-6 mb-5 text-end">
                        <label class="form-label d-block">&nbsp;</label> {{-- spacing --}}
                        <div class="d-flex gap-2 mb-2">
                            <button class="btn btn-sm btn-danger" onclick="filterWarna('merah')">Merah</button>
                            <button class="btn btn-sm btn-warning" onclick="filterWarna('kuning')">Kuning</button>
                            <button class="btn btn-sm btn-orange text-white" style="background-color: #ff9d00;"
                                onclick="filterWarna('oranye')">Oranye</button>
                            <button class="btn btn-sm btn-success" onclick="filterWarna('hijau')">Hijau</button>
                            <button class="btn btn-sm btn-primary" onclick="filterWarna('biru')">Biru</button>
                            <button class="btn btn-sm btn-secondary" onclick="filterWarna('')">Reset</button>
                        </div>

                    </div>
                </div>


                {{-- Grid Kedua --}}
                <div class="table-wrapper">
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
                </div>
            </div>
        </div>
    </div>

     <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                {{-- Filter Kedua --}}
                <div class="section-title">Rekap Piutang Invoice Baru</div>
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
        $(document).ready(function() {
            $('#customers').select2({
                placeholder: '-- Pilih Customer --',
                allowClear: true,
                width: '100%' // bukan 'resolve', agar 100%
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function reloadGridWithFilters() {
                $("#jqGrid").jqGrid('setGridParam', {
                    datatype: 'json',
                    postData: {
                        tgl_inv: $('#tgl_inv').val(),
                        inv: $('#inv').val()
                    },
                    page: 1
                }).trigger('reloadGrid');
            }

            $('#tgl_inv, #inv').on('change', function() {
                reloadGridWithFilters();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            function formatRibuan(angka) {
                return angka.replace(/\D/g, '') // hanya angka
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            function reloadGridWithFilters() {
                const tfMasukVal = $('#tf-masuk').val().replace(/[^0-9]/g, '');
                console.log("Memuat ulang grid dengan tf_masuk:", tfMasukVal);

                $("#jqGrid5").jqGrid('setGridParam', {
                    datatype: 'json',
                    postData: {
                        tf_masuk: tfMasukVal
                    },
                    page: 1
                }).trigger('reloadGrid');
            }

            $('#tf-masuk').on('input', function() {
                let val = $(this).val();
                let formatted = formatRibuan(val);
                $(this).val(formatted);
                reloadGridWithFilters();
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            function reloadGridWithFilters(customersValue = null) {
                const customers = customersValue ?? $('#customers')
                    .val(); // fallback ke value select jika tidak dikirim

                $("#jqGrid1").jqGrid('setGridParam', {
                    datatype: 'json',
                    postData: {
                        customers: customers
                    },
                    page: 1
                }).trigger('reloadGrid');
            }

            $('#customers').on('change', function() {
                const selectedCustomer = $(this).val();
                reloadGridWithFilters(selectedCustomer); // kirim value yang dipilih ke fungsi
            });
        });
    </script>

    <script>
        $("#jqGrid").jqGrid({
            url: '{{ route('data-rekap.piutang') }}',
            mtype: 'GET',
            postData: {
                tgl_inv: function() {
                    return $('#tgl_inv').val();
                },

                inv: function() {
                    return $('#inv').val();
                }
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
                    label: 'Nama Customer',
                    align: "center",
                    name: 'customer',
                    width: 120,
                    align: "left",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Harga (INC.PPN)',
                    align: "right",
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
                    name: 'tanggal',
                    align: "center",
                    label: 'Tanggal',
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
                    align: "center",
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
                    align: "center",
                    name: 'top',
                    width: 30,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Jatuh Tempo TGL',
                    align: "center",
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
                    align: "center",
                    name: 'dibayar_tgl',
                    width: 50,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Dibayar',
                    align: "right",
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
                    align: "right",
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
                    align: "right", // isi cell rata kanan
                    labelAlign: "right", // label header rata kanan
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
            pager: "#jqGridPager",
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
        $('#jqGrid').jqGrid('navGrid', "#jqGridPager", {
            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true
        });

        // Frozen columns
        $("#jqGrid").jqGrid('setFrozenColumns');

        // Live Search
        function filterWarna(warna) {
            let grid = $("#jqGrid");
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



        $("#jqGrid1").jqGrid({
            url: '{{ route('data-rekap.piutang') }}',
            mtype: 'GET',
            postData: {
                customers: function() {
                    return $('#customers').val();
                }
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
                    label: 'Nama Customer',
                    align: "center",
                    name: 'customer',
                    width: 120,
                    align: "left",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Harga (INC.PPN)',
                    align: "right",
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
                    name: 'tanggal',
                    align: "center",
                    label: 'Tanggal',
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
                    align: "center",
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
                    align: "center",
                    name: 'top',
                    width: 30,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Jatuh Tempo TGL',
                    align: "center",
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
                    align: "center",
                    name: 'dibayar_tgl',
                    width: 50,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Dibayar',
                    align: "right",
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
                    align: "right",
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
                    align: "right", // isi cell rata kanan
                    labelAlign: "right", // label header rata kanan
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
            pager: "#jqGridPager1",
            caption: "Rekap Piutang Berdasarkan Customer",
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
        $('#jqGrid1').jqGrid('navGrid', "#jqGridPager1", {
            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true
        });

        // Frozen columns
        $("#jqGrid1").jqGrid('setFrozenColumns');

        // Live Search
        function filterWarna1(warna) {
            let grid = $("#jqGrid1");
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

        $("#jqGrid2").jqGrid({
            url: '{{ route('data-rekap.piutang') }}',
            mtype: 'GET',
            postData: {
                full: true,
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
                    label: 'Nama Customer',
                    align: "center",
                    name: 'customer',
                    width: 120,
                    align: "left",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Harga (INC.PPN)',
                    align: "right",
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
                    name: 'tanggal',
                    align: "center",
                    label: 'Tanggal',
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
                    align: "center",
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
                    align: "center",
                    name: 'top',
                    width: 30,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Jatuh Tempo TGL',
                    align: "center",
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
                    align: "center",
                    name: 'dibayar_tgl',
                    width: 50,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Dibayar',
                    align: "right",
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
                    align: "right",
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
                    align: "right", // isi cell rata kanan
                    labelAlign: "right", // label header rata kanan
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
            pager: "#jqGridPager2",
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
        $('#jqGrid2').jqGrid('navGrid', "#jqGridPager2", {
            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true
        });

        // Frozen columns
        $("#jqGrid2").jqGrid('setFrozenColumns');

        // Live Search
        function filterWarna2(warna) {
            let grid = $("#jqGrid2");
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


        $("#jqGrid3").jqGrid({
            url: '{{ route('data-rekap-addcost.piutang') }}',
            mtype: 'GET',
            postData: {
                tgl_inv: function() {
                    return $('#tgl_inv').val();
                },

                inv: function() {
                    return $('#inv').val();
                }
            },
            datatype: 'json',
            colModel: [{
                    name: 'id',
                    hidden: true
                },
                {
                    label: 'Invoice External',
                    align: "center",
                    name: 'invoice_external',
                    width: 80,
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
                    label: 'Harga (INC.PPN)',
                    align: "right",
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
                    name: 'tanggal',
                    align: "center",
                    label: 'Tanggal',
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
                    label: 'TGL Invoice',
                    align: "center",
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
                    align: "center",
                    name: 'top',
                    width: 30,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Jatuh Tempo TGL',
                    align: "center",
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
                    align: "center",
                    name: 'dibayar_tgl',
                    width: 50,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Dibayar',
                    align: "right",
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
                    name: 'warna_status',
                    hidden: true
                },
                {
                    label: 'Kurang Bayar',
                    name: 'kurang_bayar',
                    width: 100,
                    align: "right", // isi cell rata kanan
                    labelAlign: "right", // label header rata kanan
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
            pager: "#jqGridPager3",
            caption: "Rekap Piutang Add-Cost",
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
        $('#jqGrid3').jqGrid('navGrid', "#jqGridPager3", {
            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true
        });

        // Frozen columns
        $("#jqGrid3").jqGrid('setFrozenColumns');

        // Live Search
        function filterWarna3(warna) {
            let grid = $("#jqGrid3");
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

        // $("#jqGrid1").jqGrid({
        //     url: '{{ route('data-rekap-total.piutang') }}',
        //     mtype: 'GET',
        //     postData: {
        //         thn_inv: function() {
        //             return $('#thn_inv').val();
        //         }
        //     },
        //     datatype: 'json',
        //     colModel: [{
        //             label: 'No',
        //             name: 'no',
        //             width: 30,
        //             align: "center",
        //             sortable: false
        //         },
        //         {
        //             label: 'Bulan',
        //             name: 'bulan',
        //             width: 40,
        //             align: "center",
        //             sortable: true
        //         },
        //         {
        //             label: 'Jumlah Invoice',
        //             name: 'total_invoice',
        //             width: 40,
        //             align: "center",
        //             sortable: true
        //         },
        //         {
        //             label: 'Nilai Invoice',
        //             name: 'nilai_invoice',
        //             width: 120,
        //             align: "right",
        //             formatter: 'currency',
        //             formatoptions: {
        //                 thousandsSeparator: ','
        //             },
        //             sortable: true
        //         },
        //         {
        //             label: 'Dibayar',
        //             name: 'telah_bayar',
        //             width: 120,
        //             align: "right",
        //             formatter: 'currency',
        //             formatoptions: {
        //                 thousandsSeparator: ','
        //             },
        //             sortable: true
        //         },
        //         {
        //             label: 'Belum Dibayar',
        //             name: 'belum_dibayar',
        //             width: 120,
        //             align: "right",
        //             formatter: 'currency',
        //             formatoptions: {
        //                 thousandsSeparator: ','
        //             },
        //             sortable: true
        //         }
        //     ],
        //     autowidth: true,
        //     shrinkToFit: true,
        //     height: 'auto',
        //     loadonce: false,
        //     rowNum: 150,
        //     rowList: [150, 500, 1000],
        //     viewrecords: true,
        //     pager: "#jqGridPager1",
        //     caption: "Jurnal List",
        //     jsonReader: {
        //         repeatitems: false,
        //         root: "rows",
        //         page: "page",
        //         total: "total",
        //         records: "records"
        //     },

        //     loadComplete: function(data) {
        //         // Mengambil nilai sum_telah_bayar dari luar rows
        //         var sumTelahBayar = data.sum_telah_bayar;
        //         var sumBelumBayar = data.sum_belum_bayar;
        //         var countInvoice = data.count_invoice;
        //         var sumInvoice = data.sum_nilai_invoice;

        //         // Menambahkan sum_telah_bayar ke footer
        //         $("#jqGrid1").jqGrid('footerData', 'set', {
        //             "bulan": "Sub Total",
        //             "total_invoice": countInvoice,
        //             "telah_bayar": sumTelahBayar,
        //             "belum_dibayar": sumBelumBayar,
        //             "nilai_invoice": sumInvoice // Menampilkan sum_telah_bayar di footer
        //         });
        //     },
        //     footerrow: true,
        //     userDataOnFooter: true,

        //     onCellSelect: function(rowId, iRow, iCol, e) {
        //         let nomor = $(this).jqGrid('getCell', rowId, 'nomor');
        //     }
        // });

        // // Navigation
        // $('#jqGrid1').jqGrid('navGrid', "#jqGridPager1", {
        //     search: false,
        //     add: false,
        //     edit: false,
        //     del: false,
        //     refresh: true
        // });

        // // Frozen columns
        // $("#jqGrid1").jqGrid('setFrozenColumns');

        $("#jqGrid5").jqGrid({
            url: '{{ route('data-rekap.piutang') }}',
            mtype: 'GET',
            postData: {

                tf_masuk: function() {
                    return $('#tf-masuk').val();
                }
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
                    label: 'Nama Customer',
                    align: "center",
                    name: 'customer',
                    width: 120,
                    align: "left",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Harga (INC.PPN)',
                    align: "right",
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
                    name: 'tanggal',
                    align: "center",
                    label: 'Tanggal',
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
                    align: "center",
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
                    align: "center",
                    name: 'top',
                    width: 30,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Jatuh Tempo TGL',
                    align: "center",
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
                    align: "center",
                    name: 'dibayar_tgl',
                    width: 50,
                    align: "center",
                    sortable: true,
                    search: true
                },
                {
                    label: 'Dibayar',
                    align: "right",
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
                    align: "right",
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
                    align: "right", // isi cell rata kanan
                    labelAlign: "right", // label header rata kanan
                    formatter: 'currency',
                    formatoptions: {
                        thousandsSeparator: ',',
                        decimalSeparator: '.',
                        prefix: ''
                    },
                    sortable: true
                },
                {
                    label: 'TF Masuk ??',
                    name: 'tf_masuk',
                    width: 100,
                    align: "right", // isi cell rata kanan
                    labelAlign: "right", // label header rata kanan
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
            pager: "#jqGridPager5",
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
        $('#jqGrid5').jqGrid('navGrid', "#jqGridPager5", {
            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true
        });

        // Frozen columns
        $("#jqGrid5").jqGrid('setFrozenColumns');

        // Live Search
        // function filterWarna(warna) {
        //     let grid = $("#jqGrid5");
        //     let postData = grid.jqGrid('getGridParam', 'postData');

        //     postData.filters = JSON.stringify({
        //         groupOp: "AND",
        //         rules: warna ? [{
        //             field: "warna_status",
        //             op: "eq",
        //             data: warna
        //         }] : []
        //     });

        //     grid.jqGrid('setGridParam', {
        //         search: true,
        //         postData: postData
        //     }).trigger("reloadGrid");
        // }
    </script>
@endsection
