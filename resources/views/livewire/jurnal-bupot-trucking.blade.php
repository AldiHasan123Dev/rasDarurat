<div>
    <div class="col-12">
        <h4>FORM JURNAL BUPOT TRUCKING</h4>
    </div>
    <div class="col-8">
        <div class="card p-3">
            <div class="row">
                <div class="mb-2 col-8">
                    <label>Template Jurnal</label>
                    <select class="form-select" id="template_id" style="font-size:.9rem !important">
                        <option value=""></option>
                        @foreach ($templates as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 col-4">
                    <div class="btn-group">
                        <button class="btn btn-success btn-sm w-100 mt-3" id="apply">Terapkan</button>
                        <button class="btn btn-warning btn-sm w-100 mt-3" id="reset">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card p-2">
            <form action="{{ route('jurnal.bupot.trucking.store') }}" method="post" class="row" id="form-submit">
                @csrf
                <input type="hidden" name="order_id" id="order_id" value="{{ $order }}">
                <input type="hidden" name="jurnal_id" id="jurnal_id" value="{{ json_encode($jurnal_id) }}">
                <div class="col-12">
                    <div class="row">
                        <div class="col-8">
                            <div class="row">
                                <div class="col-6">
                                    <label for="tipe_jurnal">Tipe Jurnal</label>
                                    <select name="tipe" required id="tipe" class="form-select">
                                        <option value="">-</option>
                                        <option value="JNL">Jurnal Umum - {{ $no_1 }}</option>
                                        <option value="BBK">Bank Keluar - {{ $no_2 }}</option>
                                        <option value="BBM">Bank Masuk - {{ $no_3 }}</option>
                                        <option value="BKK">Kas Keluar - {{ $no_4 }}</option>
                                        <option value="BKM">Kas Masuk - {{ $no_5 }}</option>
                                        <option value="BBKT">Bank Keluar Trucking - {{ $no_6 }}</option>
                                        <option value="BBMT">Bank Masuk Trucking - {{ $no_7 }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="created_at">Tanggal Jurnal</label>
                                    <input type="date" name="created_at" id="created_at" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-4 mt-4 text-end">
                            <button class="btn btn-primary btn-sm mx-2" type="button" id="addBarisTemplate">Tambah Baris Template</button>
                            <button class="btn btn-info btn-sm mx-2" type="button" onclick="addColumnDebit()">Tambah Baris</button>
                        </div>
                    </div>
                    <hr>
                    <h5>Parameter Nama Pembayar -> [1]</h5>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-sm" id="table-debit">
                            <tr>
                                <th>#</th>
                                <th style="width: 300px">Invoice</th>
                                <th style="width: 300px">Akun Debet</th>
                                <th style="width: 300px">Akun Credit</th>
                                <th>Keterangan</th>
                                <th>Nominal</th>
                                <th>Nomor Bupot</th>
                                <th>Masa Pajak</th>
                                <th>Tanggal Bupot</th>
                            </tr>
                            @for ($i = 0; $i < $debit_idx; $i++)
                            <tr class="init-table">
                                <td><input type="checkbox" name="id[]" onchange="uncheck(this,{{ $i }})" checked id="{{ $i }}" value="{{ $i }}"></td>
                                <td style="width: 150px">
                                    <select class="form-control select2" id="invoice-{{ $i }}" name="invoice[]" style="font-size:.9rem !important; width:150px">
                                        <option value=""></option>
                                        @foreach ($invoice as $item)
                                            <option value="{{ $item->order_trucking_id }}">{{ $item->invoice }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2" onchange="total()" id="debit-{{ $i }}" name="debit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2" onchange="total()" id="credit-{{ $i }}" name="credit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 250px"><input name="name[]" id="keterangan-{{ $i }}" style="width: 300px" type="text"></td>
                                <td><input type="number" name="amount[]" onkeyup="total()" id="amount-{{ $i }}"></td>
                                <td><input type="text" name="no_bupot[]" id="no_bupot-{{ $i }}"></td>
                                <td><input type="month" name="masa_bupot[]" id="masa_bupot-{{ $i }}"></td>
                                <td><input type="date" name="tanggal_bupot[]" id="tanggal_bupot-{{ $i }}"></td>
                            </tr>
                            @endfor
                        </table>
                    </div>
                    <table>
                        <tr>
                            <td style="width: 300px"><b>TOTAL DEBET</b></td>
                            <td><b id="total_debit"></b></td>
                        </tr>
                        <tr>
                            <td style="width: 300px"><b>TOTAL CREDIT</b></td>
                            <td><b id="total_credit"></b></td>
                        </tr>
                    </table>
                </div>
                <div class="btn-group">
                    <input type="hidden" name="simpan" id="simpan">
                    <button type="button" class="btn btn-success btn-sm w-50 mx-2 btn-save" onclick="simpan_jurnal('jurnal')">Simpan Jurnal</button>
                    <button type="button" class="btn btn-warning btn-sm w-50 mx-2 btn-save" onclick="simpan_jurnal('tampungan')">Simpan Tampungan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/js/selectize.js') }}"></script>
<script>
    let total_debit = 0;
    let total_credit = 0;
    let credit = 2;
    let debit = 2;
    $('.select2').select2();
    $('#reset').click(function (e) {
        location.reload();
    });

    function simpan_jurnal(name){
        $('#simpan').val(name);
        if(total_debit!=total_credit){
            alert('Jurnal Tidak Balance debit = '+total_debit+' & credit = '+total_credit+' ! Harap check lagi')
        }else{
            if($('#tipe').val()){
                if(confirm('are you sure')){
                    $('#form-submit').submit();
                }
            }else{
                alert('Harap pilih tipe jurnal');
            }
        }
    }

    function addColumnDebit(){
        var amounts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
        debit = amounts.length + 1;
        let html = `<tr>
                        <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                        <td style="width: 150px">
                            <select class="form-control select2" id="invoice-${debit}" name="invoice[]" style="font-size:.9rem !important; width:150px">
                                <option value=""></option>
                                @foreach ($invoice as $item)
                                    <option value="{{ $item->order_trucking_id }}">{{ $item->invoice }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control select2" onchange="total()" id="debit-${debit}" name="debit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control select2" onchange="total()" id="credit-${debit}" name="credit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 250px"><input name="name[]" id="keterangan-${debit}" style="width: 300px" type="text"></td>
                        <td><input type="number" name="amount[]" onkeyup="total()" id="amount-${debit}"></td>
                        <td><input type="text" name="no_bupot[]" id="no_bupot-${debit}"></td>
                        <td><input type="month" name="masa_bupot[]" id="masa_bupot-${debit}"></td>
                        <td><input type="date" name="tanggal_bupot[]" id="tanggal_bupot-${debit}"></td>
                    </tr>`;
        $('#table-debit').append(html);
        setTimeout(() => {
            $('.select2').select2();
        }, 1000);
        debit++;
    }

    function uncheck (e,id) {
        if($('#' + id).is(":checked")){
            $('#job-'+id).attr('disabled',false);
            $('#debit-'+id).attr('disabled',false);
            $('#credit-'+id).attr('disabled',false);
            $('#keterangan-'+id).attr('disabled',false)
            $('#amount-'+id).attr('disabled',false)
        }else{
            $('#job-'+id).attr('disabled',true);
            $('#debit-'+id).attr('disabled',true);
            $('#credit-'+id).attr('disabled',true);
            $('#keterangan-'+id).attr('disabled',true)
            $('#amount-'+id).attr('disabled',true)
        }
        total();
    }

    function total(){
        var check = $("input[name='id[]']").map(function(){
            if($(this).is(":checked")){
                return $(this).val();
            }
        }).get();
        total_credit = 0;
        total_debit = 0;
        for (let i = 0; i < check.length; i++) {
            const item = check[i];
            var d = $('#debit-'+item).val();
            var c = $('#credit-'+item).val();
            var a = parseInt($('#amount-'+item).val());
            if(d!=""){
                total_debit+=a;
            }
            if(c!=""){
                total_credit+=a;
            }
        }
        $('#total_debit').html('Rp. '+total_debit.toLocaleString('en-US'));
        $('#total_credit').html('Rp. '+total_credit.toLocaleString('en-US'));
    }

    $('#apply').click(function (e) {
        $('.init-table').hide();
        addTemplate();
    });

    $('#addBarisTemplate').click(function (e) {
        addTemplate();
    });

    function addTemplate(){
        let id = $('#template_id').val();
        $.ajax({
            type: "get",
            url: "{{ url('admin/templatejurnal') }}"+"/"+id,
            success: function (response) {
                $.each(response.items, function (idx, item) {
                    var amounts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
                    debit = amounts.length + 1;
                    let html = '';
                    html += `<tr class="init-table">
                            <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                            <td style="width: 150px">
                                <select class="form-control select2" id="invoice-${debit}" name="invoice[]" style="font-size:.9rem !important; width:150px">
                                    <option value=""></option>
                                    @foreach ($invoice as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control select2" onchange="total()" id="debit-${debit}" name="debit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control select2" onchange="total()" id="credit-${debit}" name="credit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 250px"><input name="name[]" value="${item.keterangan}" id="keterangan-${debit}" style="width: 300px" type="text"></td>
                            <td><input type="number" name="amount[]" onkeyup="total()" id="amount-${debit}"></td>
                        </tr>`;
                    $('#table-debit').append(html);
                    $('#debit-'+debit).val(item.coa_debit_id);
                    $('#credit-'+debit).val(item.coa_credit_id);
                });
            }
        });
        setTimeout(() => {
            $('.select2').select2();
        }, 2000);
    }
</script>
@endpush
