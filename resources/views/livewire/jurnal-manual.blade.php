<div>
    <div class="col-12">
        <h4>FORM JURNAL MANUAL</h4>
    </div>
    <div class="col-12 mt-3">
        <div class="card p-2">
            <form action="{{ route('jurnal.manual.store') }}" method="post" class="row" id="form-submit">
                @csrf
                <input type="hidden" name="order_id" id="order_id" value="{{ $order }}">
                <input type="hidden" name="jurnal_id" id="jurnal_id" value="{{ json_encode($jurnal_id) }}">
                <div class="col-12">
                    <div class="row">
                        <div class="col-8">
                            <div class="row">
                                <div class="col-6">
                                    <label for="tipe_jurnal">Tipe Jurnal</label>
                                    <select name="tipe" required id="tipe" class="form-control">
                                        <option value="">-</option>
                                        <option value="JNL">Jurnal Umum - {{ $no_1 }}</option>
                                        <option value="BBK">Bank Keluar - {{ $no_2 }}</option>
                                        <option value="BBM">Bank Masuk - {{ $no_3 }}</option>
                                        <option value="BKK">Kas Keluar - {{ $no_4 }}</option>
                                        <option value="BKM">Kas Masuk - {{ $no_5 }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="created_at">Tanggal Jurnal</label>
                                    <input type="date" name="created_at" id="created_at" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-4 mt-4 text-end">
                            <button class="btn btn-info btn-sm mx-2" type="button" onclick="addColumnDebit()">Tambah Baris</button>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-sm" id="table-debit">
                        <tr>
                            <th>#</th>
                            <th style="width: 300px">Invoice</th>
                            <th style="width: 300px">Nopol</th>
                            <th style="width: 300px">Akun Debet</th>
                            <th style="width: 300px">Akun Credit</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                        </tr>
                        @for ($i = 0; $i < $debit_idx; $i++)
                        <tr class="init-table">
                            <td><input type="checkbox" name="id[]" onchange="uncheck(this,{{ $i }})" checked id="{{ $i }}" value="{{ $i }}"></td>
                            <td style="width: 150px"><input name="invoice[]" id="invoice-{{ $i }}" style="width: 150px" type="text"></td>
                            <td style="width: 150px">
                                <select class="form-control select2" id="nopol-{{ $i }}" name="nopol[]" style="font-size:.9rem !important; width:150px">
                                    <option value=""></option>
                                    @foreach ($kendaraan as $item)
                                        <option value="{{ $item->nopol }}">{{ $item->nopol }}</option>
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
                        </tr>
                        @endfor
                    </table>
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
                <button type="button" class="btn btn-success btn-sm w-100" id="btn-save">Simpan Jurnal</button>
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

    function addColumnDebit(){
        var amounts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
        debit = amounts.length + 1;
        let html = `<tr>
                        <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                        <td style="width: 150px"><input name="invoice[]" id="invoice-${debit}" style="width: 150px" type="text"></td>
                        <td style="width: 150px">
                                <select class="form-control select2" id="nopol-${debit}" name="nopol[]" style="font-size:.9rem !important; width:150px">
                                    <option value=""></option>
                                    @foreach ($kendaraan as $item)
                                        <option value="{{ $item->nopol }}">{{ $item->nopol }}</option>
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
                    </tr>`;
        $('#table-debit').append(html);
        setTimeout(() => {
            $('.select2').select2();
        }, 1000);
        debit++;
    }

    $('#btn-save').click(function (e) {
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
    });

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
</script>
@endpush
