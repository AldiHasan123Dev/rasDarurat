@extends('layouts.admin')
@section('content')
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
            grid-template-columns: repeat(5, minmax(240px, 1fr));
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
            /* Card background putih */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Shadow lembut */
            border-radius: 8px;
            /* Sudut membulat */
            padding: 16px;
            /* Padding dalam card */
            width: 400px;
            /* Lebar card */
            margin-bottom: 16px;
            /* Jarak bawah */
            display: flex;
            flex-direction: column;
            /* Label dan select ke bawah */
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

        .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
    <!-- jQuery UI (wajib untuk jqGrid) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- jqGrid CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/css/ui.jqgrid.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <div class="container mx-auto mt-8 px-4">
@auth
    @if (auth()->user()->id == 23 || auth()->user()->id == 36)
        <div class="form-wrapper">
            <form action="{{ route('lap-pelayaran.store') }}" method="POST" id="formLapPelayaran">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="pelayaran_id">Pelayaran</label>
                        <select id="selectPelayaran" name="pelayaran_id" required class="form-control select2">
                            <option value="">-- Pilih Pelayaran --</option>
                            @foreach ($pelayaran as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tujuan">Tujuan</label>
                        <select id="selectLokasi" name="tujuan" required class="form-control select2">
                            <option value="">-- Pilih Tujuan --</option>
                            @foreach ($lokasi as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="comodity">Komoditas</label>
                        <input type="text" name="comodity" id="comodity">
                    </div>
                    <div class="form-group">
                        <label for="shipments">Shipment</label>
                        <select id="selectShipment" name="shipments" required class="form-control select2">
                            <option value="">-- Pilih Shipment --</option>
                            @foreach ($shipment as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kondisi">Kondisi</label>
                        <select id="selectKondisi" name="kondisi" required class="form-control select2">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach ($kondisi as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga OF</label>
                        <input type="number" step="0.01" name="harga" id="harga">
                    </div>
                    <div class="form-group">
                        <label for="jadwal_kapal_id">Berlaku Per Jadwal Kapal </label>
                        <select id="selectJadwal" name="jadwal_kapal_id" class="form-control select2">
                            <option value="">-- Pilih Jadwal Kapal --</option>
                            @foreach ($jadwalKapal as $row)
                                <option value="{{ $row->id }}">{{ $row->kapal->nama ?? '-' }} - {{ $row->voyage }} /
                                    ETD ({{ $row->etd }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sales">Sales Pelayaran</label>
                        <input type="text" name="sales" id="sales">
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan">
                    </div>
                    <div class="form-group">
                        <label for="tgl_info">Tanggal Info</label>
                        <input type="date" name="tgl_info" id="tgl_info">
                    </div>
                </div>
                <div class="form-action">
                    <button type="submit">Simpan Data</button>
                </div>
            </form>
        </div>
            @endif
@endauth

<div class="form-wrapper">
    <div class="card-select-container">
        <label for="lokasi" class="label-biru">Cari menurut Tujuan</label>
        <select id="lokasi" name="lokasi" class="select2 input-select">
            <option value="">-- Pilih Tujuan --</option>
            @foreach ($lokasi as $row)
            <option value="{{ $row->nama }}">{{ $row->nama }}</option>
            @endforeach
        </select>
    </div>
    
    @auth
        @if (auth()->user()->id == 23 || auth()->user()->id == 36)
            <div style="text-align: right; margin-bottom: 16px;">
                <button id="edit-btn" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#editModal">Edit</button>
            </div>
                @endif
@endauth






            <div class="table-responsives">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tarif Freight</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <form id="editForm" method="POST"
                        style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        @csrf
                        <input type="hidden" name="id" id="edit-id">

                        <!-- Kolom 1 -->
                        <div>
                            <label for="pelayaran_id">Pelayaran</label>
                            <select id="edit-pelayaran-id" name="pelayaran_id" class="form-control select2">
                                @foreach ($pelayaran as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="jadwal_kapal_id">Jadwal Kapal</label>
                            <select id="edit-jadwal-kapal-id" name="jadwal_kapal_id" class="form-control select2">
                                @foreach ($jadwalKapal as $row)
                                    <option value="{{ $row->id }}">{{ $row->kapal->nama ?? '-' }} -
                                        {{ $row->voyage }} / ETD ({{ $row->etd }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="shipments">Shipment</label>
                            <select id="edit-shipments" name="shipments" class="form-control select2">
                            @foreach ($shipment as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tujuan">Tujuan</label>
                            <select id="edit-tujuan" name="tujuan" class="form-control select2">
                                @foreach ($lokasi as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="kondisi">Kondisi</label>
                            <select id="edit-kondisi" name="kondisi" class="form-control select2">
                                @foreach ($kondisi as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="edit-comodity">Komoditas</label>
                            <input type="text" class="form-control" id="edit-comodity" name="comodity">
                        </div>

                        <div>
                            <label for="edit-harga">Harga</label>
                            <input type="number" step="0.01" class="form-control" id="edit-harga" name="harga"
                                required>
                        </div>

                        <div>
                            <label for="edit-keterangan">Keterangan</label>
                            <input type="text" class="form-control" id="edit-keterangan" name="keterangan">
                        </div>

                        <div>
                            <label for="edit-sales">Sales</label>
                            <input type="text" class="form-control" id="edit-sales" name="sales">
                        </div>

                        <div>
                            <label for="edit-tgl-info">Tgl Info</label>
                            <input type="date" name="tgl_info" class="form-control" id="edit-tgl-info">
                        </div>

                        <div style="grid-column: span 2;">
                            <label for="edit-status">Status</label>
                            <select class="form-control" id="edit-status" name="status" required>
                                <option value="1">AKTIF</option>
                                <option value="0">NON-AKTIF</option>
                            </select>
                        </div>

                        <!-- Footer tombol -->
                        <div class="modal-footer"
                            style="grid-column: span 2; display: flex; justify-content: flex-end; gap: 10px;">
                            <button type="submit" id="edit-save-btn" class="btn btn-success">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- jqGrid JS -->
    <script src="https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/js/jquery.jqgrid.min.js"></script>
    <!-- Select2 JS -->
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
        $('#edit-btn').on('click', function() {
            const id = $(this).data('id'); // ID dari baris terpilih
            let url = `/admin/harga-of/update/${id}`;
$('#form-edit').attr('action', url);
            if (!id) return;

            $.ajax({
                url: '{{ route('lap-pelayaran.show') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: function(res) {
                    if (res.success) {
                        const data = res.data;
                        $('#edit-id').val(data.id);
                        $('#edit-harga').val(data.harga);
                        $('#edit-sales').val(data.sales);
                        $('#edit-keterangan').val(data.keterangan);
                        $('#edit-status').val(data.status);
                        $('#edit-comodity').val(data.comodity);
                        $('#edit-tgl-info').val(data.tgl_info);
                        $('#edit-kondisi').val(data.kondisi).trigger('change');
                        $('#edit-tujuan').val(data.tujuan).trigger('change');
                        $('#edit-jadwal-kapal-id').val(data.jadwal_kapal_id).trigger('change');
                        $('#edit-shipments').val(data.shipments).trigger('change');
                        $('#edit-pelayaran-id').val(data.pelayaran_id).trigger('change');
                    } else {
                        alert('Data tidak ditemukan!');
                    }
                },
                error: function() {
                    alert('Gagal mengambil data dari server.');
                }
            });
        });

        $('#editForm').on('submit', function(e) {
    e.preventDefault();
        const id = $('#edit-id').val(); // Ambil dari hidden input

    if (!id) {
        alert('ID tidak ditemukan.');
        return;
    }

    const url = `/admin/harga-of/update/${id}`;
    const formData = $(this).serialize();

    $.ajax({
    url: url,
    method: 'POST',
    data: formData,
    success: function(res) {
        if (res.success) {
            alert('Data berhasil diperbarui.');
            $('#editModal').modal('hide');
            
            // Refresh jqGrid
            $('#jqGrid').trigger('reloadGrid');

        } else {
            alert('Gagal memperbarui data.');
        }
    },
    error: function(err) {
        alert('Gagal mengirim data ke server.');
        console.log(err.responseText);
    }
});

});
    </script>
    <script>
        $('#edit-btn').prop('disabled', true); // default disable

        // Filter otomatis saat pilih tujuan
        $('#lokasi').on('change', function() {
            let selectedTujuan = $(this).val();

            // Update jqGrid dengan parameter tujuan
            $("#jqGrid").setGridParam({
                url: '{{ route('data-lap-pelayaran.list') }}',
                datatype: 'json',
                postData: {
                    tujuan: selectedTujuan // kirim parameter
                },
                page: 1
            }).trigger("reloadGrid");
        });

        let id;
        $("#jqGrid").jqGrid({
            url: '{{ route('data-lap-pelayaran.list') }}',
            mtype: 'GET',
            datatype: 'json',
            colModel: [{
                    name: 'id',
                    label: 'ID',
                    hidden: true,
                    key: true
                },
                {
                    search: true,
                    name: 'class',
                    label: 'class',
                    hidden: true
                },
                 {
                    name: 'tujuan',
                    label: 'Tujuan',
                    width: 180,
                    search: true,
                    frozen: true
                },
                {
                    name: 'pelayaran',
                    label: 'Pelayaran',
                    width: 160,
                    search: true,
                    frozen: true
                },
                  {
                    name: 'sales',
                    label: 'Sales',
                    width: 100,
                    search: true
                },
                // {
                //     name: 'voyage',
                //     label: 'Voyage',
                //     width: 140,
                //     search: true
                // },
                {
                    name: 'shipments',
                    label: 'Shipment',
                    width: 140,
                    search: true
                },
                {
                    name: 'kondisi',
                    label: 'Kondisi',
                    width: 140,
                    search: true
                },
                {
                    name: 'comodity',
                    label: 'Komoditas',
                    width: 120,
                    search: true
                },
                 {
                    name: 'jadwal_kapal',
                    label: 'Berlaku Per',
                    width: 120,
                    search: true,
                    frozen: true
                },
                {
                    name: 'keterangan',
                    label: 'Keterangan',
                    width: 200,
                    search: true
                },
                {
                    name: 'tgl_info',
                    label: 'Tanggal Info',
                    width: 120,
                    search: true,
                    formatter: 'date',
                    formatoptions: {
                        srcformat: 'Y-m-d',
                        newformat: 'd-M-Y'
                    }
                },
                {
                    name: 'harga',
                    label: 'Harga OF',
                    width: 150,
                    align: 'right',
                    formatter: 'number',
                    search: false
                },
                {
                    name: 'status',
                    label: 'Status',
                    width: 80,
                    search: true,
                    align: 'center'
                }
            ],
            autowidth: true,
            shrinkToFit: true,
            height: 'auto',
            oadonce: true,
            rowNum: 25,
            rowList: [10, 25, 50, 100, 250, 500, 1000],
            viewrecords: true,
            pager: "#jqGridPager",
            caption: "Tarif Freight",

            onCellSelect: function(rowId, iRow, iCol, e) {
                const selectedId = $(this).jqGrid('getCell', rowId, 'id');
                $('#edit-btn').data('id', selectedId).prop('disabled', false); // aktifkan tombol
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
    </script>
@endpush
