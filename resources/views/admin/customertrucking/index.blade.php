@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<style>
table.dataTable tbody th, table.dataTable tbody td{
        padding: 0px 10px !important;
    }
</style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCustomerTrucking" aria-controls="offcanvasCustomerTrucking">Tambah Customer Trucking</button>
            </div>
            <div class="card-body">
                    <table class="table" style="font-size:.7rem" id="customer">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>PPH 23</th>
                                <th>Inv R1</th>
                                <th>Inv R2</th>
                                <th>Nama</th>
                                <th>PIC</th>
                                <th>Alamat</th>
                                <th>HP</th>
                                <th>NIK</th>
                                <th>NPWP</th>
                                <th>Nama NPWP</th>
                                <th>Alamat NPWP</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCustomerTrucking" aria-labelledby="offcanvasCustomerTruckingLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasCustomerTruckingLabel">Form Customer Trucking</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('customertrucking.store') }}" method="post">
                @csrf
                @include('admin.customertrucking.form', ['customertrucking'=> []])
            </form>
        </div>
    </div>

    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <div>
                    <button class="py-2 px-3 btn btn-success" id="add-tarif">Tambah Tarif Trucking</button>
                    <button class="py-2 px-3 btn btn-primary" id="edit-tarif">Edit Tarif</button>
                    <button class="py-2 px-3 btn btn-danger" id="delete-tarif">Hapus Tarif</button>
                </div>
                <div>
                    <p>Nama Customer: <span class="nama-cus">-</span></p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem" id="tarif-table">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Customer ID.</th>
                                <th>Tujuan ID.</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Tujuan</th>
                                <th>Tipe</th>
                                <th>Tarif</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTarifTrucking" aria-labelledby="offcanvasTarifTruckingLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTarifTruckingLabel">Form Tarif Trucking</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('tariftrucking.store') }}" method="post">
                <input type="hidden" name="tarif_id" id="tarif_id">
                <div id="message" class="my-3 text-center text-white alert alert-success py-2 px-5"></div>
                <div id="message-error" class="my-3 text-center text-white alert alert-danger py-2 px-5">Harap Lengkapi Form</div>
                @csrf
                @include('admin.tariftrucking.form')
                <div class="col-12 mb-2 px-1">
                    <button type="button" id="add-btn" class="btn btn-success btn-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
    <script>
        $('#add-tarif').hide();
        $('#edit-tarif').hide();
        $('#delete-tarif').hide();
        $('#message').hide();
        $('#message-error').hide();
        let id;
        let tarif_id;
        let table = $('#customer').DataTable({
            processing: true,
            serverSide: true,
            select:true,
            scrollY: '80vh',
            scrollX: true,
            scrollCollapse: true,
            ajax:{
                url: '{{ route('customertrucking.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'pph_23', name: 'pph_23' },
                { data: 'r1', name: 'r1' },
                { data: 'r2', name: 'r2' },
                { data: 'nama', name: 'nama' },
                { data: 'pic', name: 'pic' },
                { data: 'alamat', name: 'alamat' },
                { data: 'hp', name: 'hp' },
                { data: 'nik', name: 'nik' },
                { data: 'npwp', name: 'npwp' },
                { data: 'nama_npwp', name: 'nama_npwp' },
                { data: 'alamat_npwp', name: 'alamat_npwp' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        let table_tarif = $('#tarif-table').DataTable({
            processing: true,
            serverSide: true,
            select: true,
            ajax:{
                url: '{{ route('tariftrucking.data') }}',
                method:'POST',
                data:function( d) {
                    d.customer_id = id;
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'customer_id', name: 'customer_id', visible:false },
                { data: 'tujuan_id', name: 'tujuan_id', visible:false },
                { data: 'created_at', name: 'created_at', searchable:false },
                { data: 'customer', name: 'customer_trucking.nama', searchable:false },
                { data: 'tujuan', name: 'lokasi.nama' },
                { data: 'tipe', name: 'tipe' },
                { data: 'tarif', name: 'tarif' },
                { data: 'is_active', name: 'is_active' },
            ]
        });

        $('#customer tbody').on( 'click', 'tr', function () {
            id =  table.row( this ).data().id;
            $('.nama-cus').html(table.row(this).data().nama);
            $('#add-tarif').show();
            table_tarif.ajax.reload()
        });

        $('#tarif-table tbody').on( 'click', 'tr', function () {
            let customer_id =  table_tarif.row( this ).data().customer_id;
            tarif_id =  table_tarif.row( this ).data().id;
            id = customer_id;
            let tujuan_id =  table_tarif.row( this ).data().tujuan_id;
            let tipe =  table_tarif.row( this ).data().tipe;
            let is_active =  table_tarif.row( this ).data().is_active;
            let tarif =  table_tarif.row( this ).data().tarif;
            if(is_active=='Aktif'){
                is_active = 1;
            }else{
                is_active = 0;
            }
            $('#tarif_id').val(id);
            $('#customer_id').val(customer_id).trigger('change');
            $('#tujuan_id').val(tujuan_id).trigger('change');
            $('#tipe').val(tipe);
            $('#is_active').val(is_active);
            $('#tarif').val(tarif);
            $('#edit-tarif').show();
            $('#delete-tarif').show();
        });

        $("select[name=customer_id]").select2({
            dropdownParent: $('#offcanvasTarifTrucking')
        });
        $("select[name=tujuan_id]").select2({
            dropdownParent: $('#offcanvasTarifTrucking')
        });

        var myOffcanvas = document.getElementById('offcanvasTarifTrucking')
        var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)

        $('#add-btn').click(function (e) {
            if (confirm('Are you sure?')) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('api.tariftrucking.createorupdate') }}",
                    data: {
                        customer_id:id,
                        tujuan_id:$('#tujuan_id').val(),
                        tipe:$('#tipe').val(),
                        is_active:$('#is_active').val(),
                        tarif:$('#tarif').val(),
                        tarif_id:tarif_id,
                        updated_by:@json(Auth::id()),
                    },
                    success: function (response) {
                        table_tarif.ajax.reload()
                        $('#message').html('Data berhasil disimpan');
                        $('#message').show();
                        $('#tarif').val('');
                        // alert('Data Berhasil disimpan!');
                        // bsOffcanvas.hide();
                        setTimeout(() => {
                            $('#message').hide();
                        }, 5000);
                    }
                });
            }
        });

        $('#add-tarif').click(function (e) {
            e.preventDefault();
            $('#tarif_id').val('');
            $('#customer_id').val('').trigger('change');
            $('#tujuan_id').val('').trigger('change');
            $('#tipe').val('');
            $('#is_active').val('');
            $('#tarif').val('');
            $('#tarif_id').val(null);
            bsOffcanvas.show();
        });

        $('#edit-tarif').click(function (e) {
            let customer_id =  table_tarif.row('.selected').data().customer_id;
            tarif_id =  table_tarif.row('.selected').data().id;
            id = customer_id;
            let tujuan_id =  table_tarif.row('.selected').data().tujuan_id;
            let tipe =  table_tarif.row('.selected').data().tipe;
            let is_active =  table_tarif.row('.selected').data().is_active;
            let tarif =  table_tarif.row('.selected').data().tarif;
            if(is_active=='Aktif'){
                is_active = 1;
            }else{
                is_active = 0;
            }
            $('#tarif_id').val(id);
            $('#customer_id').val(customer_id).trigger('change');
            $('#tujuan_id').val(tujuan_id).trigger('change');
            $('#tipe').val(tipe);
            $('#is_active').val(is_active);
            $('#tarif').val(tarif);
            bsOffcanvas.show();
        });

        $('#delete-tarif').click(function (e) {
            if(confirm('Are you sure?')){
                $.ajax({
                    type: "POST",
                    url: "{{ route('api.tariftrucking.delete') }}",
                    data: {
                        id:tarif_id
                    },
                    success: function (response) {
                        alert('Data berhasil dihapus');
                        table_tarif.ajax.reload()
                    }
                });
            }
        });

        function changeStatus(id,val,type=null){
            if(type){
                $.ajax({
                    type: "POST",
                    url: "{{ url('api/customer-trucking') }}/"+id,
                    data: {
                        is_active:val,
                        api:true,
                        updated_by:@json(Auth::id()),
                    },
                    success: function (response) {
                        table.ajax.reload()
                    }
                });
            }else{
                $.ajax({
                    type: "POST",
                    url: "{{ route('api.tariftrucking.createorupdate') }}",
                    data: {
                        is_active:val,
                        tarif_id:id,
                        updated_by:@json(Auth::id()),
                    },
                    success: function (response) {
                        table_tarif.ajax.reload()
                    }
                });
            }
        }
    </script>
@endsection
