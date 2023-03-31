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
                    <button class="py-2 px-3 btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit" id="btn-edit"><i class="fas fa-pencil"></i> SJ Kembali</button>
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

<div class="modal fade" id="edit" tabindex="-1" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" id="edit-form" method="post" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="orderLabel">Update SJ Kembali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div class="mb-2 col-12">
                    <label for="container">Job ID</label>
                    <input type="text" name="job" id="job" class="form-control" readonly>
                </div>
                <div class="mb-2 col-12">
                    <label for="sj_kembali">SJ Kembali</label>
                    <input type="date" name="sj_kembali" id="sj_kembali" class="form-control">
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

        $('#btn-edit').hide();

        var data = @json($data);

        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: data,
            colModel: [
                {search:false, name: 'id', label : 'id', hidden:true},
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
                {search:true, name: 'tanggal', label : 'Tanggal', sorttype: 'date', datefmt:'d/m/y'},
                {search:true, name: 'invoice', label : 'Invoice'},
                {search:true, name: 'customer', label : 'Customer'},
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
                {search:true, name: 'tarif', label : 'Tarif'},
                {search:true, name: 'borongan', label : 'Borongan'},
                {search:true, name: 'sangu', label : 'Sangu Sopir'},
                {search:true, name: 'simpanan', label : 'Simpanan Sopir'},
                {search:true, name: 'kuli', label : 'Kuli'},
                {search:true, name: 'tambah_isi', label : 'Tambah Isi'},
                {search:true, name: 'tambah_solar', label : 'Tambah Solar'},
                {search:true, name: 'tb_tl', label : 'TB/TL'},
                {search:true, name: 'tally', label : 'Tally'},
                {search:true, name: 'uang_makan', label : 'Uang Makan'},
                {search:true, name: 'total_sopir', label : 'Totalan Sopir'},
                {search:true, name: 'tgl_total', label : 'Tanggal Totalan'},
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
            caption: "List Order Trucking SJ Belum Kembali",
            onCellSelect: function (rowId, iRow, iCol, e) {
                var id = $(this).jqGrid('getCell', rowId, 'id');
                var order_id = $(this).jqGrid('getCell', rowId, 'order_id');
                var job = $(this).jqGrid('getCell', rowId, 'job');
                var date_sj_kembali = $(this).jqGrid('getCell', rowId, 'date_sj_kembali');
                $('#edit-form').attr('action','{{ url('admin/ordertrucking') }}/'+id);
                $('#sj_kembali').val(date_sj_kembali);
                $('#job').val(job);
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
                    if(tipe=='combo'){
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
