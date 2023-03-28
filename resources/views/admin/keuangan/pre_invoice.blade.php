@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<style>
    table.dataTable tbody th, table.dataTable tbody td{
        padding: 0px 10px !important;
    }
    .select2.select2-container.select2-container--default{
        width: 100% !important;
    }
    thead input {
        width: 100%;
    }
    .autocomplete {
        position: relative;
        display: inline-block;
    }
    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }
    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }
    .autocomplete-items div:hover {
        /*when hovering an item:*/
        background-color: #e9e9e9;
    }
    .autocomplete-active {
        /*when navigating through the items using the arrow keys:*/
        background-color: DodgerBlue !important;
        color: #ffffff;
    }
    .dataTables_scrollBody > table > thead > tr {
        visibility: collapse;
        height: 0px !important;
    }
</style>
@endsection
@section('content')

    <div class="container mt-3">
        <a href="{{ route('keuangan.pre_invoice1') }}">Lihat Pre Invoice Versi 1 tabel</a>
        <div class="card mt-3">
            <div class="card-header py-2 px-5 d-flex justify-content-between" style="gap:10px">
                <div class="card-titles">List Pre Invoice (Read Only)</div>
                <div class="d-flex gap-2">
                    <a href="" class="btn btn-sm btn-success" id="cetak-invoice"><i class="fas fa-print"></i> Cetak Invoice Global</a>
                    <a href="" class="btn btn-sm btn-success" id="cetak-cont-invoice"><i class="fas fa-print"></i> Cetak Invoice Per Cont</a>
                </div>
                <p>No JOB: <span class="nojob"></span></p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" id="table-order" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>Tools</th>
                                <th>ID.</th>
                                <th>Tanggal</th>
                                <th>Invoice</th>
                                <th>Group JOB</th>
                                <th>ID JOB</th>
                                <th>Asuransi</th>
                                <th>Pembayar</th>
                                <th>Marketing</th>
                                <th>CS</th>
                                <th>Pengirim</th>
                                <th>Penerima</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Shipment</th>
                                <th>Kondisi</th>
                                <th>Jenis Barang</th>
                                <th>Barang</th>
                                <th>Pelayaran</th>
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>ETD</th>
                                <th>TD</th>
                                <th>BA Kirim</th>
                                <th>Nopol</th>
                                <th>Trucking</th>
                                <th>No Container</th>
                                <th>No Seal</th>
                                <th>Stuffing</th>
                                <th>Tipe Stuffing</th>
                                <th>Tgl Full</th>
                                <th>Barang Diantar</th>
                                <th>BA Kembali</th>
                                <th>Koli</th>
                                <th>M3</th>
                                <th>Berat</th>
                                <th>Satuan</th>
                                <th>Unit</th>
                                <th>Tarif</th>
                                <th>Agen</th>
                                <th>Penerima BL</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header py-2 px-5 d-flex justify-content-between" style="gap:10px">
                <div class="card-titles">List Pre Invoice Tidak Perlu BA Kembali(Read Only)</div>
                <div class="d-flex gap-2">
                    <a href="" class="btn btn-sm btn-success" id="cetak-invoice2"><i class="fas fa-print"></i> Cetak Invoice Global</a>
                    <a href="" class="btn btn-sm btn-success" id="cetak-cont-invoice2"><i class="fas fa-print"></i> Cetak Invoice Per Cont</a>
                </div>
                <p>No JOB: <span class="nojob2"></span></p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" id="table-order2" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>Tools</th>
                                <th>ID.</th>
                                <th>Tanggal</th>
                                <th>Invoice</th>
                                <th>Group JOB</th>
                                <th>ID JOB</th>
                                <th>Asuransi</th>
                                <th>Pembayar</th>
                                <th>Marketing</th>
                                <th>CS</th>
                                <th>Pengirim</th>
                                <th>Penerima</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Shipment</th>
                                <th>Kondisi</th>
                                <th>Jenis Barang</th>
                                <th>Barang</th>
                                <th>Pelayaran</th>
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>ETD</th>
                                <th>TD</th>
                                <th>BA Kirim</th>
                                <th>Nopol</th>
                                <th>Trucking</th>
                                <th>No Container</th>
                                <th>No Seal</th>
                                <th>Stuffing</th>
                                <th>Tipe Stuffing</th>
                                <th>Tgl Full</th>
                                <th>Barang Diantar</th>
                                <th>BA Kembali</th>
                                <th>Koli</th>
                                <th>M3</th>
                                <th>Berat</th>
                                <th>Satuan</th>
                                <th>Unit</th>
                                <th>Tarif</th>
                                <th>Agen</th>
                                <th>Penerima BL</th>
                                <th>Keterangan</th>
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

@endsection

