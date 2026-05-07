@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <style>
        .form-wrapper {
            background: #fff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            max-width: 100%;
            box-sizing: border-box;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }


        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .form-group input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
            outline: none;
        }

        .form-action {
            margin-top: 24px;
        }

        .form-action button {
            background-color: #2563eb;
            color: white;
            font-weight: 500;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .form-action button:hover {
            background-color: #1d4ed8;
        }

        .card-select-container {
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 16px;
            width: 100%;
            max-width: 800px;
            margin-bottom: 15px;
            display: flex;
            align-items: flex-end;
            gap: 20px;
            /* jarak antar elemen */
        }


        .label-biru {
            color: #698af3;
            /* Warna teks biru */
            font-weight: 600;
            /* Tebal */
            font-size: 14px;
            margin-bottom: 6px;
            /* Jarak ke select */
        }

        .input-select {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 14px;
        }

        #edit-btn {
            background-color: #d0ca18;
            /* warna biru Bootstrap */
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            width: 100px;
            transition: background-color 0.4s ease, transform 0.1s ease;
            margin-bottom: 6px;
        }

        /* Saat mouse hover */
        #edit-btn:hover {
            background-color: rgb(147, 138, 49);
            /* warna lebih gelap saat hover */
        }

        /* Saat tombol ditekan */
        #edit-btn:active {
            background-color: #d0ca18;
            /* lebih gelap lagi */
            transform: scale(0.97);
            /* sedikit mengecil saat diklik */
        }

        /* Saat tombol disabled (opsional) */
        #edit-btn:disabled {
            background-color: #ffffff;
            cursor: not-allowed;
        }

        .modal-body form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 768px) {

            .form-grid,
            .modal-body form {
                grid-template-columns: 1fr !important;
            }

            .modal-footer {
                flex-direction: column;
                align-items: stretch;
            }

            #edit-btn {
                width: 100%;
            }
        }


        .modal-body form .form-group {
            display: flex;
            flex-direction: column;
        }

        /* Biar Status dan tombol simpan full lebar di bawah */
        .modal-body form .form-group-full {
            grid-column: span 2;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        #btn-filter {
            background-color: #0d6efd;
            /* Biru Bootstrap */
            border-color: #0d6efd;
            color: white;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        #btn-filter:hover {
            background-color: #0b5ed7;
            /* Biru lebih gelap */
            border-color: #0b5ed7;
        }

        #btn-filter:active {
            background-color: #0a58ca;
            /* Biru lebih pekat */
            border-color: #0a58ca;
            transform: scale(0.97);
            /* Sedikit mengecil saat ditekan */
        }


        /* Semua input dan select 100% lebar dalam form grid */
        #editForm select,
        #editForm input,
        #editForm textarea {
            width: 100%;
            box-sizing: border-box;
        }

        /* Pastikan Select2 full-width */
        .select2-container {
            width: 100% !important;
        }

        .select2-selection--single {
            height: 38px !important;
            /* sama tinggi dengan .form-control */
            padding: 6px 12px;
            line-height: 24px;
        }

        .select2-selection__rendered {
            line-height: 24px !important;
        }

        .section-title {
            font-weight: bold;
            font-size: 1rem;
            margin: 20px 0 10px;
        }

        .select2-selection__arrow {
            height: 36px !important;
        }

        .card .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card .fw-bold {
            font-size: 1.3rem;
        }

        #total-tarif {
            transition: all 0.3s ease-in-out;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                    {{-- <div class="d-flex gap-2">
                        <button class="py-2 px-3 btn btn-success" data-bs-toggle="modal" data-bs-target="#order"><i class="fas fa-plus"></i> Tambah Order Trucking</button>
                        <button class="py-2 px-3 btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit" id="btn-edit"><i class="fas fa-pencil"></i> Edit</button>
                    </div> --}}
                </div>
                <div class="row mb-3">
                    <!-- Filter -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-15">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="row g-5 align-items-end justify-content-center">

                                        <div class="col-md-3">
                                            <label for="lokasi" class="label-biru">Tujuan</label>
                                            <select id="lokasi" name="lokasi" class="select2 input-select">
                                                <option value="">-- Pilih Tujuan --</option>
                                                @foreach ($lokasi as $row)
                                                    <option value="{{ $row->nama }}">{{ $row->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="lokasi" class="label-biru">Pembayar</label>
                                            <select id="pembayar" name="pembayar" class="select2 input-select">
                                                <option value="">-- Pilih Pembayar --</option>
                                                @foreach ($pembayar as $row)
                                                    <option value="{{ $row->nama }}">{{ $row->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="lokasi" class="label-biru">Marketing</label>
                                            <select id="marketing" name="marketing" class="select2 input-select">
                                                <option value="">-- Pilih Marketing --</option>
                                                @foreach ($role as $row)
                                                    <option value="{{ $row->name }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="lokasi" class="label-biru">CS</label>
                                            <select id="cs" name="cs" class="select2 input-select">
                                                <option value="">-- Pilih CS --</option>
                                                @foreach ($role as $row)
                                                    <option value="{{ $row->name }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2 d-flex">
                                            <button id="btn-filter" class="btn btn-primary w-100">
                                                Cari
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Total Tarif -->
                    <div class="row justify-content-center mb-3">
                        <div class="col-md-6">
                            <div class="row g-3">

                                <!-- Total Tarif -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-0 text-center py-3">
                                        <h6 class="text-muted mb-1">Total Tarif</h6>
                                        <h4 class="fw-bold text-success mb-0">
                                            Rp <span id="total-tarif" style="font-size: 1.5rem;">0</span>
                                        </h4>
                                    </div>
                                </div>

                                <!-- Total Tarif TD -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-0 text-center py-3">
                                        <h6 class="text-muted mb-1">Total Tarif TD</h6>
                                        <h4 class="fw-bold text-primary mb-0">
                                            Rp <span id="total-tarif-td" style="font-size: 1.5rem;">0</span>
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-0 text-center py-3">
                                        <h6 class="text-muted mb-1">Total Tarif Ready Inv</h6>
                                        <h4 class="fw-bold text-primary mb-0">
                                            Rp <span id="total-tarif-baya" style="font-size: 1.5rem;">0</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="table-responsives">
                        <table id="jqGrid"></table>
                        <div id="jqGridPager"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
    <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script src="{{ asset('assets/js/resize-column.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Pilih --",
                allowClear: true
            });

            $('#editModal .select2').select2({
                dropdownParent: $('#editModal')
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('#coa').select2({
                placeholder: "Pilih Coa",
                allowClear: true
            });
        });
    </script>
    <script>
        $('#btn-filter').on('click', function(e) {
            e.preventDefault(); // Biar tidak reload halaman jika tombol type="submit"

            let selectedTujuan = $('#lokasi').val();
            let selectedMarketing = $('#marketing').val();
            let selectedPembayar = $('#pembayar').val();
            let selectedCs = $('#cs').val();

            $("#jqGrid").setGridParam({
                url: '{{ route('jqgrid.order') }}',
                datatype: 'json',
                postData: {
                    inv_null: true,
                    tujuans: selectedTujuan,
                    marketing: selectedMarketing,
                    cs: selectedCs,
                    pembayars: selectedPembayar
                },
                page: 1
            }).trigger("reloadGrid");
        });
        let data = [];
        let id;

        $("#jqGrid").jqGrid({
            url: '{{ route('jqgrid.order') }}',
            mtype: 'GET',
            datatype: 'json',
            postData: {
                inv_null: true
            },
            colModel: [{
                    name: 'no',
                    label: 'no'
                },
                {
                    name: 'pembayar',
                    label: 'pembayar'
                },
                {
                    name: 'id',
                    label: 'id',
                    hidden: true
                },
                {
                    name: 'class',
                    label: 'class',
                    hidden: true
                },
                {
                    name: 'marketing',
                    label: 'marketing'
                },
                {
                    name: 'cs',
                    label: 'cs'
                },
                {
                    name: 'penerima',
                    label: 'pengirim'
                },
                {
                    name: 'penerima',
                    label: 'penerima'
                },
                {
                    name: 'dari',
                    label: 'dari'
                },
                {
                    name: 'tujuan',
                    label: 'tujuan'
                },
                {
                    name: 'kapal',
                    label: 'kapal'
                },
                {
                    name: 'container',
                    label: 'container'
                },
                {
                    name: 'voyage',
                    label: 'voyage'
                },
                {
                    name: 'td',
                    label: 'td'
                },
                {
                    name: 'eta',
                    label: 'eta'
                },
                {
                    name: 'barang_diantar',
                    label: 'barang_diantar'
                },
                {
                    name: 'syarat_ba',
                    label: 'syarat_ba'
                },
                {
                    name: 'kondisi',
                    label: 'kondisi'
                },
                {
                    name: 'ba_diantar_sby',
                    label: 'ba_diantar_sby'
                },
                {
                    name: 'ba_kembali',
                    label: 'ba_kembali'
                },
                {
                    name: 'tarif1',
                    label: 'tarif',
                    align: 'right',
                    formatter: 'number',
                    formatoptions: {
                        decimalSeparator: ".",
                        thousandsSeparator: ",",
                        decimalPlaces: 2,
                        defaultValue: "0.00"
                    }
                },
            ],
            autowidth: true,
            shrinkToFit: true,
            oadonce: true,
            height: 'auto',
            rowNum: 9999999,
            pager: false, // tidak pakai pager
            pgbuttons: false, // sembunyikan tombol prev/next
            pginput: false, // sembunyikan input halaman
            viewrecords: true,
            pager: "#jqGridPager",
            caption: "Job Belum Inv",
            loadComplete: function(data) {
    let sum = 0;
    let sumTdNotNull = 0;
    let sumBaYa = 0;
    let sumBaYa1 = 0;
    let sumBaTidak = 0;

    let ids = $(this).jqGrid('getDataIDs');

    for (let i = 0; i < ids.length; i++) {
        let rowData = $(this).jqGrid('getRowData', ids[i]);

        let syaratBa = (rowData.syarat_ba || '').toString().trim().toLowerCase();
        let baKembali = (rowData.ba_kembali || '').toString().trim();
        let tarif = parseFloat((rowData.tarif1 || '0').replace(/,/g, '')) || 0;

        // total semua
        sum += tarif;

        // total jika td terisi
        if (rowData.td && rowData.td !== '-') {
            sumTdNotNull += tarif;
        }

        // BA Ya -> syarat_ba = iya dan ba_kembali = "-"
        if (syaratBa === 'iya' && baKembali === '-') {
            sumBaYa += tarif;
        }

        // BA Ya (Ada) -> syarat_ba = iya dan ba_kembali terisi
        if (
            syaratBa === 'iya' &&
            baKembali !== '' &&
            baKembali !== '-' &&
            baKembali !== '0'
        ) {
            sumBaYa1 += tarif;
        }

        // BA Tidak -> syarat_ba = tidak dan ba_kembali = "-"
        if (rowData.td && rowData.td !== '-' &&syaratBa === 'tidak' && baKembali === '-') {
            sumBaTidak += tarif;
        }
    }

    $("#total-tarif").text(sum.toLocaleString('en-US'));
    $("#total-tarif-td").text(sumTdNotNull.toLocaleString('en-US'));
    $("#total-tarif-baya").text((sumBaYa1+sumBaTidak).toLocaleString('en-US'));
    $("#total-tarif-baya1").text(sumBaYa1.toLocaleString('en-US'));
    $("#total-tarif-batidak").text(sumBaTidak.toLocaleString('en-US'));
},
            rowattr: function(item) {
                return {
                    "class": item.class
                };
            }
        });


        $("#jqGrid").jqGrid('setFrozenColumns');

        const rp = (num) => {
            return num.toLocaleString('en-US');
        }
    </script>
@endsection