@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
    <style>
        .autocomplete {
            position: relative;
            display: inline-block;
        }
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            /*position the autocomplete items to be the same width as the container:*/
            top: 100%;
            left: 0;
            right: 0;
        }
        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }
        .autocomplete-items div:hover {
            /*when hovering an item:*/
            background-color: #e9e9e9;
        }
        .autocomplete-active {
            /*when navigating through the items using the arrow keys:*/
            background-color: DodgerBlue !important;
            color: #ffffff;
        }
        .dataTables_scrollBody > table > thead > tr {
            visibility: collapse;
            height: 0px !important;
        }
        td:hover {
            cursor: pointer;
        }
        table.dataTable tbody th, table.dataTable tbody td{
            padding: 0px 10px !important;
        }
        .select2.select2-container.select2-container--default{
            width: 100% !important;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" id="tambah">Tambah Sangu Sopir</button>
                <button class="py-2 px-3 btn btn-primary" id="edit">Edit Sangu Sopir</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem" id="table-sangu">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Tanggal</th>
                                <th>Tujuan</th>
                                <th>Borongan 20'</th>
                                <th>Borongan Kuli 20'</th>
                                <th>Borongan 40'</th>
                                <th>Borongan Kuli 40'</th>
                                <th>Borongan Combo 2x20</th>
                                <th>Borongan Kuli Combo 2x20</th>
                                <th>Status</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasSanguSopir" aria-labelledby="offcanvasSanguSopirLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSanguSopirLabel">Form Borongan Sopir</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('sangusopir.store') }}" method="post" id="form">
                <input type="hidden" name="id" id="id">
                <div id="message" class="my-3 text-center text-white alert alert-success py-2 px-5"></div>
                <div id="message-error" class="my-3 text-center text-white alert alert-danger py-2 px-5">Harap Lengkapi Form</div>
                @csrf
                @include('admin.sangusopir.form',['sangusopir'=>[]])
                <div class="col-12 mb-2 px-1">
                    <button type="button" id="add-btn" class="btn btn-success btn-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('assets/js/autocomplete.js')}}"></script>
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script>
    $(function() {
        var lokasi = @json($lokasi);
        autocomplete(document.querySelector("[name='tujuan']"), lokasi);
    });
</script>
    <script>
        $('#edit').hide();
        $('#message').hide();
        $('#message-error').hide();
        let table = $('#table-sangu').DataTable({
            processing: true,
            serverSide: true,
            scrollY: '50vh',
            select:true,
            ajax:{
                url: '{{ route('sangusopir.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id', },
                { data: 'created_at', name: 'created_at' },
                { data: 'tujuan', name: 'tujuan' },
                { data: 'ukuran_20', name: 'ukuran_20' },
                { data: 'borongan_kuli_20', name: 'borongan_kuli_20' },
                { data: 'ukuran_40', name: 'ukuran_40' },
                { data: 'borongan_kuli_40', name: 'borongan_kuli_40' },
                { data: 'ukuran_combo', name: 'ukuran_combo' },
                { data: 'borongan_kuli_combo', name: 'borongan_kuli_combo' },
                { data: 'is_active', name: 'is_active' },
                // { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        var myOffcanvas = document.getElementById('offcanvasSanguSopir')
        var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)

        $('#add-btn').click(function (e) {
            if (confirm('Are you sure?')) {
                let form = $("#form").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ route('api.sangusopir.createorupdate') }}",
                    data: form,
                    success: function (response) {
                        table.ajax.reload()
                        $('#message').html('Data berhasil disimpan');
                        $('#message').show();
                        $('#id').val('');
                        $('#tujuan').val('').trigger('change');
                        $('#ukuran_20').val('');
                        $('#ukuran_40').val('');
                        $('#ukuran_combo').val('');
                        $('#borongan_kuli_20').val('');
                        $('#borongan_kuli_40').val('');
                        $('#borongan_kuli_combo').val('');
                        $('#is_active').val(0);
                        setTimeout(() => {
                            $('#message').hide();
                        }, 5000);
                    }
                });
            }
        });

        $('#table-sangu tbody').on( 'click', 'tr', function () {
            let id =  table.row( this ).data().id;
            let tujuan = table.row(this).data().tujuan;
            let ukuran_20 = table.row(this).data().ukuran_20;
            let borongan_kuli_20 = table.row(this).data().borongan_kuli_20;
            let ukuran_40 = table.row(this).data().ukuran_40;
            let borongan_kuli_40 = table.row(this).data().borongan_kuli_40;
            let ukuran_combo = table.row(this).data().ukuran_combo;
            let borongan_kuli_combo = table.row(this).data().borongan_kuli_combo;
            let is_active = table.row(this).data().is_active;
            if(is_active=='Aktif'){
                is_active = 1;
            }else{
                is_active = 0;
            }
            $('#id').val(id);
            $('#tujuan').val(tujuan).trigger('change');
            $('#ukuran_20').val(ukuran_20);
            $('#ukuran_40').val(ukuran_40);
            $('#ukuran_combo').val(ukuran_combo);
            $('#borongan_kuli_20').val(borongan_kuli_20);
            $('#borongan_kuli_40').val(borongan_kuli_40);
            $('#borongan_kuli_combo').val(borongan_kuli_combo);
            $('#is_active').val(is_active);
            $('#edit').show();
        });

        $('#edit').click(function (e) {
            bsOffcanvas.show();
        });

        $('#tambah').click(function (e) {
            $('#id').val('');
            $('#tujuan').val('').trigger('change');
            $('#ukuran_20').val('');
            $('#ukuran_40').val('');
            $('#ukuran_combo').val('');
            $('#borongan_kuli_20').val('');
            $('#borongan_kuli_40').val('');
            $('#borongan_kuli_combo').val('');
            $('#is_active').val(0);
            bsOffcanvas.show();
        });

    </script>
@endsection
