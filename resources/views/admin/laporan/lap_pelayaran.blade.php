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
    max-width: 500px;
    margin-bottom: 15px;
     display: flex;
    align-items: flex-end;
    gap: 20px; /* jarak antar elemen */
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
    background-color: #0d6efd; /* Biru Bootstrap */
    border-color: #0d6efd;
    color: white;
    transition: background-color 0.2s ease, transform 0.1s ease;
}

#btn-filter:hover {
    background-color: #0b5ed7; /* Biru lebih gelap */
    border-color: #0b5ed7;
}

#btn-filter:active {
    background-color: #0a58ca; /* Biru lebih pekat */
    border-color: #0a58ca;
    transform: scale(0.97); /* Sedikit mengecil saat ditekan */
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
    </style>
    <!-- jQuery UI (wajib untuk jqGrid) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- jqGrid CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/css/ui.jqgrid.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <div class="container mx-auto mt-8 px-4">
        
        <div class="form-wrapper">
    <div class="section-title">Harga OF Pelayaran</div>
    <div class="card-select-container">
    <div>
        <label for="lokasi" class="label-biru">Cari menurut Tujuan</label>
        <select id="lokasi" name="lokasi" class="select2 input-select">
            <option value="">-- Pilih Tujuan --</option>
            @foreach ($lokasi as $row)
                <option value="{{ $row->nama }}">{{ $row->nama }}</option>
            @endforeach
        </select>
    </div>

    <button class="btn btn-primary h-100" type="button" id="btn-filter">
        <i class="fa fa-search"></i> Cari
    </button>
</div>

            <div class="table-responsives">
                <table id="jqGrid"></table>
                <div id="jqGridPager"></div>
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
       $('#btn-filter').on('click', function(e) {
    e.preventDefault(); // Biar tidak reload halaman jika tombol type="submit"

    let selectedTujuan = $('#lokasi').val();

    $("#jqGrid").setGridParam({
        url: '{{ route('jqgrid.tarif.pelayaran') }}',
        datatype: 'json',
        postData: {
            harga_of: true,
            tujuans: selectedTujuan
        },
        page: 1
    }).trigger("reloadGrid");
});


        let id;
        $("#jqGrid").jqGrid({
             url: '{{ route('jqgrid.tarif.pelayaran') }}',
            mtype: 'GET',
            datatype: 'json',
             postData: {
        harga_of: true, // atau ambil dari input jika ada
    },
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
                    name: 'dari',
                    label: 'Dari',
                    width: 80,
                    search: true,
                    frozen: true
                },
                 {
                    name: 'tujuan',
                    label: 'Tujuan',
                    width: 80,
                    search: true,
                    frozen: true
                },
                {
                    name: 'pelayaran',
                    label: 'Pelayaran',
                    width: 220,
                    search: true,
                    frozen: true
                },
                //   {
                //     name: 'sales',
                //     label: 'Sales',
                //     width: 90,
                //     search: true
                // },
                // {
                //     name: 'voyage',
                //     label: 'Voyage',
                //     width: 140,
                //     search: true
                // },
                {
                    name: 'tipe',
                    label: 'Shipment',
                    width: 80,
                    search: true
                },
                {
                    name: 'komoditi',
                    label: 'Komoditas',
                    width: 120,
                    search: true
                },
                {
                    name: 'keterangan',
                    label: 'Keterangan',
                    width: 200,
                    search: true
                },
                {
                    name: 'tanggal',
                    label: 'Tanggal Info',
                    width: 100,
                    search: true,
                    formatter: 'date',
                    formatoptions: {
                        srcformat: 'd-m-y',
                        newformat: 'd-M-Y'
                    }
                },
                {
                    name: 'tarif_nominal',
                    label: 'Harga OF',
                    width: 150,
                    align: 'right',
                    formatter: 'number',
                    search: false
                },
                {
                    name: 'is_active',
                    label: 'Status',
                    width: 70,
                    search: true,
                    align: 'center'
                }
            ],
            autowidth: true,
            shrinkToFit: true,
            responsive: true,
            forceFit: false,
            height: 'auto',
            oadonce: true,
            rowNum: 20,
            rowList: [20, 50, 100, 250, 500, 1000],
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

        $(window).on('resize', function() {
    const newWidth = $("#jqGrid").closest(".ui-jqgrid").parent().width();
    $("#jqGrid").jqGrid("setGridWidth", newWidth, true);
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
