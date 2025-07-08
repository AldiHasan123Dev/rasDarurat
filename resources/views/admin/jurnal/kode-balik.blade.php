@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .table-responsive table {
            position: relative;
            overflow-y: scroll;
        }

        .ui-jqgrid .ui-jqgrid-htable th {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }

        .ui-jqgrid .ui-jqgrid-htable th {
            border-bottom: 2px solid #ccc;
        }

        .ui-jqgrid .ui-jqgrid-htable th {
            position: sticky !important;
            top: 0 !important;
            background: #fff !important;
            z-index: 999 !important;
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
            background-color: #1a532f !important;
            color: white !important;
            border-color: #1a532f !important;
        }

        .btn-active {
            background-color: #4ade80 !important;
            color: white !important;
            border-color: #4ade80 !important;
        }

        .ui-jqgrid .ui-jqgrid-htable th {
            position: sticky !important;
            top: 0 !important;
            background: white !important;
            z-index: 10 !important;
        }

        .ui-jqgrid .ui-jqgrid-bdiv {
            max-height: 70vh;
            overflow-y: auto !important;
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
                {{-- Title --}}
                <div class="section-title">Pembuatan Code Jurnal Balik</div>

                {{-- Filter --}}
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">COA</label>
                        <select name="coa" id="coa" class="form-control select2">
                            <option value="">Pilih Coa</option>
                            @foreach ($coa as $c)
                                <option value="{{ $c->id }}">{{ $c->kode }} - {{ $c->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"></label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm type-btn"
                                data-type="debit">Debit</button>
                            <button type="button" class="btn btn-outline-danger btn-sm type-btn"
                                data-type="credit">Credit</button>
                        </div>
                    </div>
                    {{-- Filter Bulan --}}
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <label class="form-label">Bulan</label>
                        {{-- Tombol Bulan --}}
                        @php
                            $months = [
                                '01' => 'Jan',
                                '02' => 'Feb',
                                '03' => 'Mar',
                                '04' => 'Apr',
                                '05' => 'Mei',
                                '06' => 'Jun',
                                '07' => 'Jul',
                                '08' => 'Agu',
                                '09' => 'Sep',
                                '10' => 'Okt',
                                '11' => 'Nov',
                                '12' => 'Des',
                            ];
                        @endphp
                        @foreach ($months as $num => $name)
                            <button type="button" id="month" class="btn btn-outline-primary btn-sm month-btn"
                                data-month="{{ $num }}">{{ $name }}</button>
                        @endforeach
                        <label class="form-label">Tahun</label>
                        <select id="filter-year" class="form-select form-select-sm" style="width: auto;">
                            @php
                                $currentYear = date('Y');
                                for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                                    echo "<option value='$y'>$y</option>";
                                }
                            @endphp
                        </select>
                        <div class="col-md-2 ms-auto text-end align-self-start">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-success btn-sm" type="button"
                                    onclick="searchJurnal1()">Search</button>

                            </div>
                        </div>
                    </div>
                    <a class="btn btn-sm btn-warning" style="width: 140px; margin-left: 20px;" id="edit-coa1">Simpan
                        Kode</a>
                    <a class="btn btn-sm btn-primary" style="width: 160px; margin-left: 20px;" id="excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>

                    <div class="col-md-2 ms-auto text-end align-self-start">
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-wrapper">
                    <table id="jqGrid1"></table>
                    <div id="jqGridPager1"></div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script src="{{ asset('assets/js/resize-column.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('#coa').select2({
                placeholder: "Pilih Coa",
                allowClear: true
            });

            // Inisialisasi jqGrid
            $("#jqGrid1").jqGrid({
                url: '{{ route('jqgrid.jurnal') }}',
                mtype: 'GET',
                datatype: 'json',
                colModel: [{
                        name: 'kode_input',
                        label: 'Kode',
                        width: 100,
                        formatter: function(cellval, opts, rowObject) {
                            return `<input type="text" value="${rowObject.kode}" class="form-control form-control-sm kode-input" data-id="${rowObject.id}" />`;
                        },
                        sortable: false,
                        align: 'center'
                    },
                    {
                        width: 50,
                        name: 'created_at',
                        label: 'Tanggal'
                    },
                    {
                        width: 100,
                        name: 'nomor',
                        label: 'Nomor Jurnal',
                        sortable: false
                    },
                    {
                        width: 100,
                        name: 'id',
                        label: 'id',
                        hidden: true
                    },
                    {
                        width: 100,
                        name: 'invoice_external',
                        label: 'Invoice External'
                    },
                    {
                        width: 100,
                        name: 'invoice_agen',
                        label: 'Invoice Agen'
                    },
                    {
                        width: 100,
                        name: 'invoice_vendor',
                        label: 'Invoice Vendor'
                    },
                    {
                        width: 100,
                        name: 'invoice_trucking',
                        label: 'Invoice Trucking'
                    },
                    {
                        width: 100,
                        name: 'container',
                        label: 'Container'
                    },
                    {
                        width: 700,
                        name: 'nama',
                        label: 'Keterangan'
                    },
                    {
                        width: 100,
                        name: 'debit',
                        label: 'Debit'
                    },
                    {
                        width: 100,
                        name: 'credit',
                        label: 'Credit'
                    },
                ],
                autowidth: true,
                shrinkToFit: false,
                height: 'auto',
                oadonce: true,
                rowNum: 100000000,
                rowList: [10, 25, 50, 100, 250, 500, 1000],
                viewrecords: true,
                pager: "#jqGridPager1",
                caption: "Jurnal List",
                onCellSelect: function(rowId) {
                    let nomor = $(this).jqGrid('getCell', rowId, 'nomor');
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
        });

        let selectedMonth = null;

        $('.month-btn').on('click', function() {
            $('.month-btn').removeClass('active'); // hilangkan class aktif dari semua
            $(this).addClass('active'); // tambahkan class aktif pada tombol yang diklik

            selectedMonth = $(this).data('month'); // simpan bulan yang dipilih
        });


        let selectedType = "";

        $('.type-btn').on('click', function() {
            $('.type-btn').removeClass('active');
            $(this).addClass('active');

            selectedType = $(this).data('type');
        });

        $('#edit-coa1').on('click', function(e) {
            e.preventDefault();

            let dataToSend = [];

            $('.kode-input').each(function() {
                const kode = $(this).val();
                const id = $(this).data('id');

                dataToSend.push({
                    id,
                    kode: kode.trim() === '' ? null : kode.trim()
                });
            });

            if (dataToSend.length === 0) {
                alert('Tidak ada data untuk disimpan.');
                return;
            }

            $.ajax({
                url: '{{ route('jurnal.simpanKode') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    data: dataToSend
                },
                success: function(res) {
                    alert('Kode berhasil disimpan!');
                    $("#jqGrid1").trigger('reloadGrid');
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyimpan.');
                }
            });

        });


        $('#excel').on('click', function(e) {
            e.preventDefault();

            let dataToSend = [];

            $('.kode-input').each(function() {
                const id = $(this).data('id');
                dataToSend.push(id);
            });

            if (dataToSend.length === 0) {
                alert('Tidak ada data untuk disimpan.');
                return;
            }

            $.ajax({
                url: '{{ route('jurnal.exportExcel') }}',
                method: 'POST',
                xhrFields: {
                    responseType: 'blob'
                },
                data: {
                    _token: '{{ csrf_token() }}',
                    data: JSON.stringify(dataToSend)
                },
                success: function(blob, status, xhr) {
                    // Ambil nama file dari Content-Disposition header jika tersedia
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    let filename = 'export-jurnal.xlsx';

                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (matches && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    // Buat dan klik link untuk download
                    const link = document.createElement('a');
                    const url = window.URL.createObjectURL(blob);
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                },
                error: function() {
                    alert('Gagal export Excel.');
                }
            });
        });



        function searchJurnal1() {
            const coa_id = $('#coa').val();
            const month = selectedMonth; // gunakan dari variable
            const year = $('#filter-year').val();
            console.log('FILTER:', {
                coa_id,
                month,
                year
            }); // <- Tambahkan ini

            $("#jqGrid1").jqGrid('setGridParam', {
                postData: {
                    kategori: "real",
                    coa_id: coa_id,
                    month_is: month,
                    year_is: year,
                    tipe: selectedType // ← tambahan di sini
                },
                page: 1
            }).trigger('reloadGrid');
        }
    </script>
@endsection
