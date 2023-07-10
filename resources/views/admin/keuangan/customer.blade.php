@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        table.dataTable tbody th, table.dataTable tbody td{
            padding: 0px 10px !important;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        <button data-bs-toggle="modal" data-bs-target="#customer-modal" class="btn btn-sm btn-success">Edit</button>
                        {{-- <form action="{{ route('customer.import') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" id="file" onchange="submit()">
                        </form> --}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="customer" style="font-size:.7rem; white-space: nowrap;">
                                <thead>
                                    <tr>
                                        {{-- <th>#</th> --}}
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>NPWP</th>
                                        <th>Nama NPWP</th>
                                        <th>Alamat NPWP</th>
                                        <th>Invoice All In</th>
                                        <th>Perlu BA Kembali</th>
                                        <th>Marketing</th>
                                        <th>CS</th>
                                        <th>PIC</th>
                                        <th>Alamat</th>
                                        <th>Kota</th>
                                        <th>Telp</th>
                                        <th>HP</th>
                                        <th>Fax</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customer-modal" tabindex="-1" aria-labelledby="customerLabel" aria-hidden="true">
        <form action="" class="modal-dialog" method="post" id="form-customer">
            <input type="hidden" name="id" id="user_id">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit <span id="nama_customer"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label for="nik">NIK</label>
                            <input type="text" name="nik" id="nik" class="form-control">
                        </div>
                        <div class="col-12 mb-2">
                            <label for="npwp">NPWP</label>
                            <input type="text" name="npwp" id="npwp" class="form-control">
                        </div>
                        <div class="col-12 mb-2">
                            <label for="nama_npwp">Nama NPWP</label>
                            <input type="text" name="nama_npwp" id="nama_npwp" class="form-control">
                        </div>
                        <div class="col-12 mb-2">
                            <label for="alamat_npwp">Alamat NPWP</label>
                            <textarea name="alamat_npwp" id="alamat_npwp" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                        <div class="col-6 mb-2">
                            <label for="ba_kembali">Perlu BA Kembali</label>
                            <select name="ba_kembali" id="ba_kembali" class="form-control">
                                <option value="1">IYA</option>
                                <option value="0">TIDAK</option>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label for="all_in">Invoice All In</label>
                            <select name="all_in" id="all_in" class="form-control">
                                <option value="1">IYA</option>
                                <option value="0">TIDAK</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-update">Simpan</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function() {
        document.oncontextmenu = new Function("return false");
        $('body').bind('cut copy paste', function(event) {
            event.preventDefault();
        });
    });
</script>
    <script>
        let tablecus = $('#customer').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            select:true,
            ajax:{
                url: '{{ route('customer.data') }}',
                method:'POST',
                data:{
                    filter:@json(request('filter'))
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                // { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'nama', name: 'customers.nama' },
                { data: 'nik', name: 'nik' },
                { data: 'npwp', name: 'npwp' },
                { data: 'nama_npwp', name: 'nama_npwp' },
                { data: 'alamat_npwp', name: 'alamat_npwp' },
                { data: 'all_in', name: 'all_in' },
                { data: 'ba_kembali', name: 'ba_kembali' },
                { data: 'marketing_id', name: 'marketing.name' },
                { data: 'cs_id', name: 'cs.name' },
                { data: 'pic', name: 'pic' },
                { data: 'alamat', name: 'alamat' },
                { data: 'kota', name: 'kota' },
                { data: 'telp', name: 'telp' },
                { data: 'hp', name: 'hp' },
                { data: 'fax', name: 'fax' },
                { data: 'email', name: 'email' },
            ],
            select:true
        });

        $('#customer tbody').on( 'click', 'tr', function () {
            var cus =  tablecus.row( this ).data();
            let all_in = cus.all_in == 'IYA' ? 1 : 0;
            let ba_kembali = cus.ba_kembali == 'IYA' ? 1 : 0;
            $('#nama_customer').html(cus.nama);
            $('#user_id').val(cus.id);
            $('#nik').val(cus.nik);
            $('#npwp').val(cus.npwp);
            $('#nama_npwp').val(cus.nama_npwp);
            $('#alamat_npwp').val(cus.alamat_npwp);
            $("#all_in").val(all_in).change();
            $("#ba_kembali").val(ba_kembali).change();
        })

        $('#btn-update').click(function (e) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.customer.update') }}",
                data: {
                    id: $('#user_id').val(),
                    nik: $('#nik').val(),
                    npwp: $('#npwp').val(),
                    nama_npwp: $('#nama_npwp').val(),
                    alamat_npwp: $('#alamat_npwp').val(),
                    all_in: $('#all_in').val(),
                    ba_kembali: $('#ba_kembali').val(),
                },
                success: function (response) {
                    alert('Data berhasil di update!');
                    tablecus.ajax.reload();
                }
            });
        });
    </script>
@endsection
