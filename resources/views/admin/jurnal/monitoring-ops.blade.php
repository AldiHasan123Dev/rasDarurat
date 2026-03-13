@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">

    <style>
        .select2.select2-container.select2-container--default {
            width: 100% !important;
        }

        tr td {
            padding: 2px 10px;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endsection


@section('content')
    <div class="container mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="jqGrid"></table>
                        <div id="jqGridPager"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="container mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="jqGrid1"></table>
                        <div id="jqGridPager1"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="container mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="jqGrid2"></table>
                        <div id="jqGridPager2"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditOps" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Cek OPS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditOps">
                @csrf
                <div class="modal-body">

                    <input type="hidden" name="id" id="ops_id">

                    <div class="mb-2">
                        <label>Job</label>
                        <input type="text" class="form-control" id="ops_job" readonly>
                    </div>

                    <div class="mb-2">
                        <label>No Job</label>
                        <input type="text" class="form-control" id="ops_no" readonly>
                    </div>

                    <div class="mb-2">
                        <label>Cek OPS</label>
                        <input type="date" class="form-control" name="cek_ops" id="ops_value">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalEditChecker" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Cek Checker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditChecker">
                @csrf

                <div class="modal-body">

                    <input type="hidden" name="id" id="checker_id">

                    <div class="mb-2">
                        <label>Job</label>
                        <input type="text" class="form-control" id="checker_job" readonly>
                    </div>

                    <div class="mb-2">
                        <label>No Job</label>
                        <input type="text" class="form-control" id="checker_no" readonly>
                    </div>

                    <div class="mb-2">
                        <label>Cek Checker</label>
                        <input type="date" class="form-control" name="cek_checker" id="checker_value">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalEditKuli" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Cek TKBM</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditKuli">
                @csrf

                <div class="modal-body">

                    <input type="hidden" name="id" id="kuli_id">

                    <div class="mb-2">
                        <label>Job</label>
                        <input type="text" class="form-control" id="kuli_job" readonly>
                    </div>

                    <div class="mb-2">
                        <label>No Job</label>
                        <input type="text" class="form-control" id="kuli_no" readonly>
                    </div>

                    <div class="mb-2">
                        <label>Cek TKBM</label>
                        <input type="date" class="form-control" name="cek_kuli" id="kuli_value">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection



@section('script')
    <script src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script src="{{ asset('assets/js/resize-column.js') }}"></script>

    <script>
        let selectedId = null;

        /* FORMAT RUPIAH */
        const rp = (num) => {
            if (!num) return "0";
            return Number(num).toLocaleString('en-US');
        };


        /* ==============================
           GRID 1 : CEK OPS
        ============================== */

        $("#jqGrid").jqGrid({

            url: '{{ route('jqgrid.order') }}',
            mtype: "GET",
            datatype: "json",
            postData:{
        cek:"ops"
    },

            colModel: [
                {
                    name: 'cek_ops',
                    label: 'Cek OPS',
                    width: 150
                },
                {
                    name: 'job',
                    label: 'Job',
                    search: true,
                    frozen: true,
                    width: 120
                },

                {
                    name: 'no',
                    label: 'No',
                    search: true,
                    frozen: true,
                    width: 120
                },

                {
                    name: 'id',
                    hidden: true
                },

                {
                    name: 'class',
                    hidden: true
                },

                {
                    name: 'jurnal_piutang',
                    hidden: true
                },

                {
                    name: 'container',
                    label: 'Container',
                    width: 150
                },

                {
                    name: 'seal',
                    label: 'Seal',
                    width: 150
                }
            ],

            autowidth: true,
            shrinkToFit: true,
            height: 250,

            loadonce: false,

            rowNum: 25,
            rowList: [10, 25, 50, 100, 250, 500, 1000],

            viewrecords: true,

            pager: "#jqGridPager",

            caption: "Cek Ops",

           ondblClickRow:function(id){

    let row = $("#jqGrid").jqGrid('getRowData',id);

    $('#ops_id').val(id);
    $('#ops_job').val(row.job);
    $('#ops_no').val(row.no);
    $('#ops_value').val(row.cek_ops);

        selectedContainer = row.container;
    selectedSeal = row.seal;

    $('#modalEditOps').modal('show');

},

            rowattr: function(row) {

                return {
                    "class": row.class
                };

            }

        });


        $('#jqGrid').jqGrid('filterToolbar');

        $('#jqGrid').jqGrid('navGrid', "#jqGridPager", {

            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true

        });

        $("#jqGrid").jqGrid('setFrozenColumns');



        /* ==============================
           GRID 2 : CEK CHECKER
        ============================== */

        $("#jqGrid1").jqGrid({

            url: '{{ route('jqgrid.order') }}',

            mtype: 'GET',

            datatype: 'json',
            postData:{
        cek:"checker"
    },

            colModel: [
                {
                    name: 'cek_checker',
                    label: 'Cek Checker',
                    width: 150
                },
                {
                    name: 'job',
                    label: 'Job',
                    search: true,
                    frozen: true,
                    width: 120
                },

                {
                    name: 'no',
                    label: 'No',
                    search: true,
                    frozen: true,
                    width: 120
                },

                {
                    name: 'id',
                    hidden: true
                },

                {
                    name: 'class',
                    hidden: true
                },

                {
                    name: 'container',
                    label: 'Container',
                    width: 150
                },

                {
                    name: 'seal',
                    label: 'Seal',
                    width: 150
                }

            ],

            autowidth: true,

            shrinkToFit: true,

            height: 250,

            loadonce: false,

            rowNum: 25,

            rowList: [10, 25, 50, 100, 250, 500, 1000],

            viewrecords: true,

            pager: "#jqGridPager1",

            caption: "Cek Checker",
           ondblClickRow:function(id){

    let row = $("#jqGrid1").jqGrid('getRowData',id);

    $('#checker_id').val(id);
    $('#checker_job').val(row.job);
    $('#checker_no').val(row.no);
    $('#checker_value').val(row.cek_checker);

            selectedContainer = row.container;
    selectedSeal = row.seal;

    $('#modalEditChecker').modal('show');

},

            rowattr: function(row) {

                return {
                    "class": row.class
                };

            }

        });


        $('#jqGrid1').jqGrid('filterToolbar');

        $('#jqGrid1').jqGrid('navGrid', "#jqGridPager1", {

            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true

        });

        $("#jqGrid1").jqGrid('setFrozenColumns');

        $("#jqGrid2").jqGrid({

            url: '{{ route('jqgrid.order') }}',

            mtype: 'GET',

            datatype: 'json',
            postData:{
        cek:"kuli"
    },

            colModel: [
                {
                    name: 'cek_kuli',
                    label: 'Cek TKBM',
                    width: 150
                },
                {
                    name: 'job',
                    label: 'Job',
                    search: true,
                    frozen: true,
                    width: 120
                },

                {
                    name: 'no',
                    label: 'No',
                    search: true,
                    frozen: true,
                    width: 120
                },

                {
                    name: 'id',
                    hidden: true
                },

                {
                    name: 'class',
                    hidden: true
                },

                {
                    name: 'container',
                    label: 'Container',
                    width: 150
                },

                {
                    name: 'seal',
                    label: 'Seal',
                    width: 150
                }

            ],

            autowidth: true,

            shrinkToFit: true,

            height: 250,

            loadonce: false,

            rowNum: 25,

            rowList: [10, 25, 50, 100, 250, 500, 1000],

            viewrecords: true,

            pager: "#jqGridPager2",

            caption: "Cek TKBM",

            ondblClickRow:function(id){

    let row = $("#jqGrid2").jqGrid('getRowData',id);

    $('#kuli_id').val(id);
    $('#kuli_job').val(row.job);
    $('#kuli_no').val(row.no);
    $('#kuli_value').val(row.cek_kuli);

            selectedContainer = row.container;
    selectedSeal = row.seal;

    $('#modalEditKuli').modal('show');

},

            rowattr: function(row) {

                return {
                    "class": row.class
                };

            }

        });


        $('#jqGrid2').jqGrid('filterToolbar');

        $('#jqGrid2').jqGrid('navGrid', "#jqGridPager2", {

            search: false,
            add: false,
            edit: false,
            del: false,
            refresh: true

        });

        $("#jqGrid2").jqGrid('setFrozenColumns');

       $('#formEditOps').submit(function(e){

    e.preventDefault();

    $.ajax({
        url: "{{ route('order.update.ops') }}",
        type: "POST",
        data: $(this).serialize(),

        success: function(response){

            alert(
        'Cek Ops Berhasil Di Update\n\n' +
        'Container : ' + selectedContainer + '\n' +
        'Seal : ' + selectedSeal
    );

            $('#modalEditOps').modal('hide');
            $("#jqGrid").trigger("reloadGrid");

        },

        error: function(xhr){

            let message = "Terjadi kesalahan";

            if(xhr.responseJSON && xhr.responseJSON.message){
                message = xhr.responseJSON.message;
            }

            alert("Error : " + message);
        }

    });

});

$('#formEditChecker').submit(function(e){

    e.preventDefault();

    $.ajax({
        url: "{{ route('order.update.checker') }}",
        type: "POST",
        data: $(this).serialize(),

        success: function(response){

            alert(
        'Cek Checker Berhasil Di Update\n\n' +
        'Container : ' + selectedContainer + '\n' +
        'Seal : ' + selectedSeal
    );

            $('#modalEditChecker').modal('hide');
            $("#jqGrid1").trigger("reloadGrid");

        },

        error: function(xhr){

            let message = "Terjadi kesalahan";

            if(xhr.responseJSON && xhr.responseJSON.message){
                message = xhr.responseJSON.message;
            }

            alert("Error : " + message);
        }

    });

});

$('#formEditKuli').submit(function(e){

    e.preventDefault();

    $.ajax({
        url: "{{ route('order.update.kuli') }}",
        type: "POST",
        data: $(this).serialize(),

        success: function(response){

            alert(
        'Cek Checker Berhasil Di Update\n\n' +
        'Container : ' + selectedContainer + '\n' +
        'Seal : ' + selectedSeal
    );

            $('#modalEditKuli').modal('hide');
            $("#jqGrid2").trigger("reloadGrid");

        },

        error: function(xhr){

            let message = "Terjadi kesalahan";

            if(xhr.responseJSON && xhr.responseJSON.message){
                message = xhr.responseJSON.message;
            }

            alert("Error : " + message);
        }

    });

});
    </script>
@endsection
