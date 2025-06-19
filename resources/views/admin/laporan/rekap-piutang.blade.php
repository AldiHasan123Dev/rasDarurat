@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
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
    </style>
@endsection
@section('content')
    <div class="container">

        <div class="card">
            <div class="card-body">
                {{-- Filter Kedua --}}
                <div class="section-title">Rekap Piutang</div>
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

    <div class="container">

        <div class="card">
            <div class="card-body">
                {{-- Filter Kedua --}}
                <div class="section-title">Summary Rekap Piutang</div>
                <div class="row g-3">
                    @php
                        $year = date('Y');
                    @endphp

                    <div class="col-md-3">
                        <label class="form-label">Pilih Periode Inv</label>
                        <select name="thn_inv" id="thn_inv" class="form-control">
                            @for ($i = $year - 5; $i <= $year + 5; $i++)
                                <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3 ms-auto text-end">
                        {{-- <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-success btn-sm" type="button" onclick="searchJurnal1()">Search</button>
                        <a class="btn btn-sm btn-warning" target="_blank" id="edit-coa1">Edit COA</a>
                    </div> --}}
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
            function reloadGridWithFilters() {
                $("#jqGrid1").jqGrid('setGridParam', {
                    datatype: 'json',
                    postData: {
                        thn_inv: $('#thn_inv').val(),
                    },
                    page: 1
                }).trigger('reloadGrid');
            }

            $('#thn_inv').on('change', function() {
                reloadGridWithFilters();
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
                    align: "center",
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
                    align: "center",
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
                    align: "center",
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
                    align: "center",
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
                    align: "center",
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
            pager: "#jqGridPager",
            caption: "Jurnal List",
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
            url: '{{ route('data-rekap-total.piutang') }}',
            mtype: 'GET',
            postData: {
                thn_inv: function() {
                    return $('#thn_inv').val();
                }
            },
            datatype: 'json',
            colModel: [{
                    label: 'No',
                    name: 'no',
                    width: 30,
                    align: "center",
                    sortable: false
                },
                {
                    label: 'Bulan',
                    name: 'bulan',
                    width: 40,
                    align: "center",
                    sortable: true
                },
                {
                    label: 'Jumlah Invoice',
                    name: 'total_invoice',
                    width: 40,
                    align: "center",
                    sortable: true
                },
                {
                    label: 'Nilai Invoice',
                    name: 'nilai_invoice',
                    width: 120,
                    align: "right",
                    formatter: 'currency',
                    formatoptions: {
                        thousandsSeparator: ','
                    },
                    sortable: true
                },
                {
                    label: 'Dibayar',
                    name: 'telah_bayar',
                    width: 120,
                    align: "right",
                    formatter: 'currency',
                    formatoptions: {
                        thousandsSeparator: ','
                    },
                    sortable: true
                },
                {
                    label: 'Belum Dibayar',
                    name: 'belum_dibayar',
                    width: 120,
                    align: "right",
                    formatter: 'currency',
                    formatoptions: {
                        thousandsSeparator: ','
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
            caption: "Jurnal List",
            jsonReader: {
                repeatitems: false,
                root: "rows",
                page: "page",
                total: "total",
                records: "records"
            },

            loadComplete: function(data) {
                // Mengambil nilai sum_telah_bayar dari luar rows
                var sumTelahBayar = data.sum_telah_bayar;
                var sumBelumBayar = data.sum_belum_bayar;
                var countInvoice = data.count_invoice;
                var sumInvoice = data.sum_nilai_invoice;

                // Menambahkan sum_telah_bayar ke footer
                $("#jqGrid1").jqGrid('footerData', 'set', {
                    "bulan": "Total",
                    "total_invoice": countInvoice,
                    "telah_bayar": sumTelahBayar,
                    "belum_dibayar": sumBelumBayar,
                    "nilai_invoice": sumInvoice // Menampilkan sum_telah_bayar di footer
                });
            },
            footerrow: true,
            userDataOnFooter: true,

            onCellSelect: function(rowId, iRow, iCol, e) {
                let nomor = $(this).jqGrid('getCell', rowId, 'nomor');
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
    </script>
@endsection
