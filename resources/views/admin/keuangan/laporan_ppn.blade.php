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
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#faktur">Tambah Faktur</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#bukpot">Bukpot</button>
                        <form action="{{ route('keuangan.ppn.export') }}" method="post">
                            @csrf
                            <input type="hidden" name="start" value="{{ $start }}">
                            <input type="hidden" name="end" value="{{ $end }}">
                            <button type="submit" class="btn btn-success btn-sm">Export Excel</button>
                        </form>
                        <form action="{{ route('keuangan.pajak.export') }}" method="post">
                            @csrf
                            <input type="hidden" name="start" value="{{ $start }}">
                            <input type="hidden" name="end" value="{{ $end }}">
                            <button type="submit" class="btn btn-success btn-sm">Export CSV</button>
                        </form>
                          <form action="{{ route('keuangan.xml.export') }}" method="post">
                            @csrf
                            <input type="hidden" name="start" value="{{ $start }}">
                            <input type="hidden" name="end" value="{{ $end }}">
                            <button type="submit" class="btn btn-success btn-sm">Export Bahan XML</button>
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
                            <li class="list-group-item fw-bold">Rp. {{ number_format($sub_total) }}</li>
                        </ul>
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total PPN</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format(round($ppn)) }}</li>
                        </ul>
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format($sub_total+ round($ppn)) }}</li>
                        </ul>
                        <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                            <li class="list-group-item fw-bold">Total PPH</li>
                            <li class="list-group-item fw-bold">Rp. {{ number_format(round($pph)) }}</li>
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
                <div class="mb-2 col-6 autocomplete">
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

<div class="modal fade" id="bukpot" tabindex="-1" aria-labelledby="bukpotLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bukpotLabel">Data Bukpot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <input type="hidden" name="id" id="id">
                <div class="mb-2 col-12">
                    <label for="no_job">JOB</label>
                    <input type="text" id="no_job" class="form-control" required disabled>
                </div>
                <div class="mb-2 col-12">
                    <label for="pph_23">PPH 23</label>
                    <input type="text" id="pph_23" class="form-control" required disabled>
                </div>
                <div class="mb-2 col-12">
                    <label for="bupot">Bupot</label>
                    <input type="number" step="any" name="bupot" id="bupot" class="form-control">
                </div>
                <div class="mb-2 col-12">
                    <label for="selisih_bupot">Selisih Bupot</label>
                    <input type="text" name="selisih_bupot" id="selisih_bupot" class="form-control" required readonly>
                </div>
                <div class="mb-2 col-12">
                    <label for="no_bupot">No. Bupot</label>
                    <input type="text" name="no_bupot" id="no_bupot" class="form-control">
                </div>
                <div class="mb-2 col-6">
                    <label for="masa_bupot_bulan">Masa Bupot</label>
                    <select name="masa_bupot_bulan" id="masa_bupot_bulan" class="form-control">
                        <option value="JANUARI" selected>JANUARI</option>
                        <option value="FEBRUARI">FEBRUARI</option>
                        <option value="MARET">MARET</option>
                        <option value="APRIL">APRIL</option>
                        <option value="MEI">MEI</option>
                        <option value="JUNI">JUNI</option>
                        <option value="JULY">JULY</option>
                        <option value="AGUSTUS">AGUSTUS</option>
                        <option value="SEPTEMBER">SEPTEMBER</option>
                        <option value="OKTOBER">OKTOBER</option>
                        <option value="NOVEMBER">NOVEMBER</option>
                        <option value="DESEMBER">DESEMBER</option>
                    </select>
                </div>
                <div class="mb-2 col-6">
                    <label for="masa_bupot_tahun"></label>
                    <select name="masa_bupot_tahun" id="masa_bupot_tahun" class="form-control">
                        <option value="{{ date('Y') - 2 }}">{{ date('Y') - 2 }}</option>
                        <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                        <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                        <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                        <option value="{{ date('Y') + 2 }}">{{ date('Y') + 2 }}</option>
                    </select>
                </div>
                <div class="mb-2 col-12">
                    <label for="tanggal_bukpot">Tanggal Bupot</label>
                    <input type="date" name="tanggal_bupot" id="tanggal_bupot" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="add-bupot" class="btn btn-primary">Simpan</button>
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
        var invoices = @json($invoices);
        var lokasi = @json($lokasi);
        autocomplete(document.getElementById("pembayar_id"), customers);
        autocomplete(document.getElementById("invoice"), invoices);
        autocomplete(document.getElementById("tujuan"), lokasi);
    });
