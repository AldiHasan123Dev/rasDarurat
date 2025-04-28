@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <style>
        .select2.select2-container.select2-container--default {
            width: 100% !important;
        }

        tr td {
            padding: 2px 10px;
        }

        .bg-light-dark {
            background-color: #5e5e5e9e !important;
        }

        .bg-purple {
            background-color: purple !important;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-3">
        <!-- Legend / Judul -->
        <div class="card mb-3 d-inline-block">
            <div class="card-body py-2 px-3">
                <h5 class="fw-bold text-primary m-0">Monitoring Biaya Truck</h5>
            </div>
        </div>

        <!-- Grid Pertama -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
                </div>
            </div>
        </div>

        <!-- Grid Kedua -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="jqGrid1"></table>
                    <div id="jqGridPager1"></div>
                </div>
            </div>
        </div>

        <!-- Grid Ketiga -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="jqGrid2"></table>
                    <div id="jqGridPager2"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal untuk Grid Sangu Kuli -->
    <div class="modal fade" id="modalSanguKuli" tabindex="-1" aria-labelledby="modalSanguKuliLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formSanguKuli">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSanguKuliLabel">Edit Sangu Kuli</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <input type="hidden" id="rowId" name="id">

                        <div class="col-md-6">
                            <label for="sopir" class="form-label">Sopir</label>
                            <input type="text" class="form-control" id="sopir" readonly>
                        </div>

                        <div class="col-md-6">
                            <label for="nopol" class="form-label">Kendaraan</label>
                            <input type="text" class="form-control" id="nopol" readonly>
                        </div>

                        {{-- <div class="col-md-6">
                            <label for="nominal_sangu" class="form-label">Sangu Sopir</label>
                            <input type="number" class="form-control" id="nominal_sangu" readonly>
                        </div> --}}

                        {{-- <div class="col-md-6">
                            <label for="nominal_sangu_kuli" class="form-label">Sangu Kuli</label>
                            <input type="number" class="form-control" readonly id="nominal_sangu_kuli">
                        </div> --}}
{{-- 
                        <div class="col-md-6">
                            <label for="tambahan_sangu_sopir1" class="form-label">Tambahan Sangu Sopir (1) </label>
                            <input type="number" class="form-control" id="nominal_sangu1" name="nominal_sangu_sopir1">
                        </div>

                        <div class="col-md-6">
                            <label for="tambahan_sangu_sopir2" class="form-label">Tambahan Sangu Sopir (2)</label>
                            <input type="number" class="form-control" id="nominal_sangu2" name="nominal_sangu_sopir2">
                        </div> --}}
                        <div class="col-md-6">
                            <label for="tgl_sangu_kuli1" class="form-label">Tanggal Tambahan Sangu (1)</label>
                            <input type="date" class="form-control" id="tgl_sangu_kuli1" name="tgl_sangu_kuli1">
                        </div>
                        <div class="col-md-6">
                            <label for="tambahan_sangu_kuli1" class="form-label">Tambahan Sangu Kuli (1)</label>
                            <input type="number" class="form-control" id="nominal_sangu_kuli1" name="nominal_sangu_kuli1">
                        </div>
                        <div class="col-md-6">
                            <label for="tgl_sangu_kuli2" class="form-label">Tanggal Tambahan Sangu (2)</label>
                            <input type="date" class="form-control" id="tgl_sangu_kuli2" name="tgl_sangu_kuli2">
                        </div>
                        <div class="col-md-6">
                            <label for="tambahan_sangu_kuli2" class="form-label">Tambahan Sangu Kuli (2)</label>
                            <input type="number" class="form-control" id="nominal_sangu_kuli2" name="nominal_sangu_kuli2">
                        </div>
                        <div class="col-md-6">
                            <label for="tgl_sangu_kuli3" class="form-label">Tanggal Tambahan Sangu (3)</label>
                            <input type="date" class="form-control" id="tgl_sangu_kuli3" name="tgl_sangu_kuli3">
                        </div>
                        <div class="col-md-6">
                            <label for="tambahan_sangu_kuli3" class="form-label">Tambahan Sangu Kuli (3)</label>
                            <input type="number" class="form-control" id="nominal_sangu_kuli3" name="nominal_sangu_kuli3">
                        </div>

                        <!-- Tambahkan field lain sesuai kebutuhan -->

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="simpan_sangu" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal untuk TB/TL -->
    <div class="modal fade" id="modalTBTL" tabindex="-1" aria-labelledby="modalTBTLLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formTBTL">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTBTLLabel">Edit TB/TL</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <input type="hidden" id="rowId1" name="id">
                        <div class="col-md-6">
                            <label for="sopir_tbtl" class="form-label">Sopir</label>
                            <input type="text" class="form-control" id="sopir_tbtl" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="nopol_tbtl" class="form-label">Kendaraan</label>
                            <input type="text" class="form-control" id="nopol_tbtl" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="tgl_tb_tl" class="form-label">Tanggal Tambahan TB/TL (1)</label>
                            <input type="date" class="form-control" id="tgl_tb_tl" name="tgl_tb_tl">
                        </div>
                        <div class="col-md-6">
                            <label for="nominal_tb_tl" class="form-label">Tambahan TB/TL (1)</label>
                            <input type="number" class="form-control" id="nominal_tb_tl1" name="nominal_tb_tl1">
                        </div>
                        <div class="col-md-6">
                            <label for="tgl_tb_tl1" class="form-label">Tanggal Tambahan TB/TL(2)</label>
                            <input type="date" class="form-control" id="tgl_tb_tl1" name="tgl_tb_tl1">
                        </div>
                        <div class="col-md-6">
                            <label for="nominal_tb_tl2" class="form-label">Tambahan TB/TL (2)</label>
                            <input type="number" class="form-control" id="nominal_tb_tl2" name="nominal_tb_tl2">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="simpan_tb_tl" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal untuk Stappel -->
    <div class="modal fade" id="modalStappel" tabindex="-1" aria-labelledby="modalStappelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formStappel">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalStappelLabel">Edit Stappel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <input type="hidden" id="rowId2" name="id">
                        <div class="col-md-6">
                            <label for="sopir_stappel" class="form-label">Sopir</label>
                            <input type="text" class="form-control" id="sopir_stappel" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="nopol_stappel" class="form-label">Kendaraan</label>
                            <input type="text" class="form-control" id="nopol_stappel" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="tgl_stappel" class="form-label">Tanggal Tambahan Stappel</label>
                            <input type="date" class="form-control" id="tgl_stappel" name="tgl_stappel">
                        </div>
                        <div class="col-md-6">
                            <label for="nominal_stappel1" class="form-label">Tambahan Stappel</label>
                            <input type="number" class="form-control" id="nominal_stappel1" name="nominal_stappel1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="simpan_stappel" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/jquery-serializeFields.js') }}"></script>
    <script src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#jqGrid").jqGrid({
                url: '{{ route('jqgrid1.ordertrucking') }}',
                mtype: 'GET',
                datatype: 'json',
                postData: {
                    sangu_kuli: true
                },
                caption: "Sangu Kuli", // Caption tampil di atas grid
                colModel: [{
                    search:true,
                        name: 'order_trucking_id',
                        label: 'ID',
                        width: 90,
                        sorttype: 'number'
                    },
                    {
                        search:true,
                        name: 'job',
                        label: 'Job'
                    },
                    {
                        search:true,
                        name: 'sopir',
                        label: 'Sopir'
                    },
                    {
                        search:true,
                        name: 'nopol',
                        label: 'Kendaraan'
                    },
                    {
                        search:true,
                        name: 'tgl_muat',
                        label: 'Tgl Muat'
                    },
                    {
                        search:true,
                        name: 'container',
                        label: 'Container'
                    },
                    {
                        search:true,
                        name: 'seal',
                        label: 'Seal'
                    },
                    {
                        search:true,
                        name: 'customer',
                        label: 'Customer'
                    },
                    {
                        search:true,
                        name: 'tujuan',
                        label: 'Tujuan'
                    },
                    {
                        search:true,
                        name: 'nominal_sangu_kuli1',
                        label: 'Tambahan Sangu Kuli 1',
                        sorttype: 'number',
                        formatter: 'number',
                        formatoptions: {
                            decimalSeparator: ".",
                            thousandsSeparator: ",",
                            decimalPlaces: 0,
                            defaultValue: '0'
                        }
                    },
                    {
                        search:true,
                        name: 'tgl_sangu_kuli1',
                        label: 'Tgl Tambahan Sangu Kuli 1'
                    },
                    {
                        search:true,
                        name: 'nominal_sangu_kuli2',
                        label: 'Tambahan Sangu Kuli 2',
                        sorttype: 'number',
                        formatter: 'number',
                        formatoptions: {
                            decimalSeparator: ".",
                            thousandsSeparator: ",",
                            decimalPlaces: 0,
                            defaultValue: '0'
                        }
                    },
                    {
                        search:true,
                        name: 'tgl_sangu_kuli2',
                        label: 'Tgl Tambahan Sangu Kuli 2'
                    },
                    {
                        search:true,
                        name: 'nominal_sangu_kuli3',
                        label: 'Tambahan Sangu Kuli 3',
                        sorttype: 'number',
                        formatter: 'number',
                        formatoptions: {
                            decimalSeparator: ".",
                            thousandsSeparator: ",",
                            decimalPlaces: 0,
                            defaultValue: '0'
                        }
                    },
                    {
                        search:true,
                        name: 'tgl_sangu_kuli3',
                        label: 'Tgl Tambahan Sangu Kuli 3'
                    },
                ],
                autowidth: true,
                shrinkToFit: false,
                height: "auto",
                rowNum: 10,
                rowList: [10, 25, 50, 100],
                viewrecords: true,
                pager: "#jqGridPager",
                onCellSelect: function(rowId, iRow, iCol, e) {
                    var id = $(this).jqGrid('getCell', rowId, 'id');
                    // var sangu = $(this).jqGrid('getCell', rowId, 'sangu');
                    // var simpanan = $(this).jqGrid('getCell', rowId, 'simpanan');
                    // var nopol = $(this).jqGrid('getCell', rowId, 'nopol');
                    // $('#edit-form').attr('action','{{ url('admin/ordertrucking') }}/'+id);
                    // getOrder(nopol,order_id);
                    // $('#sangu').val(sangu);
                    // $('#simpanan').val(simpanan);
                    // $('#btn-edit').show();
                },
                loadComplete: function() {
                    $("#jqGrid").jqGrid('filterToolbar', {
                        searchOperators: false,
                        searchOnEnter: false,
                        defaultSearch: "cn"
                    });
                },
            });
            $("#jqGrid1").jqGrid({
                url: '{{ route('jqgrid1.ordertrucking') }}',
                mtype: 'GET',
                datatype: 'json',
                postData: {
                    tb_tl: true
                },
                caption: "TB/TL", // Caption tampil di atas grid
                colModel: [{
                    search:true,
                        name: 'order_trucking_id',
                        label: 'ID',
                        width: 90,
                        sorttype: 'number'
                    },
                    {
                        search:true,
                        name: 'job',
                        label: 'Job'
                    },
                    {
                        search:true,
                        name: 'sopir',
                        label: 'Sopir'
                    },
                    {
                        search:true,
                        name: 'nopol',
                        label: 'Kendaraan'
                    },
                    {
                        search:true,
                        name: 'tgl_muat',
                        label: 'Tgl Muat'
                    },
                    {
                        search:true,
                        name: 'container',
                        label: 'Container'
                    },
                    {
                        search:true,
                        name: 'seal',
                        label: 'Seal'
                    },
                    {  
                        search:true,
                        name: 'customer',
                        label: 'Customer'
                    },
                    {
                        search:true,
                        name: 'tujuan',
                        label: 'Tujuan'
                    },
                    {
                        search:true,
                        name: 'tgl_tb_tl',
                        label: 'Tgl Tambahan TB/TL 1'
                    },
                    {
                        search:true,
                        name: 'nominal_tb_tl1',
                        label: 'Tambahan TB/TL 1',
                        sorttype: 'number',
                        formatter: 'number',
                        formatoptions: {
                            decimalSeparator: ".",
                            thousandsSeparator: ",",
                            decimalPlaces: 0,
                            defaultValue: '0'
                        }

                    },
                    {
                        search:true,
                        name: 'nominal_tb_tl2',
                        label: 'Tambahan TB/TL 2',
                        sorttype: 'number',
                        formatter: 'number',
                        formatoptions: {
                            decimalSeparator: ".",
                            thousandsSeparator: ",",
                            decimalPlaces: 0,
                            defaultValue: '0'
                        }

                    },
                    {
                        search:true,
                        name: 'tgl_tb_tl1',
                        label: 'Tgl Tambahan TB/TL 2'
                    },
                ],
                autowidth: true,
                shrinkToFit: false,
                height: "auto",
                rowNum: 10,
                rowList: [10, 25, 50, 100],
                viewrecords: true,
                pager: "#jqGridPager1",
                loadComplete: function() {
                    $("#jqGrid1").jqGrid('filterToolbar', {
                        searchOperators: false,
                        searchOnEnter: false,
                        defaultSearch: "cn"
                    });
                },
            });
            $("#jqGrid2").jqGrid({
                url: '{{ route('jqgrid1.ordertrucking') }}',
                mtype: 'GET',
                datatype: 'json',
                postData: {
                    stappel: true
                },
                caption: "Stappel", // Caption tampil di atas grid
                colModel: [{
                    search:true,
                        name: 'order_trucking_id',
                        label: 'ID',
                        width: 90,
                        sorttype: 'number'
                    },
                    {
                        search:true,
                        name: 'job',
                        label: 'Job'
                    },
                    {
                        search:true,
                        name: 'sopir',
                        label: 'Sopir'
                    },
                    {
                        search:true,
                        name: 'nopol',
                        label: 'Kendaraan'
                    },
                    {
                        search:true,
                        name: 'tgl_muat',
                        label: 'Tgl Muat'
                    },
                    {
                        search:true,
                        name: 'container',
                        label: 'Container'
                    },
                    {
                        search:true,
                        name: 'seal',
                        label: 'Seal'
                    },
                    {
                        search:true,
                        name: 'customer',
                        label: 'Customer'
                    },
                    {
                        search:true,
                        name: 'tujuan',
                        label: 'Tujuan'
                    },
                    {
                        search:true,
                        name: 'nominal_stappel1',
                        label: 'Tambahan Stappel',
                        sorttype: 'number',
                        formatter: 'number',
                        formatoptions: {
                            decimalSeparator: ".",
                            thousandsSeparator: ",",
                            decimalPlaces: 0,
                            defaultValue: '0'
                        }
                    },
                    {
                        search:true,
                        name: 'tgl_stappel',
                        label: 'Tgl Tambahan Stappel'
                    },
                ],
                autowidth: true,
                shrinkToFit: false,
                height: "auto",
                rowNum: 10,
                rowList: [10, 25, 50, 100],
                viewrecords: true,
                pager: "#jqGridPager2",
                loadComplete: function() {
                    $("#jqGrid2").jqGrid('filterToolbar', {
                        searchOperators: false,
                        searchOnEnter: false,
                        defaultSearch: "cn"
                    });
                },
            });
        });
        // Klik baris Grid 1
        $("#jqGrid").on("dblclick", "tr.jqgrow", function() {
            let rowId = $(this).attr("id");
            let rowData = $("#jqGrid").jqGrid('getRowData', rowId);
            // Isi input modal
            $("#rowId").val(rowId);
            $("#sopir").val(rowData.sopir);
            $("#nopol").val(rowData.nopol);
            // $("#nominal_sangu").val(rowData.nominal_sangu);
            // $("#nominal_sangu_kuli").val(rowData.nominal_sangu_kuli);
            $("#tgl_sangu_kuli1").val(rowData.tgl_sangu_kuli1);
            $("#tgl_sangu_kuli2").val(rowData.tgl_sangu_kuli2);
            $("#tgl_sangu_kuli3").val(rowData.tgl_sangu_kuli3);
            $("#nominal_sangu_kuli1").val(rowData.nominal_sangu_kuli1);
            $("#nominal_sangu_kuli2").val(rowData.nominal_sangu_kuli2);
            $("#nominal_sangu_kuli3").val(rowData.nominal_sangu_kuli3);

            // Fungsi: toggle readonly berdasarkan nilai input
            function toggleReadonlyFields() {
                let valKuli1 = parseFloat($("#nominal_sangu_kuli1").val().replace(/,/g, '')) || 0;
                let valKuli2 = parseFloat($("#nominal_sangu_kuli2").val().replace(/,/g, '')) || 0;
                let valKuli3 = parseFloat($("#nominal_sangu_kuli3").val().replace(/,/g, '')) || 0;


                // $("#nominal_sangu1").prop("readonly", valSopir1 > 0);
                // $("#nominal_sangu_kuli1").prop("readonly", valKuli1 > 0);

                // let tahap1Siap = valSopir1 > 0 && valKuli1 > 0;
                // let tahap1Siap2 = valSopir2 > 0 && valKuli2 > 0 && valSopir1 > 0 && valKuli1 > 0;

                // $("#nominal_sangu2").prop("readonly", !tahap1Siap || valSopir2 > 0);
                // $("#nominal_sangu_kuli2").prop("readonly", !tahap1Siap || valKuli2 > 0);
                // $("#nominal_sangu3").prop("readonly", !tahap1Siap2 || valSopir3 > 0);
                // $("#nominal_sangu_kuli3").prop("readonly", !tahap1Siap2 || valKuli3 > 0);

                // if (valSopir3 > 0 && valKuli3 > 0) {
                //     alert(
                //         "Anda sudah menambah Sangu Sopir dan Sangu Kuli sebanyak 3 kali. Jika ada perubahan, silahkan hubungi admin.");
                //     $("#simpan_sangu").prop("hidden", true);
                // }


            }

            // Fungsi: sembunyikan nominal_sangu2 jika nominal_sangu1 > 0
            

            // Unbind dan bind ulang event input
            // $("#nominal_sangu1").off("input").on("input", function() {
            //     toggleNominalSangu2(); // ini tetap boleh jalan saat input
            // });

            // // Kunci readonly setelah selesai input
            // $("#nominal_sangu1").off("blur").on("blur", function() {
            //     toggleReadonlyFields();
            // });



            // // Jalankan fungsi saat modal dibuka
            // toggleNominalSangu2();
            // toggleReadonlyFields();

            // Tampilkan modal
            $("#modalSanguKuli").modal("show");
        });

        // Klik baris Grid 2
        $("#jqGrid1").on("dblclick", "tr.jqgrow", function() {
            let rowId = $(this).attr("id");
            let rowData = $("#jqGrid1").jqGrid('getRowData', rowId);
            $("#rowId1").val(rowId);
            $("#sopir_tbtl").val(rowData.sopir);
            $("#tgl_tb_tl").val(rowData.tgl_tb_tl);
            $("#tgl_tb_tl1").val(rowData.tgl_tb_tl1);
            $("#nopol_tbtl").val(rowData.nopol);
            $("#nominal_tb_tl1").val(rowData.nominal_tb_tl1);
            $("#nominal_tb_tl2").val(rowData.nominal_tb_tl2);
            function toggleReadonlyFields() {
                let valTbtl1 = parseFloat($("#nominal_tb_tl1").val().replace(/,/g, '')) || 0;
                // $("#nominal_tb_tl").prop("readonly", valTbtl > 0);
                // $("#nominal_tb_tl1").prop("readonly", valTbtl1 > 0);
                // if (valTbtl1 > 0) {
                //     alert(
                //         "Anda sudah menambah TB/TL. Jika ada perubahan, silahkan hubungi admin.");
                //     $("#simpan_tb_tl").prop("hidden", true);
                // }
            }
            toggleReadonlyFields();
            $("#modalTBTL").modal('show');
        });

        // Klik baris Grid 3
        $("#jqGrid2").on("dblclick", "tr.jqgrow", function() {
            let rowId = $(this).attr("id");
            let rowData = $("#jqGrid2").jqGrid('getRowData', rowId);
            $("#rowId2").val(rowId);
            $("#sopir_stappel").val(rowData.sopir);
            $("#tgl_stappel").val(rowData.tgl_stappel);
            $("#nopol_stappel").val(rowData.nopol);
            $("#nominal_stappel1").val(rowData.nominal_stappel1);
            function toggleReadonlyFields() {
                let valStappel1 = parseFloat($("#nominal_stappel1").val().replace(/,/g, '')) || 0;
                // $("#nominal_stappel1").prop("readonly", valStappel1 > 0);
                // if (valStappel1 > 0) {
                //     alert(
                //         "Anda sudah menambah Stappel. Jika ada perubahan, silahkan hubungi admin.");
                //     $("#simpan_stappel").prop("hidden", true);
                // }
            }
            toggleReadonlyFields();
            $("#modalStappel").modal('show');
        });
        $('#formSanguKuli').submit(function(e) {
            e.preventDefault();

            const token = $('meta[name="csrf-token"]').attr('content');

            const formData = {
                id: $('#rowId').val(),
                tgl_sangu_kuli1: $('#tgl_sangu_kuli1').val(),
                tgl_sangu_kuli2: $('#tgl_sangu_kuli2').val(),
                tgl_sangu_kuli3: $('#tgl_sangu_kuli3').val(),
                nominal_sangu_kuli1: $('#nominal_sangu_kuli1').val(),
                nominal_sangu_kuli2: $('#nominal_sangu_kuli2').val(),
                nominal_sangu_kuli3: $('#nominal_sangu_kuli3').val()
            };
            console.log(formData);

            $.ajax({
                url: '{{ route('monitoringBiayaTruck.update') }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function(response) {
                    $('#modalSanguKuli').modal('hide');
                    $("#jqGrid").trigger('reloadGrid');
                    alert("Data Bershasil disimpan");
                    // toastr.success atau alert bisa ditambahkan di sini
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan saat menyimpan data.");
                }
            });
        });
        $('#formTBTL').submit(function(e) {
            e.preventDefault();

            const token = $('meta[name="csrf-token"]').attr('content');

            const formData = {
                id: $('#rowId1').val(),
                tgl_tb_tl: $('#tgl_tb_tl').val(),
                tgl_tb_tl1: $('#tgl_tb_tl1').val(),
                nominal_tb_tl1: $('#nominal_tb_tl1').val(),
                nominal_tb_tl2: $('#nominal_tb_tl2').val(),
            };

            $.ajax({
                url: '{{ route('monitoringBiayaTruck.update1') }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function(response) {
                    $('#modalTBTL').modal('hide');
                    $("#jqGrid1").trigger('reloadGrid');
                    alert("Data Bershasil disimpan");
                    // toastr.success atau alert bisa ditambahkan di sini
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan saat menyimpan data.");
                }
            });
        });

        $('#formStappel').submit(function(e) {
            e.preventDefault();

            const token = $('meta[name="csrf-token"]').attr('content');

            const formData = {
                id: $('#rowId2').val(),
                tgl_stappel: $('#tgl_stappel').val(),
                nominal_stappel1: $('#nominal_stappel1').val(),
            };
            $.ajax({
                url: '{{ route('monitoringBiayaTruck.update2') }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function(response) {
                    $('#modalStappel').modal('hide');
                    $("#jqGrid2").trigger('reloadGrid');
                    alert("Data Bershasil disimpan");
                    // toastr.success atau alert bisa ditambahkan di sini
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan saat menyimpan data.");
                }
            });
        });
    </script>
@endsection
