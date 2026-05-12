@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<style>
    td:hover {
        cursor: pointer;
    }
    table.dataTable tbody th, table.dataTable tbody td{
        padding: 0px 10px !important;
    }
</style>
@endsection
@section('content')

    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex" style="gap:10px">
                {{-- <button data-bs-toggle="modal" data-bs-target="#modal-edit" class="py-2 px-3 btn btn-sm btn-primary">Edit Data</button>
                <form action="" id="delete" method="post">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                </form>
                <a style="font-size: .7rem" class="btn-sm btn btn-success" href="{{ route('customer.create') }}">Tambah Customer <i class="fas fa-plus"></i></a> --}}
            </div>
            <div class="card-body">
                <div class="table-responsives">
                    <table class="table table-sm" id="customer" style="font-size:.7rem; white-space: nowrap;">
                        <thead>
                            <tr>
                                <th class="text-center">ID.</th>
                                <th>Nama</th>
                                <th>Marketing</th>
                                <th>CS</th>
                                <th>PIC</th>
                                <th>Alamat</th>
                                <th>Kota</th>
                                <th>Telp</th>
                                <th>HP</th>
                                <th>Fax</th>
                                <th>Email</th>
                                <th>NIK</th>
                                <th>NPWP</th>
                                <th>Nama NPWP</th>
                                <th>Alamat NPWP</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($customers as $cus)
                                <tr>
                                    <td>{{ $cus->id }}</td>
                                    <td>{{ $cus->nama }}</td>
                                    <td>{{ $cus->marketing->name ?? '-' }}</td>
                                    <td>{{ $cus->cs->name ?? '-' }}</td>
                                    <td>{{ $cus->pic ?? '-' }}</td>
                                    <td>{{ $cus->alamat ?? '-' }}</td>
                                    <td>{{ $cus->kota ?? '-' }}</td>
                                    <td>{{ $cus->telp ?? '-' }}</td>
                                    <td>{{ $cus->hp ?? '-' }}</td>
                                    <td>{{ $cus->fax ?? '-' }}</td>
                                    <td>{{ $cus->email ?? '-' }}</td>
                                    <td>{{ $cus->nik ?? '-' }}</td>
                                    <td>{{ $cus->npwp ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('customer.destroy',$cus) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure?')" class="no-attr text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                                            </form>
                                            <button class="no-attr text-primary" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCustomerUpdate{{ $cus->id }}" aria-controls="offcanvasCustomerUpdate{{ $cus->id }}"><i class="fas fa-pencil"></i></button>
                                        </div>

                                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCustomerUpdate{{ $cus->id }}" aria-labelledby="offcanvasCustomerUpdate{{ $cus->id }}Label">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="offcanvasCustomerUpdate{{ $cus->id }}Label">Form Customer</h5>
                                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                            </div>
                                            <div class="offcanvas-body">
                                                <form action="{{ route('customer.update',$cus) }}" method="post">
                                                    @csrf
                                                    @method('PUT')
                                                    @include('admin.customer.form', ['cus'=>$cus])
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header p-2 d-flex" style="gap:10px">
                <button class="btn-sm btn border-bottom border-dark" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTarif" aria-controls="offcanvasTarif" id="add-tarif">Tambah Tarif <i class="fas fa-plus"></i></button>
                <b class="mt-2" style="font-size: .7rem">Atas Nama: <span class="nama-cus"></span></b>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" id="tarif" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Tanggal</th>
                                <th>Pelayaran</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Shipment</th>
                                <th>Kondisi</th>
                                <th>Satuan</th>
                                <th>Tarif</th>
                                <th>Stuffing</th>
                                <th>Keterangan</th>
                                <th>Unit</th>
                                <th>Min qty</th>
                                <th>Customer</th>
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
    </div>

<div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTarif" aria-labelledby="offcanvasTarifLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasTarifLabel">Form Tarif</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('tarif.store') }}" method="post">
            <div id="message" class="my-3 text-center text-white alert alert-success py-2 px-5"></div>
            <div id="message-error" class="my-3 text-center text-white alert alert-danger py-2 px-5">Harap Lengkapi Form</div>
            @csrf
            @include('admin.tarif.form')
            <div class="mt-2">
                <button type="button" id="add-tarif-form"  class="btn btn-success btn-sm">Tambah Data</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal-edit" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="iframe-edit" style="width: 100%; height:100vh"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script>
    $(document).ready(function() {
        document.oncontextmenu = new Function("return false");
        $('body').bind('cut copy paste', function(event) {
            event.preventDefault();
        });
    });
</script>
    <script>
        // $('#add-tarif').hide();
        $('#message-error').hide();
        let id = null;
        let tablecus = $('#customer').DataTable({
            processing: true,
            serverSide: true,
            select:true,
            scrollY: '50vh',
            scrollX: true,
            scrollCollapse: true,
            ajax:{
                url: '{{ route('customer.data') }}',
                method:'POST',
                data:{
                    type:'tarif_marketing'
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'customers.id' },
                { data: 'nama', name: 'customers.nama' },
                { data: 'marketing_id', name: 'marketing.name' },
                { data: 'cs_id', name: 'cs.name' },
                { data: 'pic', name: 'pic' },
                { data: 'alamat', name: 'alamat' },
                { data: 'kota', name: 'kota' },
                { data: 'telp', name: 'telp' },
                { data: 'hp', name: 'hp' },
                { data: 'fax', name: 'fax' },
                { data: 'email', name: 'email' },
                { data: 'nik', name: 'nik' },
                { data: 'npwp', name: 'npwp' },
                { data: 'nama_npwp', name: 'nama_npwp' },
                { data: 'alamat_npwp', name: 'alamat_npwp' },
            ],"columnDefs": [
                { className: "text-center", "targets": [0] }
            ]
        });
        let tabletar = $('#tarif').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('tarif.datatable1') }}',
                method:'POST',
                data:function( d) {
                    d.customer_id = id;
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'tarif.id' },
                { data: 'created_at', name: 'created_at' },
                { data: 'pelayaran_id', name: 'pelayaran_id' },
                { data: 'dari', name: 'dari' },
                { data: 'tujuan', name: 'lokasi.nama' },
                { data: 'shipment', name: 'shipment' },
                { data: 'kondisi', name: 'kondisi' },
                { data: 'satuan', name: 'satuan' },
                { data: 'tarif', name: 'tarif' },
                { data: 'stuffing', name: 'stuffing' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'unit', name: 'unit' },
                { data: 'min_qty', name: 'min_qty' },
                { data: 'customer_id', name: 'customer_id' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

       $(document).ready(function() {
    $("select[name=customer_id]").select2({
        dropdownParent: $('#offcanvasTarif'),
        minimumInputLength: 2,  // letakkan di sini
        delay: 400,              // dan di sini
        ajax: {
            url: '/api/get-pengirim',
            data: function (params) {
                return {
                    cari: params.term,        // teks pencarian
                    marketing: @json($idMarketing), // kirim ID marketing dari PHP
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 20) < data.counts
                    }
                };
            }
        }
    });
});

        // $("select[name=customer_id]").select2({
        //     dropdownParent: $('#offcanvasTarif')
        // });
        $("select[name=jadwal_kapal_id]").select2({
            dropdownParent: $('#offcanvasTarif')
        });
        $("select[name=dari]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=tujuan]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=shipment]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        $("select[name=kondisi]").select2({
            dropdownParent: $('#offcanvasTarif')
        });
        $("select[name=satuan_inv]").select2({
            dropdownParent: $('#offcanvasTarif'),
            tags:true
        });
        // $("select[name=satuan]").select2({
        //     dropdownParent: $('#offcanvasTarif'),
        //     tags:true
        // });
        $('#customer tbody').on( 'click', 'tr', function () {
            id =  tablecus.row( this ).data().id;
            $('.nama-cus').html(tablecus.row(this).data().nama);
            $('#iframe-edit').attr('src','{{ url('admin/customer') }}/'+id+'/edit');
            $('#delete').attr('action','{{ url('admin/customer') }}/'+id);
            // $('#add-tarif').show();
            tabletar.ajax.reload()
        });
        $('#shipment').change(function (e) {
            var text = $(this).find(":selected").text();
            var val = text.substr(0,3);
            if (val=='FCL'||val=='fcl') {
                $('#satuan').val(1);
            } else {
                $('#satuan').val(2);
            }
        });

        $('#add-tarif-form').click(function (e) {
            var data = {
                pelayaran_id : $('#pelayaran_id').val(),
                customer_id : $('#customer_id').val(),
                shipment : $('#shipment').val(),
                dari : $('#dari').val(),
                tujuan : $('#tujuan').val(),
                kondisi : $('#kondisi').val(),
                satuan : $('#satuan').val(),
                tarif : $('#tarif-price').val(),
                stuffing : $('#stuffing').val(),
                keterangan : $('#keterangan').val(),
            }
              $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
            $.ajax({
                type: "POST",
                url: "{{ route('api-tarif.store') }}",
                data: data,
                success: function (response) {
                    if (response.status=='success') {
                        $('#pelayaran_id').val('');
                        $('#tarif-price').val('');
                        $('#stuffing').val('');
                        $('#keterangan').val('');
                        $('#message').show();
                        $('#message-error').hide();
                        $('#message').html(response.message);
                        tabletar.ajax.reload();
                        setTimeout(() => {
                            $('#message').hide();
                        }, 3000);
                    }
                },
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if(jqXHR.status==422){
                    $('#message-error').show();
                    setTimeout(() => {
                        $('#message-error').hide();
                    }, 5000);
                };
                // Request failed. Show error message to user.
                // errorThrown has error message.
            });
        });
        $('#message').hide();
        var myModalEl = document.getElementById('modal-edit')
        myModalEl.addEventListener('hidden.bs.modal', function (event) {
            tablecus.ajax.reload();
        })

        function changeActive(id,is_active){
            $.ajax({
                type: "PUT",
                url: "{{ route('api.tarif.update') }}",
                data: {
                    id:id,
                    is_active:is_active,
                },
                success: function (response) {
                    alert('Data berhasil disimpana!');
                }
            });
        }
</script>
@endsection
