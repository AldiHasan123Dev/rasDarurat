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
            <div class="section-title">Cek Jurnal Harian</div>
            <div class="row g-3 align-items-end">
                <div class="col-auto">
                    <button type="button" class="btn btn-bank {{ request('bank') == 'bank' ? 'btn-active' : '' }}"
                        onclick="setTipe('Bank')">Bank</button>
                    <input type="hidden" id="bank" name="bank" value="{{ request('bank') }}">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-bank" onclick="setKas('Kas')" id="btn-kas">Kas</button>
                    <input type="hidden" name="kas" id="kas" value="">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-bank" onclick="setJurnal('Jurnal')" id="btn-jnl">Jurnal</button>
                    <input type="hidden" name="jurnal" id="jurnal" value="">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-bank" onclick="setBkt('bkt')" id="btn-bkt">Bank Trucking</button>
                    <input type="hidden" name="bkt" id="bkt" value="">
                </div>
                <div class="col-md-3">
                    <input type="date" name="tgl" id="tgl" class="form-control">
                </div>
                <div class="col-md-3 ms-auto text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-success btn-sm" type="button" onclick="searchJurnal()">Search</button>
                        <a class="btn btn-sm btn-warning" target="_blank" id="edit-coa">Edit COA</a>
                    </div>
                </div>
            </div>

            {{-- Grid Pertama --}}
            <div class="table-wrapper">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
            </div>

            {{-- Filter Kedua --}}
            <div class="section-title">Pencarian Data Jurnal</div>
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
                        <a class="btn btn-sm btn-warning" target="_blank" id="edit-coa1">Edit COA</a>
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
        let kategori = @json($is_sample);
                $("#jqGrid1").jqGrid({
            url: '{{ route('jqgrid.jurnal') }}',
            mtype: 'GET',
            datatype: 'json',
            postData: {
                kategori: kategori
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
                 $('#edit-coa1').attr('href', @json(route('jurnal.edit.coa')) + '?jurnal=' + encodeURIComponent(nomor));

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
                    tipe: @json($tipe),
                    search: val
                }
            }).trigger('reloadGrid');
        });

        $("#jqGrid").jqGrid({
            url: '{{ route('jqgrid.jurnal') }}',
            mtype: 'GET',
            datatype: 'json',
            postData: {
                kategori: kategori
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
            pager: "#jqGridPager",
            caption: "Jurnal List",
            onCellSelect: function(rowId, iRow, iCol, e) {
                id = $(this).jqGrid('getCell', rowId, 'id');
                let nomor = $(this).jqGrid('getCell', rowId, 'nomor');
               $('#edit-coa').attr('href', @json(route('jurnal.edit.coa')) + '?jurnal=' + encodeURIComponent(nomor));

            },
            rowattr: function(item) {
                return {
                    "class": item.class
                };
            }
        });

        $('#jqGrid').jqGrid('navGrid', "#jqGridPager", {
            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true
        });
        $("#jqGrid").jqGrid('setFrozenColumns');

        $('#search').keyup(function(e) {
            let val = $(this).val();
            $("#jqGrid").jqGrid('setGridParam', {
                postData: {
                    month: @json($month),
                    tipe: @json($tipe),
                    search: val
                }
            }).trigger('reloadGrid');
        });

        function changeKategori(type) {
            kategori = type;
        }

      function setKas(kas) {
    const inputKas = document.getElementById('kas');
    const inputBank = document.getElementById('bank');
    const inputJurnal = document.getElementById('jurnal');
    const inputBkt = document.getElementById('bkt');
    const btnKas = document.getElementById('btn-kas');
    const btnBank = document.querySelectorAll('.btn-bank');
    const btnJurnal = document.getElementById('btn-jnl');
    const btnBkt = document.getElementById('btn-bkt');

    // Nonaktifkan yang lain
    inputBank.value = '';
    btnBank.forEach(btn => btn.classList.remove('btn-active'));

    inputJurnal.value = '';
    if (btnJurnal) btnJurnal.classList.remove('btn-active');

    inputBkt.value = '';
    if (btnBkt) btnBkt.classList.remove('btn-active');

    // Toggle kas
    if (inputKas.value === kas) {
        inputKas.value = '';
        btnKas.classList.remove('btn-active');
    } else {
        inputKas.value = kas;
        btnKas.classList.add('btn-active');
    }
}

function setBkt(bkt) {
    const inputKas = document.getElementById('kas');
    const inputBank = document.getElementById('bank');
    const inputJurnal = document.getElementById('jurnal');
    const inputBkt = document.getElementById('bkt');
    const btnKas = document.getElementById('btn-kas');
    const btnBank = document.querySelectorAll('.btn-bank');
    const btnJurnal = document.getElementById('btn-jnl');
    const btnBkt = document.getElementById('btn-bkt');

    // Nonaktifkan yang lain
    inputBank.value = '';
    btnBank.forEach(btn => btn.classList.remove('btn-active'));

    inputKas.value = '';
    if (btnKas) btnKas.classList.remove('btn-active');

    inputJurnal.value = '';
    if (btnJurnal) btnJurnal.classList.remove('btn-active');

    // Toggle bkt
    if (inputBkt.value === bkt) {
        inputBkt.value = '';
        btnBkt.classList.remove('btn-active');
    } else {
        inputBkt.value = bkt;
        btnBkt.classList.add('btn-active');
    }
}

function setJurnal(jurnal) {
    const inputKas = document.getElementById('kas');
    const inputBank = document.getElementById('bank');
    const inputJurnal = document.getElementById('jurnal');
    const inputBkt = document.getElementById('bkt');
    const btnKas = document.getElementById('btn-kas');
    const btnBank = document.querySelectorAll('.btn-bank');
    const btnJurnal = document.getElementById('btn-jnl');
    const btnBkt = document.getElementById('btn-bkt');

    // Nonaktifkan yang lain
    inputBank.value = '';
    btnBank.forEach(btn => btn.classList.remove('btn-active'));

    inputKas.value = '';
    if (btnKas) btnKas.classList.remove('btn-active');

    inputBkt.value = '';
    if (btnBkt) btnBkt.classList.remove('btn-active');

    // Toggle jurnal
    if (inputJurnal.value === jurnal) {
        inputJurnal.value = '';
        btnJurnal.classList.remove('btn-active');
    } else {
        inputJurnal.value = jurnal;
        btnJurnal.classList.add('btn-active');
    }
}

function setTipe(bank) {
    const inputBank = document.getElementById('bank');
    const inputKas = document.getElementById('kas');
    const inputJurnal = document.getElementById('jurnal');
    const inputBkt = document.getElementById('bkt');
    const btnKas = document.getElementById('btn-kas');
    const btnJurnal = document.getElementById('btn-jnl');
    const btnBank = document.querySelectorAll('.btn-bank');
    const btnBkt = document.getElementById('btn-bkt');

    let isActive = false;

    btnBank.forEach(btn => {
        if (btn.textContent.trim() === bank && btn.classList.contains('btn-active')) {
            isActive = true;
        }
        btn.classList.remove('btn-active');
    });

    if (isActive) {
        inputBank.value = '';
    } else {
        inputBank.value = bank;

        btnBank.forEach(btn => {
            if (btn.textContent.trim() === bank) {
                btn.classList.add('btn-active');
            }
        });

        inputKas.value = '';
        if (btnKas) btnKas.classList.remove('btn-active');

        inputJurnal.value = '';
        if (btnJurnal) btnJurnal.classList.remove('btn-active');

        inputBkt.value = '';
        if (btnBkt) btnBkt.classList.remove('btn-active');
    }
}




       function searchJurnal() {
    let tgl = $('#tgl').val();

    const bankInput = document.getElementById('bank');
    const bktInput = document.getElementById('bkt');
    const jurnalInput = document.getElementById('jurnal');
    const kasInput = document.getElementById('kas');

    const btnKas = document.getElementById('btn-kas');
    const btnJurnal = document.getElementById('btn-jnl');
    const btnBank = document.querySelectorAll('.btn-bank');
    const btnBkt = document.getElementById('btn-bkt');

    let bank = bankInput?.value || '';
    let bkt = bktInput?.value || '';
    let jurnal = jurnalInput?.value || '';
    let kas = kasInput?.value || '';

    // Reset filter jika keterangan dan container diisi
    

    // Jalankan pencarian di jqGrid
    $("#jqGrid").jqGrid('setGridParam', {
        postData: {
            kategori: "real",
            tgl,
            bank,
            kas,
            jurnal,
            bkt
        },
        page: 1
    }).trigger('reloadGrid');
}

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