@section('script')
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script>
        // $('#table-order thead tr')
        //     .clone(true)
        //     .addClass('filters')
        //     .appendTo('#table-order thead');
        // $('#table-order2 thead tr')
        //     .clone(true)
        //     .addClass('filters')
        //     .appendTo('#table-order2 thead');

        let tableOrder = $('#table-order').DataTable({
            processing: true,
            serverSide: true,
            scrollY:        200,
            deferRender:    true,
            scroller:       true,
            select:true,
            scrollX:true,
            ordering:false,
            ajax:{
                url: '{{ route('order.data') }}',
                method:'POST',
                data:{filter:'pre_invoice'},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                // {data: '#', name:'search', orderable: false, searchable: false },
                // { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'tools', name: 'tools', orderable: false, searchable: false, visible:false },
                { data: 'id', name: 'id', visible:false },
                { data: 'created_at', name: 'created_at' },
                { data: 'invoice', name: 'order.invoice' },
                { data: 'job', name: 'order.job' },
                { data: 'no_job', name: 'no_job', searchable:false },
                { data: 'asuransi', name: 'order.asuransi' },
                { data: 'pembayar', name: 'pembayar.nama' },
                { data: 'marketing', name: 'name', searchable:false },
                { data: 'cs', name: 'name', searchable:false },
                { data: 'pengirim', name: 'pengirim.nama' },
                { data: 'penerima', name: 'penerima.nama' },
                { data: 'dari', name: 'tarif.dari' },
                { data: 'tujuan', name: 'tarif.tujuan' },
                { data: 'shipment', name: 'shipments.nama' },
                { data: 'kondisi', name: 'kondisi.nama' },
                { data: 'barang', name: 'barang.nama' },
                { data: 'barang_bttb', name: 'barang_bttb', searchable:false },
                { data: 'pelayaran', name: 'pelayaran.nama' },
                { data: 'kapal', name: 'kapal.nama' },
                { data: 'voyage', name: 'jadwal_kapal.voyage' },
                { data: 'etd', name: 'jadwal_kapal.etd' },
                { data: 'td', name: 'jadwal_kapal.td' },
                { data: 'ba_kirim', name: 'order.ba_kirim' },
                { data: 'nopol', name: 'order.nopol' },
                { data: 'trucking', name: 'order.trucking' },
                { data: 'container', name: 'order.container' },
                { data: 'seal', name: 'order.seal' },
                { data: 'stuffing', name: 'order.stuffing' },
                { data: 'stuffing_t', name: 'tarif.stuffing' },
                { data: 'full', name: 'order.full' },
                { data: 'barang_diantar', name: 'order.barang_diantar' },
                { data: 'ba_kembali', name: 'order.ba_kembali' },
                { data: 'koli', name: 'koli', searchable:false },
                { data: 'vol', name: 'vol', searchable:false },
                { data: 'berat', name: 'berat', searchable:false },
                { data: 'satuan', name: 'satuan', searchable:false },
                { data: 'unit', name: 'satuan.nama' },
                { data: 'tarif', name: 'tarif.tarif' },
                { data: 'agen', name: 'order.agen' },
                { data: 'penerima_bl', name: 'penerima_bl.nama' },
                { data: 'keterangan', name: 'order.keterangan' },
            ],
            // initComplete: function () {
            //     var api = this.api();

            //     // For each column
            //     api
            //         .columns()
            //         .eq(0)
            //         .each(function (colIdx) {
            //             // Set the header cell to contain the input element
            //             var cell = $('.filters th').eq(
            //                 $(api.column(colIdx).header()).index()
            //             );
            //             var title = $(cell).text();
            //             $(cell).html('<input type="text" placeholder="' + title + '" />');

            //             // On every keypress in this input
            //             $(
            //                 'input',
            //                 $('.filters th').eq($(api.column(colIdx).header()).index())
            //             )
            //                 .off('keyup change')
            //                 .on('change', function (e) {
            //                     // Get the search value
            //                     $(this).attr('title', $(this).val());
            //                     var regexr = '({search})'; //$(this).parents('th').find('select').val();

            //                     var cursorPosition = this.selectionStart;
            //                     // Search the column for that value
            //                     api
            //                         .column(colIdx)
            //                         .search(
            //                             this.value != ''
            //                                 ? regexr.replace('{search}', '(((' + this.value + ')))')
            //                                 : '',
            //                             this.value != '',
            //                             this.value == ''
            //                         )
            //                         .draw();
            //                 })
            //                 .on('keyup', function (e) {
            //                     e.stopPropagation();

            //                     $(this).trigger('change');
            //                     $(this)
            //                         .focus()[0]
            //                         // .setSelectionRange(cursorPosition, cursorPosition);
            //                 });
            //         });
            // },

        });
        let tableOrder2 = $('#table-order2').DataTable({
            processing: true,
            serverSide: true,
            scrollY:        200,
            deferRender:    true,
            scroller:       true,
            select:true,
            scrollX:true,
            ordering:false,
            ajax:{
                url: '{{ route('order.data') }}',
                method:'POST',
                data:{filter:'pre_invoice2'},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                // {data: '#', name:'search', orderable: false, searchable: false },
                // { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'tools', name: 'tools', orderable: false, searchable: false, visible:false },
                { data: 'id', name: 'id', visible:false },
                { data: 'created_at', name: 'created_at' },
                { data: 'invoice', name: 'order.invoice' },
                { data: 'job', name: 'order.job' },
                { data: 'no_job', name: 'no_job', searchable:false },
                { data: 'asuransi', name: 'order.asuransi' },
                { data: 'pembayar', name: 'pembayar.nama' },
                { data: 'marketing', name: 'name', searchable:false },
                { data: 'cs', name: 'name', searchable:false },
                { data: 'pengirim', name: 'pengirim.nama' },
                { data: 'penerima', name: 'penerima.nama' },
                { data: 'dari', name: 'tarif.dari' },
                { data: 'tujuan', name: 'tarif.tujuan' },
                { data: 'shipment', name: 'shipments.nama' },
                { data: 'kondisi', name: 'kondisi.nama' },
                { data: 'barang', name: 'barang.nama' },
                { data: 'barang_bttb', name: 'barang_bttb', searchable:false },
                { data: 'pelayaran', name: 'pelayaran.nama' },
                { data: 'kapal', name: 'kapal.nama' },
                { data: 'voyage', name: 'jadwal_kapal.voyage' },
                { data: 'etd', name: 'jadwal_kapal.etd' },
                { data: 'td', name: 'jadwal_kapal.td' },
                { data: 'ba_kirim', name: 'order.ba_kirim' },
                { data: 'nopol', name: 'order.nopol' },
                { data: 'trucking', name: 'order.trucking' },
                { data: 'container', name: 'order.container' },
                { data: 'seal', name: 'order.seal' },
                { data: 'stuffing', name: 'order.stuffing' },
                { data: 'stuffing_t', name: 'tarif.stuffing' },
                { data: 'full', name: 'order.full' },
                { data: 'barang_diantar', name: 'order.barang_diantar' },
                { data: 'ba_kembali', name: 'order.ba_kembali' },
                { data: 'koli', name: 'koli', searchable:false },
                { data: 'vol', name: 'vol', searchable:false },
                { data: 'berat', name: 'berat', searchable:false },
                { data: 'satuan', name: 'satuan', searchable:false },
                { data: 'unit', name: 'satuan.nama' },
                { data: 'tarif', name: 'tarif.tarif' },
                { data: 'agen', name: 'order.agen' },
                { data: 'penerima_bl', name: 'penerima_bl.nama' },
                { data: 'keterangan', name: 'order.keterangan' },
            ],
            // initComplete: function () {
            //     var api = this.api();

            //     // For each column
            //     api
            //         .columns()
            //         .eq(0)
            //         .each(function (colIdx) {
            //             // Set the header cell to contain the input element
            //             var cell = $('.filters th').eq(
            //                 $(api.column(colIdx).header()).index()
            //             );
            //             var title = $(cell).text();
            //             $(cell).html('<input type="text" placeholder="' + title + '" />');

            //             // On every keypress in this input
            //             $(
            //                 'input',
            //                 $('.filters th').eq($(api.column(colIdx).header()).index())
            //             )
            //                 .off('keyup change')
            //                 .on('change', function (e) {
            //                     // Get the search value
            //                     $(this).attr('title', $(this).val());
            //                     var regexr = '({search})'; //$(this).parents('th').find('select').val();

            //                     var cursorPosition = this.selectionStart;
            //                     // Search the column for that value
            //                     api
            //                         .column(colIdx)
            //                         .search(
            //                             this.value != ''
            //                                 ? regexr.replace('{search}', '(((' + this.value + ')))')
            //                                 : '',
            //                             this.value != '',
            //                             this.value == ''
            //                         )
            //                         .draw();
            //                 })
            //                 .on('keyup', function (e) {
            //                     e.stopPropagation();

            //                     $(this).trigger('change');
            //                     $(this)
            //                         .focus()[0]
            //                         // .setSelectionRange(cursorPosition, cursorPosition);
            //                 });
            //         });
            // },

        });

        $('#table-order tbody').on( 'click', 'tr', function () {
            id =  tableOrder.row( this ).data().id;
            var no_job =  tableOrder.row( this ).data().no_job;
            $('.nojob').html(no_job);
            $('#cetak-invoice').attr('href','{{ route('cetak.invoice') }}?order_id='+id);
            $('#cetak-cont-invoice').attr('href','{{ route('cetak.invoice.cont') }}?order_id='+id);
        })

        $('#table-order2 tbody').on( 'click', 'tr', function () {
            id =  tableOrder2.row( this ).data().id;
            var no_job =  tableOrder2.row( this ).data().no_job;
            $('.nojob2').html(no_job);
            $('#cetak-invoice2').attr('href','{{ route('cetak.invoice') }}?order_id='+id);
            $('#cetak-cont-invoice2').attr('href','{{ route('cetak.invoice.cont') }}?order_id='+id);
        })
</script>
@endsection
