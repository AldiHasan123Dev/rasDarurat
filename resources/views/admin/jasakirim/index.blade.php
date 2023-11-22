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
                            <b>List Nominal Belum Keluar</b>
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
                                        <th>JOB</th>
                                        <th>Barcode</th>
                                        <th>Tgl Kirim</th>
                                        <th>Tgl Terima</th>
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
                            <button type="button" class="btn btn-success" onclick="addDraf()">Buat Draf Jurnal</button>
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
                                @endif
                                @if ($role=='cs')
                                    <button class="btn btn-sm btn-success" type="button" data-bs-toggle="modal" data-bs-target="#addJob">Tambah Job</button>
                                    <button class="btn btn-sm btn-primary" onclick="merge()" type="button">Merge</button>
                                    <button class="btn btn-sm btn-info" type="button" id="unmerge">Unmerge</button>
                                @endif
                                <button class="btn btn-sm btn-warning" type="submit">Sinkronisasi Data</button>
                            </div>
                        </form>

                    <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <form action="{{ route('jasakirim.index') }}" method="get" class="modal-dialog">
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
                                                        <input type="date" class="form-control" name="start_date" value="{{ $start_date }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="end_date">Tanggal Kirim Ke</label>
                                                        <input type="date" class="form-control" name="end_date" value="{{ $end_date }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <label for="tujuan">Tujuan</label>
                                                    <select name="tujuan" id="tujuan" class="form-select">
                                                        <option value=""></option>
                                                        @foreach ($lokasi as $loc)
                                                        <option value="{{ $loc->id }}" {{ $tujuan==$loc->id?'selected':'' }}>{{ $loc->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Cari</button>
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
                                        <th>JOB</th>
                                        <th>Barcode</th>
                                        <th>Tgl Kirim</th>
                                        <th>Tgl Terima</th>
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
                            <table class="table table-sm" id="table-2" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tanggal</th>
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
                                            <td>{{ date('d/m/y',strtotime($item->first()->tgl_invoice)) }}</td>
                                            <td>{{ $item->first()->invoice }}</td>
                                            <td>{{ $item->first()->ekspedisi }}</td>
                                            <td>{{ $item->count() }}</td>
                                            <td><b>{{ number_format($item->sum('nominal')) }}</b></td>
                                            <td>
                                                <div class="d-flex gap-3">
                                                    @if ($role=='jurnal')
                                                    <a href="{{ route('jasakirim.draf.jurnal',['invoice'=>$item->first()->invoice]) }}" class="btn btn-success py-0 px-5" style="height: 20px; font-size:.7rem">Buat Draf</a>
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
                                                                    <table class="table table-sm" id="table-2" style="font-size:.7rem">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>ID.</th>
                                                                                <th>Tujuan</th>
                                                                                <th>Kota</th>
                                                                                <th>JOB</th>
                                                                                <th>Barcode</th>
                                                                                <th>Tgl Kirim</th>
                                                                                <th>Tgl Terima</th>
                                                                                <th>Nominal</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($item as $rs)
                                                                                <tr>
                                                                                    <td>{{ $loop->iteration }}</td>
                                                                                    <td>{{ $rs->lokasi->nama }}</td>
                                                                                    <td>{{ $rs->agen->lokasi->nama }}</td>
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
                    <h5 class="modal-title" id="addJobLabel">Input ID JOB</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <select name="order_id" id="order_id" class="form-select">
                            <option value=""></option>
                            @foreach ($orders as $item)
                            <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="btn-add-job" class="btn btn-primary">Tambahkan</button>
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
            scrollCollapse: true,
            scrollY: '50vh',
            ajax:{
                url: '{{ route('jasakirim.data') }}',
                method:'POST',
                data:{
                    nominal:1,
                    role:@json($role),
                    start_date:@json($start_date),
                    end_date:@json($end_date),
                    tujuan:@json($tujuan),
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
    </script>
@endsection
