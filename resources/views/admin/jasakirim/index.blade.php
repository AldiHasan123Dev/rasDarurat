@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="row">
            @if ($role!='cs')
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
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                        {{-- <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasJasaKirim" aria-controls="offcanvasJasaKirim">Tambah JasaKirim</button> --}}
                        <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Filter</button>
                    <b id="nominal-selected">Rp. 0</b>
                    <form action="{{ route('jasakirim.sync.data') }}" method="post">
                        @csrf
                        <div class="d-flex gap-1">
                            <label for="selectAll">
                                <input type="checkbox" name="selectAll" id="selectAll" class="selectAll">
                                Select All Data
                            </label>
                            <button class="btn btn-sm btn-primary" onclick="merge()" type="button">Merge</button>
                            <button class="btn btn-sm btn-info" type="button" id="unmerge">Unmerge</button>
                            <button class="btn btn-sm btn-success" type="submit">Sinkronisasi Data</button>

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
            $('#nominal-selected').html('Rp. '+nominal.toLocaleString('id-ID'));
        });

        document.querySelector('#unmerge').addEventListener('click', function () {
            // alert(table1.rows('.selected').data().id + ' row(s) selected');
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
            $('#nominal-selected').html('Rp. '+nominal.toLocaleString('id-ID'));
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
    </script>
@endsection
