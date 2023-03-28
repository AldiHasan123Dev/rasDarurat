@extends('layouts.admin')
@section('style')
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<style>
    td, th {
        border: 1px solid #ccc;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card p-3">
                <div class="card-header">
                    <div class="d-flex gap-5">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#faktur">Tambah Faktur</button>
                        <form action="{{ route('keuangan.ppn.export') }}" method="post">
                            @csrf
                            <input type="hidden" name="start" value="{{ $start }}">
                            <input type="hidden" name="end" value="{{ $end }}">
                            <button type="submit" class="btn btn-success btn-sm">Export Excel</button>
                        </form>
                        <form method="get" action="{{ url()->current() }}" class="d-flex gap-3">
                            <div class="btn-group">
                                <input type="date" name="start" id="start" value="{{ $start }}" class="form-control">
                                <button disabled style="width: 70px" style="border:none; outline:none;"><i class="fas fa-arrow-right"></i></button>
                                <input type="date" name="end" id="end" value="{{ $end }}" class="form-control">
                                <button class="btn btn-sm btn-primary">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsives mt-3">
                    {{-- <table class="w-100" id="table-ppn" style="font-size: .7rem">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th>Invoice</th>
                                <th>NPWP</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Nama NPWP</th>
                                <th style="width:10px">Alamat NPWP</th>
                                <th>Tanggal Faktur</th>
                                <th>Tujuan</th>
                                <th>Uraian</th>
                                <th>Daftar Faktur Pajak</th>
                                <th>Sub Total</th>
                                <th>PPN</th>
                                <th>Total</th>
                                <th>PPH</th>
                                <th>No.JOB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksi as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->invoice }}</td>
                                    <td>{{ $item->pembayar->npwp }}</td>
                                    <td>{{ $item->pembayar->nik }}</td>
                                    <td>{{ $item->pembayar->nama }}</td>
                                    <td>{{ $item->pembayar->nama_npwp }}</td>
                                    <td>{{ Str::limit($item->pembayar->alamat_npwp, 30, '...') }}</td>
                                    <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                                    <td>{{ $item->tujuan }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->nsfp }}</td>
                                    <td>{{ number_format(ceil($item->sub_total)) }}</td>
                                    <td>{{ number_format($item->ppn) }}</td>
                                    <td>{{ number_format(ceil($item->ppn + $item->sub_total)) }}</td>
                                    <td>{{ number_format($item->pph) }}</td>
                                    <td>{{ $item->no_job() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table> --}}
                    <table id="jqGrid"></table>
                    <div id="jqGridPager"></div>
                </div>
                <div class="card-footer py-2">
                    <div class="d-flex gap-3 mt-2 justify-content-center">
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total Sub Total</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format($sub_total,2,',','.') }}</li>
                        </ul>
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total PPN</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format($ppn,2,',','.') }}</li>
                        </ul>
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format($sub_total+$ppn,2,',','.') }}</li>
                        </ul>
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total PPH</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format($pph,2,',','.') }}</li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="faktur" tabindex="-1" aria-labelledby="fakturLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fakturLabel">Tambah Faktur Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div class="mb-2 col-6">
                    <label for="nsfp">Nomor Faktur</label>
                    <input type="text" name="nsfp" id="nsfp" class="form-control" value="{{ $no }}" required readonly>
                </div>
                <div class="mb-2 col-6">
                    <label for="invoice">Invoice</label>
                    <input type="text" name="invoice" id="invoice" class="form-control" required>
                </div>
                <div class="mb-2 col-6 autocomplete">
                    <label for="pembayar_id">Pembayar</label>
                    <input type="text" name="pembayar_id" id="pembayar_id" class="form-control" required>
                </div>
                <div class="mb-2 col-6 autocomplete">
                    <label for="tujuan">Tujuan</label>
                    <input type="text" name="tujuan" id="tujuan" class="form-control" required>
                </div>
                <div class="mb-2 col-12">
                    <label for="keterangan">Uraian</label>
                    <input type="text" name="keterangan" id="keterangan" class="form-control" required>
                </div>
                <div class="mb-2 col-6">
                    <label for="sub_total">Sub Total</label>
                    <input type="text" name="sub_total" id="sub_total" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-6">
                    <label for="ppn">PPN</label>
                    <input type="text" name="ppn" id="ppn" class="form-control rupiah" required>
                </div>
                <div class="mb-2 col-6">
                    <label for="total">Total</label>
                    <input type="text" name="total" id="total" class="form-control" required readonly>
                </div>
                <div class="mb-2 col-6">
                    <label for="pph">PPH</label>
                    <input type="text" name="pph" id="pph" class="form-control rupiah" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="create-nsfp" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
<script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
<script src="{{asset('assets/js/autocomplete.js')}}"></script>
<script>
    $(function() {
        var customers = @json($customers);
        var lokasi = @json($lokasi);
        autocomplete(document.getElementById("pembayar_id"), customers);
        autocomplete(document.getElementById("tujuan"), lokasi);
    });
</script>
    <script>
        var data = @json($data);

        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: data,
            colModel: [
                {search:true, name: 'invoice', label : 'Invoice'},
                {search:true, name: 'npwp', label : 'NPWP'},
                {search:true, name: 'nik', label : 'NIK', sorttype: "int"},
                {search:true, name: 'nama', label : 'Nama'},
                {search:true, name: 'nama_npwp', label : 'Nama NPWP'},
                {search:true, name: 'alamat_npwp', label : 'Alamat NPWP'},
                {search:true, name: 'tanggal', label : 'Tanggal Faktur', sorttype: 'date', datefmt:'d/m/Y'},
                {search:true, name: 'tujuan', label : 'Tujuan'},
                {search:true, name: 'uraian', label : 'Uraian'},
                {search:true, name: 'daftar_faktur_pajak', label : 'Faktur'},
                {search:true, name: 'sub_total', label : 'Sub Total'},
                {search:true, name: 'ppn', label : 'PPN'},
                {search:true, name: 'ppn_subtotal', label : 'Total'},
                {search:true, name: 'pph', label : 'PPH'},
                {search:true, name: 'no_job', label : 'JOB'},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100],
			viewrecords: true,
            pager: "#jqGridPager",
            caption: "Laporan PPN"
        });

        $('#jqGrid').jqGrid('filterToolbar',{stringResult: true, searchOnEnter: false, defaultSearch: 'cn'});
			$('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
                search: false, // show search button on the toolbar
                add: false,
                edit: false,
                del: false,
                refresh: true
            });

        $('#sub_total').keyup(function (e) {
            hitung();
        });

        $('#ppn').keyup(function (e) {
            hitung();
        });

        function hitung (){
            var sub_total = $('#sub_total').val().replace(/\./g, "");
            var ppn = $('#ppn').val().replace(/\./g, "");
            var total = parseInt(sub_total) + parseInt(ppn);
            $('#total').val(total.toLocaleString('en-US'));
        }

        $('#create-nsfp').click(function (e) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.nsfp.store') }}",
                data: {
                    nsfp:$('#nsfp').val(),
                    invoice:$('#invoice').val(),
                    pembayar_id:$('#pembayar_id').val(),
                    tujuan:$('#tujuan').val(),
                    keterangan:$('#keterangan').val(),
                    sub_total:$('#sub_total').val(),
                    ppn:$('#ppn').val(),
                    total:$('#total').val(),
                    pph:$('#pph').val(),
                },
                success: function (response) {
                    if(!response){
                        alert('Pembayar Tidak Ditemukan')
                    }else{
                        location.reload();
                    };
                }
            });
        });
    </script>
@endsection