</script>
    <script>
        var data = @json($data);
        let id;
        let ppn;
        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: data,
            colModel: [
                {search:true, name: 'id', label : 'id', hidden: true},
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
                {search:true, name: 'no_bupot', label : 'No Bupot'},
                {search:true, name: 'masa_bupot', label : 'Masa Pajak'},
                {search:true, name: 'bupot', label : 'Bupot'},
                {search:true, name: 'bupot_nominal', label : 'Bupot', hidden:true},
                {search:true, name: 'masa_bupot_tahun', label : 'Bupot', hidden:true},
                {search:true, name: 'masa_bupot_bulan', label : 'Bupot', hidden:true},
                {search:true, name: 'tanggal_bupot_date', label : 'Bupot', hidden:true},
                {search:true, name: 'tanggal_bupot', label : 'Tanggal Bupot'},
                {search:true, name: 'selisih_bupot', label : 'Selisih Bupot'},
                {search:true, name: 'jurnal_bupot', label : 'Jurnal Bupot'},
            ],
            autowidth: true,
            shrinkToFit: false,
            height: 250,
            oadonce: true,
            rowNum: 25,
            rowList:[10,25,50,100],
			viewrecords: true,
            pager: "#jqGridPager",
            caption: "Laporan PPN",
            onCellSelect: function (rowId, iRow, iCol, e) {
                id = $(this).jqGrid('getCell', rowId, 'id');
                ppn = $(this).jqGrid('getCell', rowId, 'pph');
                let no_job = $(this).jqGrid('getCell', rowId, 'no_job');
                let no_bupot = $(this).jqGrid('getCell', rowId, 'no_bupot');
                let masa_bupot_tahun = $(this).jqGrid('getCell', rowId, 'masa_bupot_tahun');
                let masa_bupot_bulan = $(this).jqGrid('getCell', rowId, 'masa_bupot_bulan');
                let tanggal_bupot_date = $(this).jqGrid('getCell', rowId, 'tanggal_bupot_date');
                let bupot = $(this).jqGrid('getCell', rowId, 'bupot_nominal');
                let selisih_bupot = $(this).jqGrid('getCell', rowId, 'selisih_bupot');
                $('#no_job').val(no_job);
                $('#no_bupot').val(no_bupot);
                $('#bupot').val(bupot);
                $('#selisih_bupot').val(selisih_bupot);
                $('#pph_23').val(ppn);
                $('#masa_bupot_tahun').val(masa_bupot_tahun);
                $('#masa_bupot_bulan').val(masa_bupot_bulan);
                $('#tanggal_bupot').val(tanggal_bupot_date);
            },
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

        $('#add-bupot').click(function (e) {
    e.preventDefault();

    let $btn = $(this);
    let originalText = $btn.text();

    // Ubah tombol menjadi loading
    $btn.prop('disabled', true).text('Memproses...');

    $.ajax({
        type: "POST",
        url: "{{ route('api.transaksi.update.bupot') }}",
        data: {
            id: id,
            bupot: $('#bupot').val(),
            no_bupot: $('#no_bupot').val(),
            masa_bupot_bulan: $('#masa_bupot_bulan').val(),
            masa_bupot_tahun: $('#masa_bupot_tahun').val(),
            selisih_bupot: $('#selisih_bupot').val(),
            tanggal_bupot: $('#tanggal_bupot').val(),
        },
        success: function (response) {
            if (!response) {
                alert('Data Tidak Ditemukan');
                $btn.prop('disabled', false).text(originalText);
            } else {
                alert('Data Bukpot berhasil disimpan!');
                location.reload();
            }
        },
        error: function () {
            alert('Terjadi kesalahan saat menyimpan data.');
            $btn.prop('disabled', false).text(originalText);
        }
    });
});


        $('#bupot').keyup(function (e) {
            var val = $(this).val();
            var ppn_23 = parseFloat(ppn.replace(/,/g, ''))
            $('#selisih_bupot').val(ppn_23 - val);
        });
    </script>
@endsection
