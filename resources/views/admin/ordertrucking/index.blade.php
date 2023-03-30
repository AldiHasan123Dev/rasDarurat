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
</style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <div class="d-flex gap-2">
                    <button class="py-2 px-3 btn btn-success" data-bs-toggle="modal" data-bs-target="#order"><i class="fas fa-plus"></i> Tambah Order Trucking</button>
                    <button class="py-2 px-3 btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit" id="btn-edit"><i class="fas fa-pencil"></i> Edit</button>
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
    <div class="modal-dialog">
        <form action="{{ route('ordertrucking.store') }}" method="post" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="orderLabel">Tambah Order Trucking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="created_at">Tanggal</label>
                    <input type="date" name="created_at" id="created_at" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-2">
                    <label for="customer">Customer</label>
                    <select name="customer_id" id="customer" class="form-control" required>
                        @foreach ($customers as $cus)
                            <option {{ $loop->first?'selected':'' }} value="{{ $cus->id }}">{{ $cus->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label for="kendaraan">Kendaraan</label>
                    <select name="kendaraan_id" id="kendaraan" class="form-control" required>
                        @foreach ($kendaraan as $kend)
                            <option {{ $loop->first?'selected':'' }} value="{{ $kend->id }}">{{ $kend->nopol }} || {{ $kend->milik }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label for="sopir">Sopir</label>
                    <select name="sopir_id" id="sopir" class="form-control" required>
                        @foreach ($sopir as $sup)
                            <option {{ $loop->first?'selected':'' }} value="{{ $sup->id }}">{{ $sup->nama }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label for="container">No. Cont</label>
                    <input type="text" name="container" id="container" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label for="seal">Seal</label>
                    <input type="text" name="seal" id="seal" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label for="tipe">Tipe Cont</label>
                    <select name="tipe" id="tipe" class="form-control" required>
                        <option value="20">20 Fit</option>
                        <option value="40">40 Fit</option>
                        <option value="COMBO">COMBO</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label for="tujuan">Tujuan</label>
                    <select name="tujuan" id="tujuan" class="form-control" required>
                        @foreach ($tujuan as $loc)
                            <option {{ $loop->first?'selected':'' }} value="{{ $loc->id }}">{{ $loc->tujuanInfo->nama }} </option>
                        @endforeach
                    </select>
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
    <div class="modal-dialog">
        <form action="" id="edit-form" method="post" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="orderLabel">Update Order Trucking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="container">Job</label>
                    <select name="order_id" id="container" class="form-control">
                    </select>
                </div>
                <div class="mb-2">
                    <label for="sangu">Sangu</label>
                    <input type="text" name="sangu" id="sangu" class="form-control rupiah">
                </div>
                <div class="mb-2">
                    <label for="simpanan">Sangu Simpanan</label>
                    <input type="text" name="simpanan" id="simpanan" class="form-control rupiah">
                </div>
                <div class="mb-2">
                    <label for="sj_kembali">SJ Kembali</label>
                    <input type="date" name="sj_kembali" id="sj_kembali" class="form-control rupiah">
                </div>
                <div class="mb-2">
                    <label for="sj_kembali_fa">SJ Diterima FA</label>
                    <input type="date" name="sj_kembali_fa" id="sj_kembali_fa" class="form-control rupiah">
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
        $("#customer").select2({
            dropdownParent: $('#order'),
        });
        $("#kendaraan").select2({
            dropdownParent: $('#order'),
        });
        $("#tujuan").select2({
            dropdownParent: $('#order'),
        });
        $("#sopir").select2({
            dropdownParent: $('#order'),
        });

        var data = @json($data);

        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: data,
            colModel: [
                {search:true, name: 'id', label : 'id', hidden:true},
                {search:true, name: 'order_id', label : 'order_id', hidden:true},
                {search:true, name: 'date_sj_kembali', label : 'SJ Kembali D', hidden:true},
                {search:true, name: 'date_sj_kembali_fa', label : 'SJ Diterima FA D', hidden:true},
                {search:true, name: 'tanggal', label : 'Tanggal', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'invoice', label : 'Invoice'},
                {search:true, name: 'customer', label : 'Customer'},
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
                {search:true, name: 'tagihan', label : 'Tagihan'},
                {search:true, name: 'sangu', label : 'Sangu Sopir', editable:true},
                {search:true, name: 'simpanan', label : 'Simpanan Sopir'},
                {search:true, name: 'kuli', label : 'Kuli'},
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
                var sangu = $(this).jqGrid('getCell', rowId, 'sangu');
                var simpanan = $(this).jqGrid('getCell', rowId, 'simpanan');
                var nopol = $(this).jqGrid('getCell', rowId, 'nopol');
                var date_sj_kembali = $(this).jqGrid('getCell', rowId, 'date_sj_kembali');
                var date_sj_kembali_fa = $(this).jqGrid('getCell', rowId, 'date_sj_kembali_fa');
                $('#edit-form').attr('action','{{ url('admin/ordertrucking') }}/'+id);
                getOrder(nopol,order_id);
                $('#sangu').val(sangu);
                $('#simpanan').val(simpanan);
                $('#sj_kembali').val(date_sj_kembali);
                $('#sj_kembali_fa').val(date_sj_kembali_fa);
                $('#btn-edit').show();
            }
        });

        $('#jqGrid').jqGrid('filterToolbar');
		$('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
            search: false, // show search button on the toolbar
            add: false,
            edit: false,
            del: false,
            refresh: true
        });

        function getOrder(nopol,id) {
            $.ajax({
                type: "GET",
                url: "{{ url('api/get-order-nopol') }}"+'/'+nopol,
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
    </script>
@endsection
