<style>
    .btn-bank {
        background-color: #1a532f !important;
        /* Tailwind bg-green-300 */
        color: white !important;
        border-color: #1a532f !important;
    }

    .btn-active {
        background-color: #4ade80 !important;
        /* Tailwind bg-green-300 */
        color: white !important;
        border-color: #4ade80 !important;
    }
</style>

<div class="row">
    <div class="col-12 mt-3">
        {{-- <div class="row">
            <div class="col-9">
                <div class="d-flex gap-2">
                    <b class="mt-2">Bulan: </b>
                    @foreach ($months as $idx => $item)
                        <a href="{{ route('jurnal.index',['month'=>sprintf('%02d',$idx+1),'tipe'=>$tipe, 'year'=>$year, 'is_sample' => $is_sample]) }}" class="{{ $idx+1==(int)$month?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">{{ $item }}</a>
                    @endforeach
                </div>
            </div>
            <div class="col-3">
                <form action="{{ route('jurnal.index') }}" method="get">
                    <input type="hidden" name="tipe" value="{{ $tipe }}">
                    <input type="hidden" name="is_sample" value="{{ $is_sample }}">
                    <input type="hidden" name="month" value="{{ request('month') }}">
                    <select class="form-control px-3 py-1" name="year" onchange="submit()" style="font-size:.8rem">
                        <option {{ $year=='2023'?'selected':'' }} value="2023">2023</option>
                        <option {{ $year=='2024'?'selected':'' }} value="2024">2024</option>
                        <option {{ $year=='2025'?'selected':'' }} value="2025">2025</option>
                        <option {{ $year=='2026'?'selected':'' }} value="2026">2026</option>
                        <option {{ $year=='2027'?'selected':'' }} value="2027">2027</option>
                        <option {{ $year=='2028'?'selected':'' }} value="2028">2028</option>
                        <option {{ $year=='2029'?'selected':'' }} value="2029">2029</option>
                        <option {{ $year=='2030'?'selected':'' }} value="2030">2030</option>
                    </select>
                </form>
            </div>
            <div class="col-8">
                <div class="my-3">
                    <div class="row">
                        <div class="col-4">
                            <label for="search">Search</label>
                            <input type="text" id="search" class="form-control" placeholder="Cari berdasarkan nomor jurnal/keterangan/akun/job/tanggal/invoice/container">
                        </div>
                        <div class="col-4">
                            <label for="search">Filter Tanggal</label>
                            <form action="{{ route('jurnal.index') }}" method="get">
                                <input type="hidden" name="tipe" value="{{ $tipe }}">
                                <input type="hidden" name="month" value="{{ $month }}">
                                <input type="hidden" name="year" value="{{ $year }}">
                                <input type="hidden" name="is_sample" value="{{ $is_sample }}">
                                <input type="date" class="form-control" name="date" onchange="submit()" value="{{ request('date') }}">
                            </form>
                        </div>
                        <div class="col-4">
                            <label for="is_sample">Kategori</label>
                            <form  action="{{ route('jurnal.index') }}" method="get" class="d-flex gap-2">
                                <input type="hidden" name="tipe" value="{{ $tipe }}">
                                <input type="hidden" name="year" value="{{ $year }}">
                                <input type="hidden" name="month" value="{{ $month }}">
                                <label for="sample_false">
                                    <input type="radio" name="is_sample" id="sample_false" value="real" {{ $is_sample=='real' ? 'checked' : '' }} onclick="submit()"> Jurnal Real
                                </label>
                                <label for="sample_true">
                                    <input type="radio" name="is_sample" id="sample_true" value="sample" {{ $is_sample=='sample' ? 'checked' : '' }} onclick="submit()"> Jurnal Sample
                                </label>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="d-flex justify-content-between">
                    <div class="d-flex gap-2 mt-5">
                        <b class="mt-2">Tipe: </b>
                        <a href="{{ route('jurnal.index',['tipe'=>'BB','month'=>request('month'),'year'=>request('year'), 'is_sample' => $is_sample]) }}" class="{{ $tipe=='BB'?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">BANK</a>
                        <a href="{{ route('jurnal.index',['tipe'=>'BK','month'=>request('month'),'year'=>request('year'), 'is_sample' => $is_sample]) }}" class="{{ $tipe=='BK'?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">KAS</a>
                        <a href="{{ route('jurnal.index',['tipe'=>'JNL','month'=>request('month'),'year'=>request('year'), 'is_sample' => $is_sample]) }}" class="{{ $tipe=='JNL'?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">JNL</a>
                        <a href="{{ route('jurnal.index',['tipe'=>'TEST','month'=>request('month'),'year'=>request('year'), 'is_sample' => $is_sample]) }}" class="{{ $tipe=='TEST'?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">TEST</a>
                    </div>
                    <div>
                        <a href="" class="btn btn-sm btn-primary mt-5" id="edit-btn">Edit</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsives">
            <table id="jqGrid"></table>
            <div id="jqGridPager"></div>
        </div> --}}
        {{-- {{ $data->links() }} --}}
        {{-- @if ($data->hasMorePages()) --}}
        {{-- <button wire:click.prevent="loadMore" class="btn btn-sm btn-primary w-100">Load more</button> --}}
        {{-- @endif --}}
        <div class="row">
            {{-- <b class="mb-3">Cek Jurnal Harian</b> --}}
            {{-- <div class="col-12 mb-2 col-md-6">
                <label for="" class="form-label">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan" class="form-control">
            </div>
            <div class="col-12 mb-2 col-md-6">
                <label for="" class="form-label">Container</label>
                <input type="text" name="container" id="container" class="form-control">
            </div> --}}

            <!-- gx = horizontal gap, gy = vertical -->
            {{-- <div class="col-auto">
        <button type="button"
            class="btn btn-bank {{ request('bank') == 'bank' ? 'btn-active' : '' }}"
            onclick="setTipe('Bank')">
            Bank
        </button>
        <input type="hidden" id="bank" name="bank" value="{{ request('bank') }}">
    </div>


    <div class="col-auto">
        <button type="button"
            class="btn btn-bank"
            onclick="setKas('Kas')"
            id="btn-kas">
            Kas
        </button>
        <input type="hidden" name="kas" id="kas" value="">
    </div>

    <div class="col-auto">
        <button type="button"
            class="btn btn-bank"
            onclick="setJurnal('Jurnal')"
            id="btn-jnl">
            Jurnal
        </button>
        <input type="hidden" name="jurnal" id="jurnal" value="">
    </div>

     <div class="col-auto">
        <button type="button"
            class="btn btn-bank"
            onclick="setBkt('bkt')"
            id="btn-bkt">
            Bank Trucking
        </button>
        <input type="hidden" name="bkt" id="bkt" value="">
    </div>

        <div class="col-auto mb-6">
               <input type="date" name="tgl" id="tgl" class="form-control" >
            </div>





          <div class="col-12 mb-4 col-md-3 ms-auto">
    <div class="d-flex justify-content-end gap-2">
        <button class="btn btn-success btn-sm" type="button" onclick="searchJurnal()">Search</button>
        <a href="#" class="btn btn-sm btn-primary" id="edit-btn">Edit</a>
        <a class="btn btn-sm btn-warning" target="_blank" id="edit-coa">Edit COA</a> <!-- Contoh tambahan -->
    </div>
</div>

        </div>
        <div class="table-responsives">
            <table id="jqGrid"></table>
            <div id="jqGridPager"></div>
        </div> --}}
            <table class="table table-sm mt-2">
                {{-- @if ($total_debit != $total_credit)
                <tr>
                    <td colspan="2" class="text-center text-danger"><div class="alert alert-danger">JURNAL TIDAK BALANCE</div></td>
                </tr>
            @endif --}}
                <tr>
                    <td>Debit</td>
                    <td>: {{ number_format($total_debit, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Credit</td>
                    <td>: {{ number_format($total_credit, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>JNL TERAKHIR</td>
                    <td>: {{ number_format($total_jnl) }}</td>
                </tr>
            </table>
        </div>
        <div class="row">
            <b class="mb-3">Pencarian Data Jurnal</b>
            <div class="col-12 mb-2 col-md-3">
                <label for="" class="form-label">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan" class="form-control">
            </div>
            <div class="col-12 mb-2 col-md-3">
                <label for="" class="form-label">Container</label>
                <input type="text" name="container" id="container" class="form-control">
            </div>
            <div class="col-12 mb-2 col-md-3">
                <label for="" class="form-label">No Jurnal</label>
                <input type="text" name="no-jnl" id="no-jnl" class="form-control">
            </div>
            <div class="col-12 mb-2 col-md-3">
                <label for="" class="form-label">Group JOB</label>
                <input type="text" name="job" id="job" class="form-control">
            </div>
            <div class="col-12 mb-4 col-md-3 ms-auto">
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-success btn-sm" type="button" onclick="searchJurnal1()">Search</button>
                    <a href="#" class="btn btn-sm btn-primary" target="_blank" id="edit-btn">Edit</a>
                </div>

            </div>
        </div>
        <div class="table-responsives">
            <table id="jqGrid1"></table>
            <div id="jqGridPager1"></div>
        </div>

        @push('scripts')
            <script src="{{ asset('assets/js/resize-column.js') }}"></script>
            <script>
                let id;
                let kategori = @json($is_sample);
                $("#jqGrid1").jqGrid({
                    url: '{{ route('jqgrid.jurnal') }}',
                    mtype: 'GET',
                    datatype: 'json',
                    postData: {
                        kategori: kategori
                    },
                    colModel: [{
                            search: true,
                            width: 50,
                            name: 'created_at',
                            label: 'Tanggal'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'nomor',
                            label: 'Nomor Jurnal',
                            sortable: false
                        },
                        {
                            search: true,
                            width: 50,
                            name: 'coa_kode',
                            label: 'Kode'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'coa_nama',
                            label: 'Akun'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'id',
                            label: 'id',
                            hidden: true
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'invoice',
                            label: 'Invoice'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'job',
                            label: 'Group JOB'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'no_job',
                            label: 'ID JOB'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'container',
                            label: 'Container'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'nopol',
                            label: 'Nopol'
                        },
                        {
                            search: true,
                            width: 300,
                            name: 'nama',
                            label: 'Keterangan'
                        },
                        {
                            name: 'debits',
                            label: 'Debit',
                            width: 100,
                            search: true,
                            align: 'right',
                            formatter: 'number',
                            formatoptions: {
                                decimalSeparator: ".",
                                thousandsSeparator: ",",
                                decimalPlaces: 2,
                                defaultValue: "0.00"
                            }
                        },
                        {
                            name: 'credits',
                            label: 'Credit',
                            width: 100,
                            search: true,
                            align: 'right',
                            formatter: 'number',
                            formatoptions: {
                                decimalSeparator: ".",
                                thousandsSeparator: ",",
                                decimalPlaces: 2,
                                defaultValue: "0.00"
                            }
                        }

                    ],
                    autowidth: true,
                    shrinkToFit: true,
                    height: 'auto',
                    oadonce: true,
                    rowNum: 25,
                    rowList: [10, 25, 50, 100, 250, 500, 1000],
                    viewrecords: true,
                    pager: "#jqGridPager1",
                    caption: "Jurnal List",
                    onCellSelect: function(rowId, iRow, iCol, e) {
                        id = $(this).jqGrid('getCell', rowId, 'id');
                        let nomor = $(this).jqGrid('getCell', rowId, 'nomor');
                        $('#edit-btn').attr('href', @json(url('admin/jurnal-edit')) + '?jurnal=' + nomor);

                    },
                    rowattr: function(item) {
                        return {
                            "class": item.class
                        };
                    }
                });

                $('#jqGrid1').jqGrid('navGrid', "#jqGridPager", {
                    search: false,
                    add: false,
                    edit: false,
                    del: false,
                    refresh: true
                });
                $("#jqGrid1").jqGrid('setFrozenColumns');

                $('#search').keyup(function(e) {
                    let val = $(this).val();
                    $("#jqGrid1").jqGrid('setGridParam', {
                        postData: {
                            month: @json($month),
                            tipe: @json($tipe),
                            search: val
                        }
                    }).trigger('reloadGrid');
                });

                $("#jqGrid").jqGrid({
                    url: '{{ route('jqgrid.jurnal') }}',
                    mtype: 'GET',
                    datatype: 'json',
                    postData: {
                        kategori: kategori
                    },
                    colModel: [{
                            search: true,
                            width: 50,
                            name: 'created_at',
                            label: 'Tanggal',
                            frozen: true
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'nomor',
                            label: 'Nomor Jurnal',
                            frozen: true,
                            sortable: false
                        },
                        {
                            search: true,
                            width: 50,
                            name: 'coa_kode',
                            label: 'Kode',
                            frozen: true,
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'coa_nama',
                            label: 'Akun',
                            frozen: true,
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'id',
                            label: 'id',
                            hidden: true
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'invoice',
                            label: 'Invoice'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'job',
                            label: 'Group JOB'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'no_job',
                            label: 'ID JOB'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'container',
                            label: 'Container'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'nopol',
                            label: 'Nopol'
                        },
                        {
                            search: true,
                            width: 300,
                            name: 'nama',
                            label: 'Keterangan'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'debit',
                            label: 'Debit'
                        },
                        {
                            search: true,
                            width: 100,
                            name: 'credit',
                            label: 'Credit'
                        },
                    ],
                    autowidth: true,
                    shrinkToFit: true,
                    height: 'auto',
                    oadonce: true,
                    rowNum: 25,
                    rowList: [10, 25, 50, 100, 250, 500, 1000],
                    viewrecords: true,
                    pager: "#jqGridPager",
                    caption: "Jurnal List",
                    onCellSelect: function(rowId, iRow, iCol, e) {
                        id = $(this).jqGrid('getCell', rowId, 'id');
                        let nomor = $(this).jqGrid('getCell', rowId, 'nomor');
                        $('#edit-coa').attr('href', @json(route('jurnal.edit.coa')) + '?jurnal=' + encodeURIComponent(
                            nomor));

                    },
                    rowattr: function(item) {
                        return {
                            "class": item.class
                        };
                    }
                });

                $('#jqGrid').jqGrid('navGrid', "#jqGridPager", {
                    search: false,
                    add: false,
                    edit: false,
                    del: false,
                    refresh: true
                });
                $("#jqGrid").jqGrid('setFrozenColumns');

                $('#search').keyup(function(e) {
                    let val = $(this).val();
                    $("#jqGrid").jqGrid('setGridParam', {
                        postData: {
                            month: @json($month),
                            tipe: @json($tipe),
                            search: val
                        }
                    }).trigger('reloadGrid');
                });

                function changeKategori(type) {
                    kategori = type;
                }

                function setKas(kas) {
                    const inputKas = document.getElementById('kas');
                    const inputBank = document.getElementById('bank');
                    const inputJurnal = document.getElementById('jurnal');
                    const inputBkt = document.getElementById('bkt');
                    const btnKas = document.getElementById('btn-kas');
                    const btnBank = document.querySelectorAll('.btn-bank');
                    const btnJurnal = document.getElementById('btn-jnl');
                    const btnBkt = document.getElementById('btn-bkt');

                    // Nonaktifkan yang lain
                    inputBank.value = '';
                    btnBank.forEach(btn => btn.classList.remove('btn-active'));

                    inputJurnal.value = '';
                    if (btnJurnal) btnJurnal.classList.remove('btn-active');

                    inputBkt.value = '';
                    if (btnBkt) btnBkt.classList.remove('btn-active');

                    // Toggle kas
                    if (inputKas.value === kas) {
                        inputKas.value = '';
                        btnKas.classList.remove('btn-active');
                    } else {
                        inputKas.value = kas;
                        btnKas.classList.add('btn-active');
                    }
                }

                function setBkt(bkt) {
                    const inputKas = document.getElementById('kas');
                    const inputBank = document.getElementById('bank');
                    const inputJurnal = document.getElementById('jurnal');
                    const inputBkt = document.getElementById('bkt');
                    const btnKas = document.getElementById('btn-kas');
                    const btnBank = document.querySelectorAll('.btn-bank');
                    const btnJurnal = document.getElementById('btn-jnl');
                    const btnBkt = document.getElementById('btn-bkt');

                    // Nonaktifkan yang lain
                    inputBank.value = '';
                    btnBank.forEach(btn => btn.classList.remove('btn-active'));

                    inputKas.value = '';
                    if (btnKas) btnKas.classList.remove('btn-active');

                    inputJurnal.value = '';
                    if (btnJurnal) btnJurnal.classList.remove('btn-active');

                    // Toggle bkt
                    if (inputBkt.value === bkt) {
                        inputBkt.value = '';
                        btnBkt.classList.remove('btn-active');
                    } else {
                        inputBkt.value = bkt;
                        btnBkt.classList.add('btn-active');
                    }
                }

                function setJurnal(jurnal) {
                    const inputKas = document.getElementById('kas');
                    const inputBank = document.getElementById('bank');
                    const inputJurnal = document.getElementById('jurnal');
                    const inputBkt = document.getElementById('bkt');
                    const btnKas = document.getElementById('btn-kas');
                    const btnBank = document.querySelectorAll('.btn-bank');
                    const btnJurnal = document.getElementById('btn-jnl');
                    const btnBkt = document.getElementById('btn-bkt');

                    // Nonaktifkan yang lain
                    inputBank.value = '';
                    btnBank.forEach(btn => btn.classList.remove('btn-active'));

                    inputKas.value = '';
                    if (btnKas) btnKas.classList.remove('btn-active');

                    inputBkt.value = '';
                    if (btnBkt) btnBkt.classList.remove('btn-active');

                    // Toggle jurnal
                    if (inputJurnal.value === jurnal) {
                        inputJurnal.value = '';
                        btnJurnal.classList.remove('btn-active');
                    } else {
                        inputJurnal.value = jurnal;
                        btnJurnal.classList.add('btn-active');
                    }
                }

                function setTipe(bank) {
                    const inputBank = document.getElementById('bank');
                    const inputKas = document.getElementById('kas');
                    const inputJurnal = document.getElementById('jurnal');
                    const inputBkt = document.getElementById('bkt');
                    const btnKas = document.getElementById('btn-kas');
                    const btnJurnal = document.getElementById('btn-jnl');
                    const btnBank = document.querySelectorAll('.btn-bank');
                    const btnBkt = document.getElementById('btn-bkt');

                    let isActive = false;

                    btnBank.forEach(btn => {
                        if (btn.textContent.trim() === bank && btn.classList.contains('btn-active')) {
                            isActive = true;
                        }
                        btn.classList.remove('btn-active');
                    });

                    if (isActive) {
                        inputBank.value = '';
                    } else {
                        inputBank.value = bank;

                        btnBank.forEach(btn => {
                            if (btn.textContent.trim() === bank) {
                                btn.classList.add('btn-active');
                            }
                        });

                        inputKas.value = '';
                        if (btnKas) btnKas.classList.remove('btn-active');

                        inputJurnal.value = '';
                        if (btnJurnal) btnJurnal.classList.remove('btn-active');

                        inputBkt.value = '';
                        if (btnBkt) btnBkt.classList.remove('btn-active');
                    }
                }




                function searchJurnal() {
                    const keterangan = $('#keterangan').val().trim();
                    const container = $('#container').val().trim();
                    let tgl = $('#tgl').val();

                    const bankInput = document.getElementById('bank');
                    const bktInput = document.getElementById('bkt');
                    const jurnalInput = document.getElementById('jurnal');
                    const kasInput = document.getElementById('kas');

                    const btnKas = document.getElementById('btn-kas');
                    const btnJurnal = document.getElementById('btn-jnl');
                    const btnBank = document.querySelectorAll('.btn-bank');
                    const btnBkt = document.getElementById('btn-bkt');

                    let bank = bankInput?.value || '';
                    let bkt = bktInput?.value || '';
                    let jurnal = jurnalInput?.value || '';
                    let kas = kasInput?.value || '';

                    // Reset filter jika keterangan dan container diisi


                    // Jalankan pencarian di jqGrid
                    $("#jqGrid").jqGrid('setGridParam', {
                        postData: {
                            kategori: "real",
                            tgl,
                            bank,
                            kas,
                            jurnal,
                            bkt
                        },
                        page: 1
                    }).trigger('reloadGrid');
                }

                function searchJurnal1() {
                    const keterangan = $('#keterangan').val().trim();
                    const container = $('#container').val().trim();
                    const nomor = $('#no-jnl').val();
                    const job = $('#job').val();

                    // Reset filter jika keterangan dan container diisi

                    // Jalankan pencarian di jqGrid
                    $("#jqGrid1").jqGrid('setGridParam', {
                        postData: {
                            kategori: "real",
                            keterangan,
                            container,
                            nomor: nomor,
                            job: job
                        },
                        page: 1
                    }).trigger('reloadGrid');
                }
            </script>
        @endpush
