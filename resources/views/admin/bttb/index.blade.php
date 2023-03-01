@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-1">
                <a href="{{ route('order.index') }}" class="btn btn-primary btn-sm">Kembali</a>
            </div>
            <div class="col-4">
                <div class="card p-3 shadow">
                    <span>Cutomer Info</span>
                    <hr>
                    <div class="item d-flex justify-content-between">Pengirim: <span>{{ $order->pengirim->nama }}</span></div>
                    <div class="item d-flex justify-content-between">Penerima: <span>{{ $order->penerima->nama }}</span></div>
                    <div class="item d-flex justify-content-between">Alamat Penerima: <span>{{ $order->penerima->alamat }}</span></div>
                    <div class="item d-flex justify-content-between">HP Penerima: <span>{{ $order->penerima->hp }}</span></div>
                </div>
            </div>
            <div class="col-4">
                <div class="card p-3 shadow">
                    <span>Order Info</span>
                    <hr>
                    <div class="item d-flex justify-content-between">No. BTTB: <span>{{ $order->job }}-{{ $order->no_job }}</span></div>
                    <div class="item d-flex justify-content-between">Nama Kapal: <span>{{ $order->tarif->jadwal_kapal->kapal->nama }}</span></div>
                    <div class="item d-flex justify-content-between">No. Container: <span>{{ $order->container }}</span></div>
                    <div class="item d-flex justify-content-between">No. Seal: <span>{{ $order->seal }}</span></div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header p-2 d-flex" style="gap:10px">
                        <button class="py-2 px-3 btn btn-sm btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBTTB" aria-controls="offcanvasBTTB"><i class="fas fa-plus"></i> Tambah BTTB</button>
                        <a class="py-2 px-3 btn btn-sm btn-secondary" style="font-size: .7rem" href="{{ route('cetak.bttb',['order_id'=>$order->id]) }}"><i class="fas fa-print"></i> Print BTTB</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" style="font-size:.7rem">
                                <thead>
                                    <tr>
                                        <th>ID.</th>
                                        <th>No. Gudang</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>P</th>
                                        <th>L</th>
                                        <th>T</th>
                                        <th>Vol</th>
                                        <th>Berat</th>
                                        <th>Tgl Masuk</th>
                                        <th>Pengirim</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>Vol
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasBTTB" aria-labelledby="offcanvasBTTBLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasBTTBLabel">Form BTTB</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('bttb.store') }}" method="post">
                @csrf
                @include('admin.bttb.form', ['bttb'=>[]])
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $("select[name=pengirim_id]").select2({
            dropdownParent: $('#offcanvasBTTB')
        });
        $("select[name=satuan_id]").select2({
            dropdownParent: $('#offcanvasBTTB'),
            tags:true
        });
        $("select[name=barang_id]").select2({
            dropdownParent: $('#offcanvasBTTB'),
            tags:true
        });
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('bttb.data') }}',
                method:'POST',
                data:{order_id:@json($order->id)},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'no_gudang', name: 'no_gudang' },
                { data: 'barang_id', name: 'barang_id' },
                { data: 'qty', name: 'qty' },
                { data: 'satuan_id', name: 'satuan_id' },
                { data: 'p', name: 'p' },
                { data: 'l', name: 'l' },
                { data: 't', name: 't' },
                { data: 'vol', name: 'vol' },
                { data: 'berat', name: 'berat' },
                { data: 'tgl_masuk', name: 'tgl_masuk' },
                { data: 'pengirim_id', name: 'pengirim_id' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection
