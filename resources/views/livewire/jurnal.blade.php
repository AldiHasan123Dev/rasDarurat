<div>
    <div class="col-6">
        <div class="card p-3">
            <div class="row">
                <div class="mb-2 col-8">
                    <label>JOB ID / SEAL</label>
                    <select class="form-control select2" id="select-order" wire:model="order" style="font-size:.9rem !important">
                        <option value=""></option>
                        @foreach ($orders as $item)
                        <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="mb-2 col-4">
                    <label>Tipe</label>
                    <select wire:model="tipe" class="form-control select2" style="font-size:.9rem !important">
                        <option value=""></option>
                        <option value="debit">Debit</option>
                        <option value="kredit">Kredit</option>
                    </select>
                </div> --}}
                <div class="mb-2 col-4">
                    <div class="btn-group">
                        <button class="btn btn-success btn-sm w-100 mt-3" id="apply" wire:click="apply">Terapkan</button>
                        <button class="btn btn-warning btn-sm w-100 mt-3" id="reset">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($order)
    <div class="col-12 mt-2">
        <div class="card p-3">
            <table class="table table-sm table-bordered" style="font-size:.7rem">
                <tr>
                    <td class="fw-bold">Param</td>
                    <td>[1] Pembayar (XPDC)</td>
                    <td>[2] Pengirim (XPDC)</td>
                    <td>[3] Penerima (XPDC)</td>
                    <td>[4] Pelayaran (XPDC)</td>
                    <td>[5] Customer (TRUCKING)</td>
                </tr>
                <tr>
                    <td class="fw-bold">Output</td>
                    <td id="pembayar">-</td>
                    <td id="pengirim">-</td>
                    <td id="penerima">-</td>
                    <td id="pelayaran">-</td>
                    <td id="customer">-</td>
                </tr>
            </table>
        </div>
    </div>
    @endif
    <div class="col-12 mt-3">
        <div class="card p-2">
            <form action="{{ route('jurnal.store') }}" method="post" class="row">
                @csrf
                <input type="hidden" name="order_id" id="order_id" value="{{ $order }}">
                <input type="hidden" name="jurnal_id" id="jurnal_id" value="{{ json_encode($jurnal_id) }}">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold" style="font-size: .8rem !important">Debit Account</span>
                        <button class="btn btn-info btn-sm" type="button" onclick="addColumnDebit()">Tambah Kolom</button>
                    </div>
                    <hr>
                    <table class="table table-sm" id="table-debit">
                        <tr>
                            <td>#</td>
                            <td>Nomor</td>
                            <td>Akun</td>
                            <td>Keterangan</td>
                            <td>Debit</td>
                            <td>Credit</td>
                        </tr>
                        @if ($is_apply)
                            @foreach ($jurnals->where('debit','>',0) as $deb)
                            <tr>
                                <td><input type="checkbox" onchange="check(this)" name="debit_id[]" id="debit-{{ $loop->iteration }}" value="{{ $loop->iteration }}"></td>
                                <td><input type="text" name="debit_nomor[]" value="{{ $deb->nomor }}"></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="debit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}" {{ $deb->coa_id==$item->id?'selected':'' }}>{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="debit_name[]" value="{{ $deb->nama }}" style="width: 300px" type="text"></td>
                                <td><input type="number" name="debit_amount[]" onkeyup="amount('debit')" value="{{ $deb->debit }}" id="amountD-{{ $loop->iteration }}"></td>
                                <td><input type="number" disabled></td>
                            </tr>
                            @endforeach
                        @else
                            @for ($i = 0; $i < $debit_idx; $i++)
                            <tr>
                                <td><input type="checkbox" onchange="check(this)" name="debit_id[]" id="debit-{{ $i }}" value="{{ $i }}"></td>
                                <td><input type="text" name="debit_nomor[]"></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="debit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="debit_name[]" style="width: 300px" type="text"></td>
                                <td><input type="number" name="debit_amount[]" onkeyup="amount('debit')" id="amountD-{{ $i }}"></td>
                                <td><input type="number" disabled></td>
                            </tr>
                            @endfor
                        @endif
                    </table>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold" style="font-size: .8rem !important">Credit Account</span>
                        <button class="btn btn-info btn-sm" type="button" onclick="addColumnCredit()">Tambah Kolom</button>
                    </div>
                    <hr>
                    <table class="table table-sm" id="table-credit">
                        <tr>
                            <td>#</td>
                            <td>Nomor</td>
                            <td>Akun</td>
                            <td>Keterangan</td>
                            <td>Debit</td>
                            <td>Credit</td>
                        </tr>
                        @if ($is_apply)
                            @foreach ($jurnals->where('credit','>',0) as $cre)
                            <tr>
                                <td><input type="checkbox" onchange="check(this)" name="credit_id[]" id="credit-{{ $loop->iteration }}" value="{{ $loop->iteration }}"></td>
                                <td><input type="text" name="credit_nomor[]" value="{{ $cre->nomor }}"></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="credit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}" {{ $cre->coa_id==$item->id?'selected':'' }}>{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="credit_name[]" value="{{ $cre->nama }}" style="width: 300px" type="text"></td>
                                <td><input type="number" disabled></td>
                                <td><input type="number" name="credit_amount[]" onkeyup="amount('credit')" value="{{ $cre->credit }}" id="amountD-{{ $loop->iteration }}"></td>
                            </tr>
                            @endforeach
                        @else
                            @for ($i = 0; $i < $credit_idx; $i++)
                            <tr>
                                <td><input type="checkbox" onchange="check(this)" name="credit_id[]" id="credit-{{ $i }}" value="{{ $i }}"></td>
                                <td><input type="text" name="credit_nomor[]"></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="credit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="credit_name[]" style="width: 300px" type="text"></td>
                                <td><input type="number" disabled></td>
                                <td><input type="number" onkeyup="amount('credit')" name="credit_amount[]" id="amountC-{{ $i }}"></td>
                            </tr>
                            @endfor
                        @endif
                    </table>
                </div>
                <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('are you sure?')">Simpan Jurnal</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let credit = 2;
    let debit = 2;
    $('.select2').select2();
    $('#select-order').on('change', function (e) {
        var data = $('#select-order').select2("val");
        @this.set('order', data);
        setTimeout(() => {
            $('.select2').select2();
            $('#order_id').val(data);
            $.ajax({
                type: "POST",
                url: "{{ url('api/order') }}"+'/'+data,
                success: function (response) {
                    $('#pembayar').html(response.pembayar);
                    $('#penerima').html(response.penerima);
                    $('#pengirim').html(response.pengirim);
                    $('#pelayaran').html(response.pelayaran);
                    $('#customer').html(response.customer_trucking);
                }
            });
        }, 1000);
    });

    $('#apply').click(function (e) {
        setTimeout(() => {
            $('.select2').select2();
            var data = $('#select-order').select2("val");
            $.ajax({
                type: "POST",
                url: "{{ url('api/order') }}"+'/'+data,
                success: function (response) {
                    $('#pembayar').html(response.pembayar);
                    $('#penerima').html(response.penerima);
                    $('#pengirim').html(response.pengirim);
                    $('#pelayaran').html(response.pelayaran);
                    $('#customer').html(response.customer_trucking);
                }
            });
        }, 1000);
    });
    $('#reset').click(function (e) {
        location.reload();
    });

    function addColumnCredit(){
        let html = `<tr>
                            <td><input type="checkbox" onchange="check(this)" name="credit_id[]" id="credit-${credit}" value="${credit}"></td>
                            <td><input type="text" name="credit_nomor[]"></td>
                            <td style="width: 200px">
                                <select class="form-control select2" name="credit_coa_id[]" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 300px"><input name="credit_name[]" style="width: 300px" type="text"></td>
                            <td><input type="number" disabled></td>
                            <td><input type="number" onkeyup="amount(credit)" name="credit_amount[]" id="amountC-${credit}"></td>
                        </tr>`;
        $('#table-credit').append(html);
        setTimeout(() => {
            $('.select2').select2();
        }, 1000);
        credit++;
    }
    function addColumnDebit(){
        let html = `<tr>
                            <td><input type="checkbox" onchange="check(this)" name="debit_id[]" id="debit-${debit}" value="${debit}"></td>
                            <td><input type="text" name="debit_nomor[]"></td>
                            <td style="width: 200px">
                                <select class="form-control select2" name="debit_coa_id[]" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 300px"><input name="debit_name[]" style="width: 300px" type="text"></td>
                            <td><input type="number" onkeyup="amount('debit')" name="debit_amount[]" id="amountD-${debit}"></td>
                            <td><input type="number" disabled></td>
                        </tr>`;
        $('#table-debit').append(html);
        setTimeout(() => {
            $('.select2').select2();
        }, 1000);
        debit++;
    }

    let data_debit = [];
    let data_credit = [];
    function check(e) {
        data_debit = $("#table-debit input:checkbox:checked").map(function(){
                            return $(this).val();
                        }).get();
        data_credit = $("#table-credit input:checkbox:checked").map(function(){
                            return $(this).val();
                        }).get();

        if (data_credit.length==1 && data_debit.length == 1) {
            $("#table-credit input:checkbox").attr('disabled',true);
            $("#table-debit input:checkbox").attr('disabled',true);
            $("#table-debit input:checkbox:checked").attr('disabled',false);
            $("#table-credit input:checkbox:checked").attr('disabled',false);
        }else{
            if (data_debit.length>1 && data_credit.length == 1) {
                $("#table-credit input:checkbox").attr('disabled',true);
                $("#table-credit input:checkbox:checked").attr('disabled',false);
            }else{
                $("#table-credit input:checkbox").attr('disabled',false);
            }
            if (data_credit.length>1 && data_debit.length == 1) {
                $("#table-debit input:checkbox").attr('disabled',true);
                $("#table-debit input:checkbox:checked").attr('disabled',false);
            }else{
                $("#table-debit input:checkbox").attr('disabled',false);
            }
        }
    }

    function amount(tipe = 'debit') {
        if (data_credit.length!=0 && data_debit.length!=0) {
            if(data_credit.length==data_debit.length){
                let credit_amount = $('#amountC-'+data_credit[0]).val();
                let debit_amount = $('#amountD-'+data_debit[0]).val();
                if(tipe=='credit'){
                    $('#amountD-'+data_debit[0]).val(credit_amount);
                }
                if(tipe=='debit'){
                    $('#amountC-'+data_credit[0]).val(debit_amount);
                }
            }
            if(data_credit.length>data_debit.length){
                let credit_amount = 0;
                for (let i = 0; i < data_credit.length; i++) {
                    const amount_c = $('#amountC-'+data_credit[i]).val();
                    credit_amount += parseInt(amount_c);
                }
                $('#amountD-'+data_debit[0]).val(credit_amount);
            }
            if(data_debit.length>data_credit.length){
                let debit_amount = 0;
                for (let i = 0; i < data_debit.length; i++) {
                    const amount_d = $('#amountD-'+data_debit[i]).val();
                    debit_amount += parseInt(amount_d);
                }
                $('#amountC-'+data_debit[0]).val(debit_amount);
            }
        }
    }


</script>
@endpush
