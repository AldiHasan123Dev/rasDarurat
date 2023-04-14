@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<style>
    .select2.select2-container.select2-container--default{
        width: 100% !important;
    }
    tr td{
        padding: 2px 10px;
    }
    .bg-light-dark{
        background-color: #5e5e5e9e !important;
    }
</style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card-12">
            <div class="card">
                <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                    <div class="d-flex gap-2">
                        @if (Auth::user()->role_id==1)
                        <form action="{{ route('ordertrucking.export') }}" method="post">
                            @csrf
                            <button class="py-2 px-3 btn btn-sm btn-success" type="submit">Export Excel</button>
                        </form>
                    @endif
                        <button class="py-2 px-3 btn btn-success" data-bs-toggle="modal" data-bs-target="#order"><i class="fas fa-plus"></i> Tambah Order Trucking</button>
                        <button class="py-2 px-3 btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit" id="btn-edit"><i class="fas fa-pencil"></i> Edit</button>
                        <button class="py-2 px-3 btn btn-danger" type="button" id="delete"><i class="fas fa-trash"></i> Hapus</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsives">
                        <table id="jqGrid"></table>
                        <div id="jqGridPager"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3">
            <div class="card p-3 shadow">
                <p>Keterangan:</p>
                <table>
                    <tr>
                        <td style="width: 30px"><div class="bg-light-dark" style="height: 10px; width:20px"></div></td>
                        <td>: Order JOB Kosong (Check No. Container dan Seal harus sama persis)</td>
                    </tr>
                    <tr>
                        <td style="width: 30px"><div class="bg-primary" style="height: 10px; width:20px"></div></td>
                        <td>: SJ Diterima FA (Belum Totalan Sopir)</td>
                    </tr>
                    <tr>
                        <td style="width: 30px"><div class="bg-warning" style="height: 10px; width:20px"></div></td>
                        <td>: Sudah Totalan Sopir (Belum Terbit Invoice)</td>
                    </tr>
                    <tr>
                        <td style="width: 30px"><div class="bg-danger" style="height: 10px; width:20px"></div></td>
                        <td>: Sudah Terbit Invoice</td>
                    </tr>
                    <tr>
                        <td style="width: 30px"><div class="bg-success" style="height: 10px; width:20px"></div></td>
                        <td>: Customer RAS Tipe R2 (Tanpa Invoice)</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>


    {{-- <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasOrderTrucking" aria-labelledby="offcanvasOrderTruckingLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasOrderTruckingLabel">Form Order Trucking</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('ordertrucking.store') }}" method="post">
                @csrf
                @include('admin.ordertrucking.form')
            </form>
        </div>
    </div> --}}

