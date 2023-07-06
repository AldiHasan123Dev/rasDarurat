@extends('layouts.iframe')
@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/selectize.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/selectize.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/awesomplete.css') }}">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form method="POST" action="{{ route('bttb.store') }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ request('order_id') }}">
                    <div class="table-responsives">
                        <table class="w-100 table-bordered" style="font-size: .7rem; table-layout:auto">
                            <thead>
                                <tr class="text-center">
                                    <td>No.Gudang</td>
                                    <td style="width: 200px">Barang</td>
                                    <td>Qty</td>
                                    <td>Satuan</td>
                                    <td>P</td>
                                    <td>L</td>
                                    <td>T</td>
                                    <td>Vol Manual</td>
                                    <td>Berat</td>
                                    <td>Tgl Masuk</td>
                                    <td>Pengirim</td>
                                    <td>Keterangan</td>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 5; $i++)
                                    <tr>
                                        <td><input type="text" style="width: 100px" name="bttb[{{ $i }}][no_gudang]" id="no_gudang-{{ $i }}"></td>
                                        <td><input name="bttb[{{ $i }}][barang_id]" id="barang_id-{{ $i }}" class="barang" style="width: 200px"/></td>
                                        <td><input type="number" style="width: 70px" name="bttb[{{ $i }}][qty]" id="qty-{{ $i }}"></td>
                                        <td>
                                            <select name="bttb[{{ $i }}][satuan_id]" id="satuan_id-{{ $i }}" class="selecttizecreate" style="width: 100px">
                                                <option value=""></option>
                                                @foreach ($satuan as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" step="any" onkeyup="hitungVolCreate({{ $i }})" style="width: 70px" name="bttb[{{ $i }}][p]" id="p-{{ $i }}"></td>
                                        <td><input type="number" step="any" onkeyup="hitungVolCreate({{ $i }})" style="width: 70px" name="bttb[{{ $i }}][l]" id="l-{{ $i }}"></td>
                                        <td><input type="number" step="any" onkeyup="hitungVolCreate({{ $i }})" style="width: 70px" name="bttb[{{ $i }}][t]" id="t-{{ $i }}"></td>
                                        <td><input type="number" style="width: 70px" name="bttb[{{ $i }}][vol]" id="vol-{{ $i }}"></td>
                                        <td><input type="number" style="width: 70px" name="bttb[{{ $i }}][berat]" id="berat-{{ $i }}"></td>
                                        <td><input type="date" style="width: 100px" name="bttb[{{ $i }}][tgl_masuk]" id="tgl_masuk-{{ $i }}"></td>
                                        <td>
                                            <select name="bttb[{{ $i }}][pengirim_id]" id="pengirim_id-{{ $i }}" class="selecttize" style="width: 100px">
                                                <option value=""></option>
                                                @foreach ($pengirim as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="bttb[{{ $i }}][keterangan]" id="keterangan-{{ $i }}"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 mb-2 px-1 mt-3">
                        <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('are you sure?')">Tambah BTTB</button>
                    </div>
                </form>
            </div>
            <div class="col-12 mt-3">
                <div class="card p-3 shadow">
                    <span>LIST BTTB</span>
                    <hr>
                    <table class="table table-sm nowrap" id="table-bttb" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>No.</th>
                                <th>Tanggal</th>
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
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{ asset('assets/js/selectize.js') }}"></script>
<script src="{{ asset('assets/js/awesomplete.js') }}"></script>
<script>
    let barangs = @json($barang);
    let satuans = @json($satuan);
    let barang = document.getElementById("barang_id-3");
        new Awesomplete(barang, {
        list: barangs,
        minChars: 3,
        maxItems: 5
    });
    $('.selecttizecreate').selectize({
        sortField: 'text',
        maxOptions:10,
        create:true
    });
    $('.selecttize').selectize({
        sortField: 'text',
        maxOptions:10,
    });

    function hitungVolCreate(i){
        var p = $('#p-'+i).val();
        var l = $('#l-'+i).val();
        var t = $('#t-'+i).val();
        var vol = $('#vol-'+i).val();
        var qty = $('#qty-'+i).val();
        if(p>0&&l>0&&t>0){
            vol = ((p*l*t)/1000000) * qty;
            vol = vol.toFixed(2);
        }
        $('#vol-'+i).val(vol);
    }

    let tablebttb = $('#table-bttb').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('bttb.data') }}',
                method:'POST',
                data:function( d) {
                    d.order_id = @json(request('order_id'));
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id', visible:false },
                { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                { data: 'created_at', name: 'created_at' },
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
                { data: 'action', name: 'action', orderable: false, searchable: false, visible:false },
            ],
            select:true
        });
</script>
@endsection
