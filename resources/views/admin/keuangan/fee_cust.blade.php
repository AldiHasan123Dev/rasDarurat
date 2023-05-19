@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
    <style>
        td:hover {
            cursor: pointer;
        }
        table.dataTable tbody th, table.dataTable tbody td{
            padding: 0px 10px !important;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAsuransi" aria-controls="offcanvasAsuransi">Input Tanggal</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm no-wrap nowrap" id="table-data" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID JOB</th>
                                <th>Pembayar</th>
                                <th>Pelayaran</th>
                                <th>Shippment</th>
                                <th>No Cont</th>
                                <th>Seal</th>
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>Fee</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->whereNull('tgl_komisi') as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }}</td>
                                    <td>{{ $item->tarif->customer->nama ?? '-' }}</td>
                                    <td>{{ $item->jadwal_kapal->pelayaran->nama ?? '-' }}</td>
                                    <td>{{ $item->tarif->shipmentInfo->nama ?? '-' }}</td>
                                    <td>{{ $item->container }}</td>
                                    <td>{{ $item->seal }}</td>
                                    <td>{{ $item->jadwal_kapal->kapal->nama ?? '-' }}</td>
                                    <td>{{ $item->jadwal_kapal->voyage ?? '-' }}</td>
                                    <td>{{ number_format($item->komisi) }}</td>
                                    <td>{{ is_null($item->tgl_komisi) ? '-' : date('d/m/y',strtotime($item->tgl_komisi)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="m-3">
                <span>List Sudah Terbit Tanggal</span>
                <hr>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm no-wrap nowrap" id="table-print" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID JOB</th>
                                <th>Pembayar</th>
                                <th>Pelayaran</th>
                                <th>Shippment</th>
                                <th>No Cont</th>
                                <th>Seal</th>
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>Fee</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->whereNotNull('tgl_komisi') as $item)
                                <tr>
                                    <td>{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }}</td>
                                    <td>{{ $item->tarif->customer->nama ?? '-' }}</td>
                                    <td>{{ $item->jadwal_kapal->pelayaran->nama ?? '-' }}</td>
                                    <td>{{ $item->tarif->shipmentInfo->nama ?? '-' }}</td>
                                    <td>{{ $item->container }}</td>
                                    <td>{{ $item->seal }}</td>
                                    <td>{{ $item->jadwal_kapal->kapal->nama ?? '-' }}</td>
                                    <td>{{ $item->jadwal_kapal->voyage ?? '-' }}</td>
                                    <td>{{ number_format($item->komisi) }}</td>
                                    <td>{{ is_null($item->tgl_komisi) ? '-' : date('d/m/y',strtotime($item->tgl_komisi)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasAsuransi" aria-labelledby="offcanvasAsuransiLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasAsuransiLabel">Input Tanggal</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="#" id="form-update" method="post">
                @csrf
                @method('PUT')
                <div class="mb-2">
                    <label for="id_job">ID JOB</label>
                    <input type="text" disabled name="id_job" id="id_job" class="form-control">
                </div>
                <div class="mb-2">
                    <label for="tgl_komisi">Tanggal Komisi</label>
                    <input type="date" name="tgl_komisi" id="tgl_komisi" class="form-control">
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
    <script>
        let table = $('#table-print').DataTable({
            scrollX : true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'print',
                    title:'',
                    customize: function(win) {
                        $(win.document.body)
                        .css('font-size', '.7rem');

                        $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '.7rem');
                    },
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                'colvis'
            ],columnDefs: [
                {
                    target: 0,
                    visible: false,
                    searchable: false,
                },
            ],
        });
        let table1 = $('#table-data').DataTable({
            scrollX : true,
            select:true,
            columnDefs: [
                {
                    target: 0,
                    visible: false,
                    searchable: false,
                },
            ],
        });

        $('#table-data tbody').on( 'click', 'tr', function () {
            let data =  table1.row( this ).data();
            $('#id_job').val(data[1]);
            let url = @json(url('admin/order'))+'/'+data[0];
            $('#form-update').attr('action',url);
        });
    </script>
@endsection
