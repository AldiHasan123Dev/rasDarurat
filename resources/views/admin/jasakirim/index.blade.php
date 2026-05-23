@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="row">
            @if ($role!='cs' && $role!='jurnal')
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJasaKirim" aria-controls="offcanvasJasaKirim">Tambah JasaKirim</button> --}}
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <b>KOTA AGEN BELUM DI ISI TARIF</b>
                            <form action="{{ route('jasakirim.sync') }}" method="post">
                                @csrf
                                <button class="btn btn-sm btn-success" type="submit">Sinkronisasi Harga</button>
                            </form>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-sm" id="table-1" style="font-size:.7rem">
                                <thead>
                                    <tr> 
                                        <th>ID.</th>
                                        <th>Tujuan</th>
                                        <th>Kota</th>
                                        <th>JOB/ITEM</th>
                                        <th>Barcode</th>
                                        <th>Tgl Kirim</th>
                                        <th>Tgl Terima Kurir</th>
                                        <th>Nominal</th>
                                        <th>Ekspedisi</th>
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
            @endif
            @if ($role!='jurnal')
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJasaKirim" aria-controls="offcanvasJasaKirim">Tambah JasaKirim</button> --}}
                        <!-- Button trigger modal -->
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Filter</button>
                            @if ($role!='cs')
                            <button type="button" class="btn btn-success" onclick="addDraf()">Buat Draf Jurnal</button>
                            @endif
                        </div>
                        <b id="nominal-selected">Rp. 0</b>
                        <form action="{{ route('jasakirim.sync.data') }}" method="post">
                            @csrf
                            <div class="d-flex gap-1">
                                @if ($role!='cs')
                                    <label for="selectAll">
                                        <input type="checkbox" name="selectAll" id="selectAll" class="selectAll">
                                        Select All Data
                                    </label>
                                    <button class="btn btn-sm btn-primary" onclick="merge()" type="button">Merge</button>
                                    @endif
                                    @if ($role=='cs')
                                    <button class="btn btn-sm btn-info" type="button" id="unmerge">Unmerge</button>
                                    @endif
                                    <button class="btn btn-sm btn-success" onclick="loadListKirimDokumen()" type="button">Tambah Item Resi</button>
                                {{-- <button class="btn btn-sm btn-warning" type="submit">Sinkronisasi Data</button> --}}
                            </div>
                        </form>

                    <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                           <form id="filterForm" class="modal-dialog">
                                <input type="hidden" name="role" value="{{ $role }}">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Filter Pencarian Data</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <label for="start_date">Tanggal Kirim Dari</label>
                                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $start_date }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="end_date">Tanggal Kirim Ke</label>
                                                       <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $end_date }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <label for="tujuan">Tujuan</label>
                                                       <select name="tujuan" id="tujuan" class="form-select">
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="search">ID JOB</label>
                                                        <input type="text" class="form-control" id="searching" name="searching" value="{{ $search }}">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="mb-2">
                                                        <label for="barcode">Barcode</label>
                                                        <input type="text" class="form-control" id="barcode" name="barcode" value="{{ $barcode }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="btnFilter" class="btn btn-primary">
                                            Cari
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="table-2" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>ID.</th>
                                        <th>Tujuan</th>
                                        <th>Kota</th>
                                        <th>JOB/ITEM</th>
                                        <th>Barcode</th>
                                        <th>Tgl Kirim</th>
                                        <th>Tgl Terima Kurir</th>
                                        <th>Nominal</th>
                                        <th>Ekspedisi</th>
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
            @endif
            @if ($role!='cs')
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        <b>List Draf Jurnal</b>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-li11795st" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        @if ($role=='jurnal')
                                            <th>Nomor Jurnal</th>
                                        @endif
                                        <th>Tanggal Draf</th>
                                        <th>Kode Draf</th>
                                        <th>Ekspedisi</th>
                                        <th>Jumlah Resi</th>
                                        <th>Total Biaya</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            @if ($role=='jurnal')
                                                <th>{{ $item->first()->jurnal ?? '-' }}</th>
                                            @endif
                                            <td>{{ date('d/m/y',strtotime($item->first()->tgl_invoice)) }}</td>
                                            <td>{{ $item->first()->invoice }}</td>
                                            <td>{{ $item->first()->ekspedisi }}</td>
                                            <td>{{ $item->count() }}</td>
                                            <td><b>{{ number_format($item->sum('nominal')) }}</b></td>
                                            <td>
                                                <div class="d-flex gap-3">
                                                    @if ($role=='jurnal')
                                                        @if (!$item->first()->jurnal)
                                                            <a href="{{ route('jasakirim.draf.jurnal',['invoice'=>$item->first()->invoice]) }}" class="btn btn-success py-0 px-5" style="height: 20px; font-size:.7rem">Buat Jurnal</a>
                                                        @else
                                                            <a href="{{ route('jurnal.edit',['jurnal'=>$item->first()->jurnal]) }}" class="btn btn-warning py-0 px-5" style="height: 20px; font-size:.7rem">Lihat Jurnal</a>
                                                            @if (Auth::user()->role_id==1)
                                                                <form action="{{ route('jasakirim.sync.jurnal') }}" method="post">
                                                                    @csrf
                                                                    <input type="hidden" name="invoice" value="{{ $item->first()->invoice }}">
                                                                    <button class="btn btn-success py-0 px-5" style="height: 20px; font-size:.7rem" type="submit">Sinkronisasi</button>
                                                                </form>
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <button class="btn btn-info py-0 px-5" style="height: 20px; font-size:.7rem" type="button" data-bs-toggle="modal" data-bs-target="#listresi-{{ $loop->iteration }}">Detail</button>
                                                </div>
                                                <div class="modal fade" id="listresi-{{ $loop->iteration }}" tabindex="-1" aria-labelledby="listresi-{{ $loop->iteration }}Label" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="listresi-{{ $loop->iteration }}Label">List Draf {{  $item->first()->invoice }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table table-list" style="font-size:.7rem">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>ID.</th>
                                                                                <th>Tujuan</th>
                                                                                <th>Kota</th>
                                                                                <th>JOB</th>
                                                                                <th>Barcode</th>
                                                                                <th>Tgl Kirim</th>
                                                                                <th>Tgl Terima Kurir</th>
                                                                                <th>Nominal</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($item as $rs)
                                                                                <tr>
                                                                                    <td>{{ $loop->iteration }}</td>
                                                                                    <td>{{ $rs->lokasi->nama ?? '-' }}</td>
                                                                                    <td>{{ $rs->agen->lokasi->nama ?? '-' }}</td>
                                                                                    <td>{{ $rs->order_name() }}</td>
                                                                                    <td>{{ $rs->barcode }}</td>
                                                                                    <td>{{ date('d/m/y', strtotime($rs->tgl_kirim)) }}</td>
                                                                                    <td>{{ date('d/m/y', strtotime($rs->tgl_terima)) }}</td>
                                                                                    <td>{{ number_format($rs->nominal) }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                {{-- <button type="button" id="btn-add-job" class="btn btn-primary">Tambahkan</button> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasJasaKirim" aria-labelledby="offcanvasJasaKirimLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasJasaKirimLabel">Form JasaKirim</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('jasakirim.store') }}" method="post">
                @csrf
                @include('admin.jasakirim.form')
            </form>
        </div>
    </div>

    <div class="modal fade" id="addJob" tabindex="-1" aria-labelledby="addJobLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJobLabel">Item tambahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="kirim-dokumen-list"></tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="d-flex gap-2">
                        <input type="text" name="nama" id="nama" class="form-control" placeholder="Tulis keterangan" style="width: 80%">
                        <button class="py-1 px-3 bg-success text-white" type="button" style="width: 20%" onclick="addKirimDokumen()">Simpan</button>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="btn-add-job" class="btn btn-primary">Tambahkan</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @if ($role!='cs')
        <script> 
            let table = $('#table-1').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            ajax:{
                url: '{{ route('jasakirim.data') }}',
                method:'POST',
                data:{
                    nominal:0,
                    role:@json($role),
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'lokasi_id', name: 'lokasi_id' },
                { data: 'kota', name: 'kota' },
                { data: 'orders', name: 'orders' },
                { data: 'barcode', name: 'barcode' },
                { data: 'tgl_kirim', name: 'tgl_kirim' },
                { data: 'tgl_terima', name: 'tgl_terima' },
                { data: 'nominal', name: 'nominal' },
                { data: 'ekspedisi', name: 'ekspedisi' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
        </script>
    @endif
    <script>
        let table1 = $('#table-2').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searchDelay: 500,
            deferRender:true,
            scrollCollapse: true,
            scrollY: '50vh',
            searching:false,
            ajax:{
                url: '{{ route('jasakirim.data') }}',
                method:'POST',
               data: function(d){

                d.nominal = 1;

                d.role = @json($role);

                d.start_date = $('#start_date').val();

                d.end_date = $('#end_date').val();

                d.tujuan = $('#tujuan').val();

                d.searching = $('#searching').val();

                d.barcode = $('#barcode').val();
            },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'lokasi_id', name: 'lokasi_id' },
                { data: 'kota', name: 'kota' },
                { data: 'orders', name: 'orders' },
                { data: 'barcode', name: 'barcode' },
                { data: 'tgl_kirim', name: 'tgl_kirim' },
                { data: 'tgl_terima', name: 'tgl_terima' },
                { data: 'nominal', name: 'nominal' },
                { data: 'ekspedisi', name: 'ekspedisi' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
        table1.on('click', 'tbody tr', function (e) {
            e.currentTarget.classList.toggle('selected');
            let nominal = 0;
            for (let i = 0; i < table1.rows('.selected').data().length; i++) {
                var num = table1.rows('.selected').data()[i].nominal
                nominal += parseInt(num.replace(',','').replace('.',''))
            }
            $('#nominal-selected').html('Rp. '+nominal.toLocaleString('id-ID')+'('+table1.rows('.selected').data().length+')');
        });

        $('#btnFilter').click(function(){

    table1.ajax.reload();

    $('#exampleModal').modal('hide');
});

        $('#unmerge').click(function (e) {
            let arr = []
            for (let i = 0; i < table1.rows('.selected').data().length; i++) {
                arr[i] = table1.rows('.selected').data()[i].id
            }

            $.ajax({
                type: "POST",
                url: "{{ route('jasakirim.unmerge') }}",
                data: {id:arr},
                success: function (response) {
                    table1.ajax.reload();
                    alert('unmerge berhasil!');
                }
            });
        });

        $('#tujuan').select2({

    dropdownParent: $('#exampleModal'),

    placeholder: 'Pilih Tujuan',

    allowClear: true,

    width: '100%',

    ajax: {

        url: "{{ url('api/lokasi/select2') }}",

        dataType: 'json',

        delay: 250,

        data: function(params){
            return {
                search: params.term
            };
        },

        processResults: function(data){
            return {
                results: data
            };
        },

        cache: true
    }
});

        $(".selectAll").on( "click", function(e) {
            if ($(this).is( ":checked" )) {
                $('#table-2 tbody tr').addClass('selected');
            } else {
                $('#table-2 tbody tr').removeClass('selected');
            }
            let nominal = 0;
            for (let i = 0; i < table1.rows('.selected').data().length; i++) {
                var num = table1.rows('.selected').data()[i].nominal
                nominal += parseInt(num.replace(',','').replace('.',''))
            }
            $('#nominal-selected').html('Rp. '+nominal.toLocaleString('id-ID')+'('+table1.rows('.selected').data().length+')');
        });


        function merge(){
            if(confirm('are you sure?')){
                $.ajax({
                    type: "POST",
                    url: "{{ route('jasakirim.merge') }}",
                    success: function (response) {
                        table1.ajax.reload();
                        alert('Merge data berhasil');
                    }
                });
            }
        }

        $('#btn-add-job').click(function (e) {
            if(table1.rows('.selected').data().length == 1){
                var val = $('#order_id').val();
                $.ajax({
                    type: "POST",
                    url: "{{ url('api/update-order-request') }}",
                    data: {
                        id:val,
                        jasa_kirim_id:table1.rows('.selected').data()[0].id
                    },
                    success: function (response) {
                        table1.ajax.reload();
                        alert('ID JOB berhasil ditambahkan!');
                    }
                });
            }else{
                alert('Harap pilih satu data saja!');
            }
        });

        function addDraf(){
            if (confirm('Are you sure?')) {
                let arr = []
                for (let i = 0; i < table1.rows('.selected').data().length; i++) {
                    arr[i] = table1.rows('.selected').data()[i].id
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('jasakirim.add.draf') }}",
                    data: {
                        id:arr
                    },
                    success: function (response) {
                        alert('Buat Draf Berhasil');
                        location.reload()
                    }
                });
            }
        }

        var addItem = new bootstrap.Modal(document.getElementById('addJob'), {
            keyboard: false
        })
        var myModalEl = document.getElementById('addJob')
        myModalEl.addEventListener('hidden.bs.modal', function (event) {
            table1.ajax.reload();
        })
        function loadListKirimDokumen() {
            if(table1.rows('.selected').data().length == 1){
                addItem.show();
                $.ajax({
                    type: "GET",
                    url: "{{ url('api/kirim-dokumen') }}"+'?jasa_kirim_id='+table1.rows('.selected').data()[0].id,
                    success: function (response) {
                        let html = '';
                        $.each(response, function (idx, item) {
                            html += `<tr>
                                        <td>${idx+1}</td>
                                        <td>${item.nama}</td>
                                        <td>
                                            <button class="py-1 px-3 bg-danger text-white" type="button" onclick="deleteKirimDokumen(${item.id})">Hapus</button>
                                        </td>
                                    </tr>`;
                        });
                        $('#kirim-dokumen-list').html(html);
                    }
                });
            }else{
                alert('Harap pilih satu data saja!');
            }
        }

        function addKirimDokumen(){
            var val = $('#nama').val();
            if (val) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('kirim_dokumen.store') }}",
                    data: {
                        nama:val,
                        jasa_kirim_id:table1.rows('.selected').data()[0].id
                    },
                    success: function (response) {
                        $('#nama').val('');
                        loadListKirimDokumen();
                        alert('Data berhasil ditambahkan!');
                    }
                });
            }
        }

        function deleteKirimDokumen(id){
            $.ajax({
                type: "DELETE",
                url: "{{ url('api/kirim-dokumen') }}"+"/"+id,
                success: function (response) {
                    loadListKirimDokumen();
                    alert('Data berhasil dihapus!');
                }
            });
        }

        $('.table-list').DataTable();
    </script>
@endsection