<!-- Modal -->
<div class="modal fade" id="order" tabindex="-1" aria-labelledby="orderLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('ordertrucking.store') }}" method="post" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="orderLabel">Tambah Order Trucking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div class="mb-2 col-4">
                    <label for="tgl_muat">Tanggal Muat</label>
                    <input type="date" name="tgl_muat" id="tgl_muat" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-2 col-4">
                    <label for="customer">Customer</label>
                    <select name="customer_id" id="customer" class="form-control" required>
                        @foreach ($customers as $cus)
                            <option {{ $loop->first?'selected':'' }} value="{{ $cus->id }}">{{ $cus->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 col-4">
                    <label for="kendaraan">Kendaraan</label>
                    <select name="kendaraan_id" id="kendaraan" class="form-control" required>
                        @foreach ($kendaraan as $kend)
                            <option {{ $loop->first?'selected':'' }} value="{{ $kend->id }}">{{ $kend->nopol }} || {{ $kend->milik }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 col-4">
                    <label for="sopir">Sopir</label>
                    <select name="sopir_id" id="sopir" class="form-control" required>
                        @foreach ($sopir as $sup)
                            <option {{ $loop->first?'selected':'' }} value="{{ $sup->id }}">{{ $sup->nama }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 col-4">
                    <label for="container">No. Cont</label>
                    <input type="text" name="container" id="container" class="form-control" required>
                </div>
                <div class="mb-2 col-4">
                    <label for="seal">Seal</label>
                    <input type="text" name="seal" id="seal" class="form-control" required>
                </div>
                <div class="mb-2 col-4">
                    <label for="tipe">Tipe Cont</label>
                    <select name="tipe" id="tipe" class="form-control" required>
                        <option value="20">20'</option>
                        <option value="40">40'</option>
                        <option value="COMBO">COMBO</option>
                    </select>
                </div>
                <div class="mb-2 col-4">
                    <label for="tujuan">Tujuan</label>
                    <select name="tujuan" id="tujuan" class="form-control" required>
                        @foreach ($tujuan as $loc)
                            <option {{ $loop->first?'selected':'' }} value="{{ $loc->id }}">{{ $loc->tujuanInfo->nama }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 col-4">
                    <label for="sangu">Sangu Borongan</label>
                    <input type="text" name="sangu" id="sangu" class="form-control" disabled>
                </div>
                <div class="my-2 col-12">
                    Keterangan
                    <hr>
                    <div class="d-flex gap-3">
                        <div>
                            <label>
                                <input type="checkbox" name="ambil_empty_tambak_langon" value="1">
                                Ambil Empty Tambak Langon
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="checkbox" name="ambil_empty_teluk_langon" value="1">
                                Ambil Empty Teluk Lamong
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="checkbox" name="bongkar_full_teluk_langon" value="1">
                                Bongkar Full Teluk Lamong
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="edit" tabindex="-1" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form action="" id="edit-form" method="post" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="orderLabel">Update Order Trucking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div class="mb-2 col-3">
                    <label for="container">Job ID</label>
                    <input type="text" name="job" id="job" class="form-control" readonly>
                </div>
                <div class="mb-2 col-3">
                    <label for="tgl_muat">Tanggal Muat</label>
                    <input type="date" name="tgl_muat" id="tgl_muat_edit" class="form-control">
                </div>
                <div class="mb-2 col-3">
                    <label for="sj_kembali_fa">SJ Diterima FA</label>
                    <input type="date" name="sj_kembali_fa" id="sj_kembali_fa" class="form-control">
                </div>
                <div class="mb-2 col-3">
                    <label for="customer">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control" required>
                        @foreach ($customers as $cus)
                            <option {{ $loop->first?'selected':'' }} value="{{ $cus->id }}">{{ $cus->nama }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="mb-2 col-3">
                    <label for="kendaraan">Kendaraan</label>
                    <select name="kendaraan_id" id="kendaraan_id" class="form-control" required>
                        @foreach ($kendaraan as $kend)
                            <option {{ $loop->first?'selected':'' }} value="{{ $kend->id }}">{{ $kend->nopol }} || {{ $kend->milik }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 col-3">
                    <label for="sopir">Sopir</label>
                    <select name="sopir_id" id="sopir_id" class="form-control" required>
                        @foreach ($sopir as $sup)
                            <option {{ $loop->first?'selected':'' }} value="{{ $sup->id }}">{{ $sup->nama }} </option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="mb-2 col-3">
                    <label for="container">No. Cont</label>
                    <input type="text" name="container" id="container-edit" class="form-control" required>
                </div>
                <div class="mb-2 col-3">
                    <label for="seal">Seal</label>
                    <input type="text" name="seal" id="seal-edit" class="form-control" required>
                </div>
                {{-- <div class="mb-2 col-3">
                    <label for="tipe">Tipe Cont</label>
                    <select name="tipe" id="tipe-edit" class="form-control" required>
                        <option value="20">20'</option>
                        <option value="40">40'</option>
                        <option value="COMBO">COMBO</option>
                    </select>
                </div>
                <div class="mb-2 col-3">
                    <label for="tujuan">Tujuan</label>
                    <select name="tujuan" id="tujuan-edit" class="form-control" required>
                        @foreach ($tujuan as $loc)
                            <option {{ $loop->first?'selected':'' }} value="{{ $loc->id }}">{{ $loc->tujuanInfo->nama }} </option>
                        @endforeach
                    </select>
                </div> --}}
                {{-- <div class="mb-2 col-3">
                    <label for="sangu">Sangu</label>
                    <input type="text" name="sangu" id="sangu-edit" class="form-control" disabled>
                </div> --}}
                <div class="col-12 my-2">
                    Biaya Lain-lain
                    <hr>
                </div>
                <div class="mb-2 col-3">
                    <label for="borongan">Borongan</label>
                    <input type="text" name="borongan" id="borongan" class="form-control rupiah" readonly required>
                </div>
                <div class="mb-2 col-3">
                    <label for="sangu">Sangu Sopir</label>
                    <input type="text" name="sangu" id="sangu-edit" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-3">
                    <label for="borongan_kuli">Borongan Kuli</label>
                    <input type="text" name="borongan_kuli" id="borongan_kuli" class="form-control rupiah" readonly required>
                </div>
                <div class="mb-2 col-3">
                    <label for="kuli">Sangu Kuli</label>
                    <input type="text" name="kuli" id="kuli" class="form-control rupiah" required>
                </div>
                {{-- <div class="mb-2 col-3">
                    <label for="simpanan">Sangu Simpanan</label>
                    <input type="text" name="simpanan" id="simpanan" class="form-control rupiah" required>
                </div> --}}
                <div class="mb-2 col-3">
                    <label for="tambah_isi">Tambah Isi</label>
                    <input type="text" name="tambah_isi" id="tambah_isi" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-3">
                    <label for="tambah_solar">Tambah Solar</label>
                    <input type="text" name="tambah_solar" id="tambah_solar" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-3">
                    <label for="tb_tl">TB/TL</label>
                    <input type="text" name="tb_tl" id="tb_tl" class="form-control rupiah" readonly required>
                </div>
                <div class="mb-2 col-3">
                    <label for="tally">Tally</label>
                    <input type="text" name="tally" id="tally" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-3">
                    <label for="uang_makan">Uang Makan</label>
                    <input type="text" name="uang_makan" id="uang_makan" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-3">
                    <label for="op">OP/naikkan Mty</label>
                    <input type="text" name="op" id="op" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-3">
                    <label for="cleaning">Cleaning</label>
                    <input type="text" name="cleaning" id="cleaning" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-3">
                    <label for="stappel">Stappel/Inap</label>
                    <input type="text" name="stappel" id="stappel" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-3">
                    <label for="lain_lain">Lain-lain</label>
                    <input type="text" name="lain_lain" id="lain_lain" class="form-control rupiah" required>
                </div>
                <div class="my-2 col-12">
                    Keterangan
                    <hr>
                    <div class="d-flex gap-3">
                        <div>
                            <label>
                                <input type="checkbox" name="ambil_empty_tambak_langon" id="ambil_empty_tambak_langon" value="1">
                                Ambil Empty Tambak Langon
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="checkbox" name="ambil_empty_teluk_langon" id="ambil_empty_teluk_langon" value="1">
                                Ambil Empty Teluk Lamong
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="checkbox" name="bongkar_full_teluk_langon" id="bongkar_full_teluk_langon" value="1">
                                Bongkar Full Teluk Lamong
                            </label>
                        </div>
                    </div>
                    <textarea name="keterangan" id="keterangan" cols="30" rows="4" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script>
        // let table = $('.table').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax:{
        //         url: '{{ route('ordertrucking.data') }}',
        //         method:'POST',
        //         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        //     },
        //     columns: [
        //         { data: 'id', name: 'id' },
        //     { data: 'order_id', name: 'order_id' },
        //     { data: 'customer_id', name: 'customer_id' },
        //     { data: 'sopir_id', name: 'sopir_id' },
        //     { data: 'kendaraan_id', name: 'kendaraan_id' },
        //     { data: 'dari', name: 'dari' },
        //     { data: 'tujuan', name: 'tujuan' },
        //     { data: 'type', name: 'type' },
        //     { data: 'sangu', name: 'sangu' },
        //     { data: 'simpanan', name: 'simpanan' },
        //     { data: 'tagihan', name: 'tagihan' },
        //     { data: 'kuli', name: 'kuli' },
        //         { data: 'action', name: 'action', orderable: false, searchable: false },
        //     ]
        // });

        $('#btn-edit').hide();
        $('#delete').hide();
        $("#customer").select2({
            dropdownParent: $('#order'),
        });
        $("#kendaraan").select2({
            dropdownParent: $('#order'),
        });
        $("#customer_id").select2({
            dropdownParent: $('#edit'),
        });
        $("#kendaraan_id").select2({
            dropdownParent: $('#edit'),
        });
        $("#sopir_id").select2({
            dropdownParent: $('#edit'),
        });
        $("#tujuan").select2({
            dropdownParent: $('#order'),
        });
        $("#tujuan-edit").select2({
            dropdownParent: $('#edit'),
        });
        $("#tujuan").val('').trigger('change');
        $("#sopir").select2({
            dropdownParent: $('#order'),
        });

        var data = @json($data);

        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: data,
            colModel: [
                {search:false, name: 'id', label : 'ID', sorttype: 'number', width:50},
                {search:true, name: 'class', label : 'class', hidden:true},
                {search:false, name:'ambil_empty_tambak_langon', label:'#', hidden:true},
                {search:false, name:'ambil_empty_teluk_langon', label:'#', hidden:true},
                {search:false, name:'bongkar_full_teluk_langon', label:'#', hidden:true},
                {search:false, name: 'order_id', label : 'order_id', hidden:true},
                {search:false, name: 'customer_id', label : 'customer_id', hidden:true},
                {search:false, name: 'kendaraan_id', label : 'kendaraan_id', hidden:true},
                {search:false, name: 'sopir_id', label : 'sopir_id', hidden:true},
                {search:false, name: 'sangu_id', label : 'sangu_id', hidden:true},
                {search:false, name: 'date_sj_kembali', label : 'SJ Kembali D', hidden:true},
                {search:false, name: 'date_sj_kembali_fa', label : 'SJ Diterima FA D', hidden:true},
                {search:false, name: 'date_tgl_muat', label : 'Tanggal Muat D', hidden:true},
                // {search:true, name: 'tanggal', label : 'Tanggal', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'tgl_muat', label : 'Tanggal Muat', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'invoice', label : 'Invoice'},
                {search:true, name: 'tgl_invoice', label : 'Tgl Invoice'},
                {search:true, name: 'customer', label : 'Customer'},
                {search:true, name: 'trucking', label : 'Trucking'},
                {search:true, name: 'pembayar', label : 'Pembayar'},
                {search:true, name: 'job', label : 'Job'},
                {search:true, name: 'sopir', label : 'Sopir'},
                {search:true, name: 'nopol', label : 'Nopol'},
                {search:true, name: 'container', label : 'Container'},
                {search:true, name: 'seal', label : 'Seal'},
                {search:true, name: 'dari', label : 'Dari'},
                {search:true, name: 'tujuan', label : 'Tujuan'},
                {search:true, name: 'tipe', label : 'Tipe'},
                {search:true, name: 'sj_kembali', label : 'SJ Kembali'},
                {search:true, name: 'sj_kembali_fa', label : 'SJ Diterima FA'},
                {search:true, name: 'borongan', label : 'Borongan'},
                {search:true, name: 'sangu', label : 'Sangu Sopir'},
                {search:true, name: 'simpanan', label : 'Simpanan Sopir'},
                {search:true, name: 'borongan_kuli', label : 'Borongan Kuli'},
                {search:true, name: 'kuli', label : 'Sangu Kuli'},
                {search:true, name: 'simpanan_kuli', label : 'Simpanan Kuli'},
                {search:true, name: 'tambah_isi', label : 'Tambah Isi'},
                {search:true, name: 'tambah_solar', label : 'Tambah Solar'},
                {search:true, name: 'tb_tl', label : 'TB/TL'},
                {search:true, name: 'tally', label : 'Tally'},
                {search:true, name: 'uang_makan', label : 'Uang Makan'},
                {search:true, name: 'op', label : 'OP/naikkan Mty'},
                {search:true, name: 'cleaning', label : 'Cleaning'},
                {search:true, name: 'stappel', label : 'Stappel/Inap'},
                {search:true, name: 'lain_lain', label : 'Lain-lain'},
                {search:true, name: 'total_sopir', label : 'Totalan Sopir'},
                {search:true, name: 'tgl_total', label : 'Tanggal Totalan'},
                {search:true, name: 'tarif', label : 'Tarif'},
                {search:true, name: 'pph_21', label : 'PPh 21-3%'},
                {search:true, name: 'pph_23', label : 'PPh 23-2%'},
                {search:true, name: 'total_invoice', label : 'Inv'},
                {search:true, name: 'margin', label : 'Margin'},
                {search:true, name: 'keterangan', label : 'Keterangan', width:450},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100],
			viewrecords: true,
            pager: "#jqGridPager",
            caption: "Order Trucking",
            onCellSelect: function (rowId, iRow, iCol, e) {
                var id = $(this).jqGrid('getCell', rowId, 'id');
                var order_id = $(this).jqGrid('getCell', rowId, 'order_id');
                var job = $(this).jqGrid('getCell', rowId, 'job');
                var customer_id = $(this).jqGrid('getCell', rowId, 'customer_id');
                var kendaraan_id = $(this).jqGrid('getCell', rowId, 'kendaraan_id');
                var sopir_id = $(this).jqGrid('getCell', rowId, 'sopir_id');
                var tipe = $(this).jqGrid('getCell', rowId, 'tipe');
                var sangu_id = $(this).jqGrid('getCell', rowId, 'sangu_id');
                var sangu = $(this).jqGrid('getCell', rowId, 'sangu');
                var borongan = $(this).jqGrid('getCell', rowId, 'borongan');
                var tambah_isi = $(this).jqGrid('getCell', rowId, 'tambah_isi');
                var tambah_solar = $(this).jqGrid('getCell', rowId, 'tambah_solar');
                var tb_tl = $(this).jqGrid('getCell', rowId, 'tb_tl');
                var tally = $(this).jqGrid('getCell', rowId, 'tally');
                var uang_makan = $(this).jqGrid('getCell', rowId, 'uang_makan');
                var kuli = $(this).jqGrid('getCell', rowId, 'kuli');
                var borongan_kuli = $(this).jqGrid('getCell', rowId, 'borongan_kuli');
                var op = $(this).jqGrid('getCell', rowId, 'op');
                var cleaning = $(this).jqGrid('getCell', rowId, 'cleaning');
                var stappel = $(this).jqGrid('getCell', rowId, 'stappel');
                var lain_lain = $(this).jqGrid('getCell', rowId, 'lain_lain');
                var container = $(this).jqGrid('getCell', rowId, 'container');
                var seal = $(this).jqGrid('getCell', rowId, 'seal');
                var simpanan = $(this).jqGrid('getCell', rowId, 'simpanan');
                var nopol = $(this).jqGrid('getCell', rowId, 'nopol');
                var date_sj_kembali = $(this).jqGrid('getCell', rowId, 'date_sj_kembali');
                var date_sj_kembali_fa = $(this).jqGrid('getCell', rowId, 'date_sj_kembali_fa');
                var date_tgl_muat = $(this).jqGrid('getCell', rowId, 'date_tgl_muat');
                var ambil_empty_tambak_langon = $(this).jqGrid('getCell', rowId, 'ambil_empty_tambak_langon');
                var ambil_empty_teluk_langon = $(this).jqGrid('getCell', rowId, 'ambil_empty_teluk_langon');
                var bongkar_full_teluk_langon = $(this).jqGrid('getCell', rowId, 'bongkar_full_teluk_langon');
                $('#edit-form').attr('action','{{ url('admin/ordertrucking') }}/'+id);
                $('#delete').val(id);
                $('#job').val(job);
                $('#borongan').val(borongan);
                $('#tambah_isi').val(tambah_isi);
                $('#tambah_solar').val(tambah_solar);
                $('#tb_tl').val(tb_tl);
                $('#tally').val(tally);
                $('#uang_makan').val(uang_makan);
                $('#kuli').val(kuli);
                $('#sangu-edit').val(sangu);
                $('#tipe-edit').val(tipe);
                $('#simpanan').val(simpanan);
                $('#borongan_kuli').val(borongan_kuli);
                $('#op').val(op);
                $('#cleaning').val(cleaning);
                $('#stappel').val(stappel);
                $('#lain_lain').val(lain_lain);
                $('#sj_kembali').val(date_sj_kembali);
                $('#sj_kembali_fa').val(date_sj_kembali_fa);
                $("#customer_id").val(customer_id).trigger('change');
                $("#kendaraan_id").val(kendaraan_id).trigger('change');
                $("#sopir_id").val(sopir_id).trigger('change');
                $("#tujuan-edit").val(sangu_id).trigger('change');
                $("#container-edit").val(container);
                $("#seal-edit").val(seal);
                $("#tgl_muat_edit").val(date_tgl_muat);
                $('#btn-edit').show();
                $('#delete').show();
                if(ambil_empty_tambak_langon==1){
                    $('#ambil_empty_tambak_langon').attr('checked',true);
                }
                if(ambil_empty_teluk_langon==1){
                    $('#ambil_empty_teluk_langon').attr('checked',true);
                }
                if(bongkar_full_teluk_langon==1){
                    $('#bongkar_full_teluk_langon').attr('checked',true);
                }
            },
            rowattr: function (item) {
                return { "class": item.class };
            }
        });

        $('#jqGrid').jqGrid('filterToolbar',{stringResult: true, searchOnEnter: false, defaultSearch: 'cn'});
		$('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
            search: false, // show search button on the toolbar
            add: false,
            edit: false,
            del: false,
            refresh: true
        });

        function getOrder(container,id) {
            $.ajax({
                type: "GET",
                url: "{{ url('api/get-order-container') }}"+'/'+container,
                success: function (response) {
                    var options_cont = '';
                    options_cont += `<option value="">-</option>`;
                    $.each(response, function (idx, item) {
                        if (id==item.id) {
                            options_cont += `<option selected value="${item.id}">${item.job}-${pad(item.no_job, 2)} || ${item.container}</option>`
                        }else{
                            options_cont += `<option value="${item.id}">${item.job}-${pad(item.no_job, 2)} || ${item.container}</option>`
                        }
                    });

                    $('#container').append(options_cont);
                }
            });

        }

        function pad(n, width, z) {
            z = z || '0';
            n = n + '';
            return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
        }

        $('#tujuan').change(function (e) {
            e.preventDefault();
            getSangu();
        });

        $('#tipe').change(function (e) {
            e.preventDefault();
            getSangu();
        });

        function getSangu(){
            let tujuan = $('#tujuan').val();
            let tipe = $('#tipe').val();

            $.ajax({
                type: "POST",
                url: "{{ route('api.sangusopir.getSangu') }}",
                data: {
                    tujuan:tujuan
                },
                success: function (response) {
                    if(tipe==20){
                        $('#sangu').val(rp(response.ukuran_20));
                    }
                    if(tipe==40){
                        $('#sangu').val(rp(response.ukuran_40));
                    }
                    if(tipe=='COMBO'){
                        $('#sangu').val(rp(response.ukuran_combo));
                    }
                }
            });
        }

        const rp = (num) => num.toLocaleString('en-US');

        $('#delete').click(function (e) {
            e.preventDefault();
            if(confirm('are you sure?')){
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('order-trucking.delete') }}",
                    data: {
                        id:$('#delete').val()
                    },
                    success: function (response) {
                        alert('Hapus Data Berhasil!');
                        location.reload();
                    }
                });
            }
        });
    </script>
@endsection
