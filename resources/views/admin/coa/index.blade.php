@extends('layouts.admin')
@section('style')
    <style>
        table{
            position: relative;
            overflow-y: scroll;
        }
        th{
            background-color: white !important;
            position: sticky !important;
            top: 0;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA" aria-controls="offcanvasCOA">Tambah COA</button>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="height: 600px">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead style="background-color: white">
                            <tr style="background-color: white">
                                <th>#</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>View Cont</th>
                                <th>View Nopol</th>
                                <th>View No JOB</th>
                                <th>View Invoice</th>
                                <th>View No BG</th>
                                <th>View No Bupot</th>
                                <th>View Tgl Bupot</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_active')" {{ $item->is_active==1?'checked':'' }}></td>
                                    <td>{{ $item->kode }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_cont')" {{ $item->is_cont==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_nopol')" {{ $item->is_nopol==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_nojob')" {{ $item->is_nojob==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_invoice')" {{ $item->is_invoice==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_nobg')" {{ $item->is_nobg==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_nobupot')" {{ $item->is_nobupot==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }},'is_tglbupot')" {{ $item->is_tglbupot==1?'checked':'' }}></td>
                                    <td>{{ $item->keterangan }}</td>
                                </tr>
                                @if ($item->coas->count()>0)
                                    @foreach ($item->coas as $a)
                                    <tr>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_active')" {{ $a->is_active==1?'checked':'' }}></td>
                                        <td>{{ $a->kode }}</td>
                                        <td>{{ $a->nama }}</td>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_cont')" {{ $a->is_cont==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_nopol')" {{ $a->is_nopol==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_nojob')" {{ $a->is_nojob==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_invoice')" {{ $a->is_invoice==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_nobg')" {{ $a->is_nobg==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_nobupot')" {{ $a->is_nobupot==1?'checked':'' }}></td>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }},'is_tglbupot')" {{ $a->is_tglbupot==1?'checked':'' }}></td>
                                        <td>{{ $a->keterangan }}</td>
                                    </tr>
                                        @if ($a->coas->count()>0)
                                            @foreach ($a->coas as $b)
                                            <tr>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_active')" {{ $b->is_active==1?'checked':'' }}></td>
                                                <td>{{ $b->kode }}</td>
                                                <td>{{ $b->nama }}</td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_cont')" {{ $b->is_cont==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_nopol')" {{ $b->is_nopol==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_nojob')" {{ $b->is_nojob==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_invoice')" {{ $b->is_invoice==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_nobg')" {{ $b->is_nobg==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_nobupot')" {{ $b->is_nobupot==1?'checked':'' }}></td>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }},'is_tglbupot')" {{ $b->is_tglbupot==1?'checked':'' }}></td>
                                                <td>{{ $b->keterangan }}</td>
                                            </tr>
                                            @if ($b->coas->count()>0)
                                                @foreach ($b->coas as $c)
                                                    <tr>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_active')" {{ $c->is_active==1?'checked':'' }}></td>
                                                        <td>{{ $c->kode }}</td>
                                                        <td>{{ $c->nama }}</td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_cont')" {{ $c->is_cont==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_nopol')" {{ $c->is_nopol==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_nojob')" {{ $c->is_nojob==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_invoice')" {{ $c->is_invoice==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_nobg')" {{ $c->is_nobg==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_nobupot')" {{ $c->is_nobupot==1?'checked':'' }}></td>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }},'is_tglbupot')" {{ $c->is_tglbupot==1?'checked':'' }}></td>
                                                        <td>{{ $c->keterangan }}</td>
                                                    </tr>
                                                    @if ($c->coas->count()>0)
                                                        @foreach ($c->coas as $d)
                                                            <tr>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_active')" {{ $d->is_active==1?'checked':'' }}></td>
                                                                <td>{{ $d->kode }}</td>
                                                                <td>{{ $d->nama }}</td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_cont')" {{ $d->is_cont==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_nopol')" {{ $d->is_nopol==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_nojob')" {{ $d->is_nojob==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_invoice')" {{ $d->is_invoice==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_nobg')" {{ $d->is_nobg==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_nobupot')" {{ $d->is_nobupot==1?'checked':'' }}></td>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }},'is_tglbupot')" {{ $d->is_tglbupot==1?'checked':'' }}></td>
                                                                <td>{{ $d->keterangan }}</td>
                                                            </tr>
                                                            @if ($d->coas->count()>0)
                                                                @foreach ($d->coas as $e)
                                                                    <tr>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_active')" {{ $e->is_active==1?'checked':'' }}></td>
                                                                        <td>{{ $e->kode }}</td>
                                                                        <td>{{ $e->nama }}</td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_cont')" {{ $e->is_cont==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_nopol')" {{ $e->is_nopol==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_nojob')" {{ $e->is_nojob==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_invoice')" {{ $e->is_invoice==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_nobg')" {{ $e->is_nobg==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_nobupot')" {{ $e->is_nobupot==1?'checked':'' }}></td>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }},'is_tglbupot')" {{ $e->is_tglbupot==1?'checked':'' }}></td>
                                                                        <td>{{ $e->keterangan }}</td>
                                                                    </tr>
                                                                        @if ($e->coas->count()>0)
                                                                            @foreach ($e->coas as $f)
                                                                            <tr>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_active')" {{ $f->is_active==1?'checked':'' }}></td>
                                                                                <td>{{ $f->kode }}</td>
                                                                                <td>{{ $f->nama }}</td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_cont')" {{ $f->is_cont==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_nopol')" {{ $f->is_nopol==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_nojob')" {{ $f->is_nojob==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_invoice')" {{ $f->is_invoice==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_nobg')" {{ $f->is_nobg==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_nobupot')" {{ $f->is_nobupot==1?'checked':'' }}></td>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }},'is_tglbupot')" {{ $f->is_tglbupot==1?'checked':'' }}></td>
                                                                                <td>{{ $f->keterangan }}</td>
                                                                            </tr>
                                                                            @endforeach
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasCOA" aria-labelledby="offcanvasCOALabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasCOALabel">Form COA</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('coa.store') }}" method="post">
                @csrf
                @include('admin.coa.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    {{-- <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('coa.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
            { data: 'coa_id', name: 'coa_id' },
            { data: 'kode', name: 'kode' },
            { data: 'nama', name: 'nama' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script> --}}
    <script>
        function updateActive(e,id,tipe){
            let data = {update_status:1};
            if(tipe=='is_active'){
                data.is_active = 1;
            }
            if(tipe=='is_cont'){
                data.is_cont = 1;
            }
            if(tipe=='is_nopol'){
                data.is_nopol = 1;
            }
            if(tipe=='is_nojob'){
                data.is_nojob = 1;
            }
            if(tipe=='is_invoice'){
                data.is_invoice = 1;
            }
            if(tipe=='is_nobg'){
                data.is_nobg = 1;
            }
            if(tipe=='is_nobupot'){
                data.is_nobupot = 1;
            }
            if(tipe=='is_tglbupot'){
                data.is_tglbupot = 1;
            }
            $.ajax({
                type: "PUT",
                url: "{{ url('admin/coa') }}"+"/"+id,
                data: data,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    alert('Data Berhasil disimpan!');
                }
            });
        }
    </script>
@endsection
