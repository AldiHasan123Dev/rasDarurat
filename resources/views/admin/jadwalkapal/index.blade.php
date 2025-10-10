@extends('layouts.admin')

@section('style')
    {{-- jQuery UI untuk jqGrid --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- jqGrid CSS --}}
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

    <style>
        table.dataTable tbody th,
        table.dataTable tbody td {
            padding: 0px 10px !important;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJadwalKapal"
                    aria-controls="offcanvasJadwalKapal">Tambah JadwalKapal</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    {{-- ✅ Tabel DataTables --}}
                    <table class="table table-sm nowrap w-100" id="dataTableJadwal" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>Tools</th>
                                <th>ID.</th>
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>Pelayaran</th>
                                <th>Rute</th>
                                <th>Closing</th>
                                <th>Etd</th>
                                <th>Td</th>
                                <th>Eta</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ jqGrid untuk Monitoring ETD --}}
    <div class="container mt-2">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white fw-bold">
                📅 Reminders Close TD (Tanggal ETD sudah berlalu)
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    <em>
                        ⚠️ Tabel ini idealnya <strong>kosong</strong> supaya tidak menghambat proses penagihan,
                        karena <strong>Close TD</strong> merupakan salah satu syarat untuk
                        <strong>PRE-Invoice</strong>.
                    </em>
                </p>

                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
            </div>
        </div>


        {{-- ✅ Offcanvas Form Jadwal Kapal --}}
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasJadwalKapal"
            aria-labelledby="offcanvasJadwalKapalLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasJadwalKapalLabel">Form JadwalKapal</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form action="{{ route('jadwalkapal.store') }}" method="post">
                    @csrf
                    @include('admin.jadwalkapal.form', ['kapal' => $kapal, 'jadwalkapal' => []])
                </form>
            </div>
        </div>
    @endsection

    @section('script')
        {{-- ✅ jQuery utama (hanya sekali) --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        {{-- Bootstrap --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        {{-- ✅ DataTables --}}
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

        {{-- ✅ jqGrid --}}
        <script src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>

        {{-- ✅ Select2 --}}
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function() {
                // Disable copy/paste
                document.oncontextmenu = new Function("return false");
                $('body').bind('cut copy paste', function(e) {
                    e.preventDefault();
                });

                // === DataTables ===
                $('#dataTableJadwal').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('jadwalkapal.data') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    },
                    columns: [{
                            data: 'tools',
                            name: 'tools',
                            visible: false
                        },
                        {
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'kapal',
                            name: 'kapal.nama'
                        },
                        {
                            data: 'voyage',
                            name: 'voyage'
                        },
                        {
                            data: 'pelayaran',
                            name: 'pelayaran.nama'
                        },
                        {
                            data: 'rute',
                            name: 'rute'
                        },
                        {
                            data: 'closing',
                            name: 'closing'
                        },
                        {
                            data: 'etd',
                            name: 'etd'
                        },
                        {
                            data: 'td',
                            name: 'td'
                        },
                        {
                            data: 'eta',
                            name: 'eta'
                        },
                        {
                            data: 'keterangan',
                            name: 'keterangan'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });

                // === jqGrid ===
                $("#jqGrid").jqGrid({
                    url: "{{ route('jqgrid.jadwalkapal') }}",
                    datatype: "json",
                    mtype: "GET",
                    colModel: [{
                            name: 'id',
                            label: 'ID'
                        },
                        {
                            name: 'nama_kapal',
                            label: 'Kapal'
                        },
                        {
                            name: 'nama_pelayaran',
                            label: 'Pelayaran'
                        },
                        {
                            name: 'rute',
                            label: 'Rute'
                        },
                        {
                            name: 'voyage',
                            label: 'Voyage'
                        },
                        {
                            name: 'ba_kirim',
                            label: 'BA Kirim'
                        },
                        {
                            name: 'etd',
                            label: 'ETD'
                        },
                        {
                            name: 'eta',
                            label: 'ETA'
                        },
                        {
                            name: 'keterangan',
                            label: 'Keterangan'
                        }
                    ],
                    autowidth: true, // ✅ Lebar menyesuaikan container
                    shrinkToFit: true, // ✅ Kolom disesuaikan dengan total lebar
                    responsive: true, // ✅ Tambahan agar grid menyesuaikan layar
                    height: 'auto', // ✅ Tinggi otomatis menyesuaikan jumlah baris
                    gridview: true,
                    height: 250,
                    rowNum: 25,
                    rowList: [10, 25, 50, 100],
                    pager: "#jqGridPager",
                    viewrecords: true,
                    loadonce: true,
                    caption: "📦 Close TD",
                });

                $('#jqGrid').jqGrid('filterToolbar', {
                    stringResult: true,
                    searchOnEnter: false,
                    defaultSearch: 'cn'
                });
                $('#jqGrid').jqGrid('navGrid', "#jqGridPager", {
                    search: false,
                    add: false,
                    edit: false,
                    del: false,
                    refresh: true
                });

                // === Select2 ===
                $("select[name=kapal_id]").select2({
                    dropdownParent: $('#offcanvasJadwalKapal'),
                    tags: true
                });
                $("select[name=pelayaran_id]").select2({
                    dropdownParent: $('#offcanvasJadwalKapal')
                });
            });
        </script>
    @endsection
