@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCOA" aria-controls="offcanvasCOA">Tambah COA</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $item->id }})" {{ $item->is_active==1?'checked':'' }}></td>
                                    <td>{{ $item->kode }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                </tr>
                                @if ($item->coas->count()>0)
                                    @foreach ($item->coas as $a)
                                    <tr>
                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $a->id }})" {{ $a->is_active==1?'checked':'' }}></td>
                                        <td>{{ $a->kode }}</td>
                                        <td>{{ $a->nama }}</td>
                                        <td>{{ $a->keterangan }}</td>
                                    </tr>
                                        @if ($a->coas->count()>0)
                                            @foreach ($a->coas as $b)
                                            <tr>
                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $b->id }})" {{ $b->is_active==1?'checked':'' }}></td>
                                                <td>{{ $b->kode }}</td>
                                                <td>{{ $b->nama }}</td>
                                                <td>{{ $b->keterangan }}</td>
                                            </tr>
                                            @if ($b->coas->count()>0)
                                                @foreach ($b->coas as $c)
                                                    <tr>
                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $c->id }})" {{ $c->is_active==1?'checked':'' }}></td>
                                                        <td>{{ $c->kode }}</td>
                                                        <td>{{ $c->nama }}</td>
                                                        <td>{{ $c->keterangan }}</td>
                                                    </tr>
                                                    @if ($c->coas->count()>0)
                                                        @foreach ($c->coas as $d)
                                                            <tr>
                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $d->id }})" {{ $d->is_active==1?'checked':'' }}></td>
                                                                <td>{{ $d->kode }}</td>
                                                                <td>{{ $d->nama }}</td>
                                                                <td>{{ $d->keterangan }}</td>
                                                            </tr>
                                                            @if ($d->coas->count()>0)
                                                                @foreach ($d->coas as $e)
                                                                    <tr>
                                                                        <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $e->id }})" {{ $e->is_active==1?'checked':'' }}></td>
                                                                        <td>{{ $e->kode }}</td>
                                                                        <td>{{ $e->nama }}</td>
                                                                        <td>{{ $e->keterangan }}</td>
                                                                    </tr>
                                                                        @if ($e->coas->count()>0)
                                                                            @foreach ($e->coas as $f)
                                                                            <tr>
                                                                                <td><input type="checkbox" value="1" onchange="updateActive(this,{{ $f->id }})" {{ $f->is_active==1?'checked':'' }}></td>
                                                                                <td>{{ $f->kode }}</td>
                                                                                <td>{{ $f->nama }}</td>
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
        function updateActive(e,id){
            let is_active = 0;
            if ($(e).is(":checked")) {
                is_active = 1;
            }
            $.ajax({
                type: "PUT",
                url: "{{ url('admin/coa') }}"+"/"+id,
                data: {update_status:1},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    alert('Data Berhasil disimpan!');
                }
            });
        }
    </script>
@endsection
