<div>
    <div class="col-12">
        <h4>FORM JURNAL GROUP JOB</h4>
    </div>
    <div class="col-8">
        <div class="card p-3">
            <div class="row">
                <div class="mb-2 col-8">
                    <label>Template Jurnal</label>
                    <select class="form-control" id="template_id" style="font-size:.9rem !important">
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
    <div class="col-12 mt-2">
        <div class="card p-3">
            <span>PARAM</span>
            <div class="d-flex flex-wrap gap-2" style="white-space: nowrap">
                <span class="bg-light-primary px-2 py-1">[1] ID JOB</span>
                <span class="bg-light-primary px-2 py-1">[2] Cont (XPDC)</span>
                <span class="bg-light-primary px-2 py-1">[3] Seal (XPDC)</span>
                <span class="bg-light-primary px-2 py-1">[4] Kapal (XPDC)</span>
                <span class="bg-light-primary px-2 py-1">[5] Voyage (XPDC)</span>
                <span class="bg-light-primary px-2 py-1">[6] Shipment (XPDC)</span>
                <span class="bg-light-primary px-2 py-1">[7] Pembayar (XPDC)</span>
                <span class="bg-light-primary px-2 py-1">[8] Customer (TRUCKING)</span>
                <span class="bg-light-primary px-2 py-1">[9] Shipment (TRUCKING)</span>
                <span class="bg-light-primary px-2 py-1">[10] Tujuan (TRUCKING)</span>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card p-2">
            <form action="{{ route('jurnal.kolektif.store') }}" method="post" class="row" id="form-submit">
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
                    <table class="table table-sm" id="table-debit">
                        <tr>
                            <td>#</td>
                            <td>JOB</td>
                            <td>Akun Debet</td>
                            <td>Akun Credit</td>
                            <td>Keterangan</td>
                            <td>Nominal</td>
                        </tr>
                        @for ($i = 0; $i < $debit_idx; $i++)
                        <tr class="init-table">
                            <td><input type="checkbox" name="id[]" onchange="uncheck(this,{{ $i }})" checked id="{{ $i }}" value="{{ $i }}"></td>
                            <td style="width: 200px">
                                <select class="form-control select2" id="job-{{ $i }}" onchange="getOrder()" name="job[]" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @foreach ($orders as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 200px">
                                <select class="form-control select2" onchange="total()" id="debit-{{ $i }}" name="debit_coa_id[]" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 200px">
                                <select class="form-control select2" onchange="total()" id="credit-{{ $i }}" name="credit_coa_id[]" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 300px"><input name="name[]" id="keterangan-{{ $i }}" style="width: 300px" type="text"></td>
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
                        <tr class="border border-top-md">
                            <td class="text-secondary" style="width: 300px"><span>CHECK VOUCHER</span></td>
                            <td class="text-secondary"><span id="total_voucher"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="btn-group mt-3">
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
    let credit = 2;
    let debit = 2;
    let total_debit = 0;
    let total_credit = 0;
    let check_order_id = [];

    $('.select2').select2();
    $('#reset').click(function (e) {
        location.reload();
    });
    $('#template_id').click(function (e) {
        setTimeout(() => {
            $('.select2').select2();
        }, 2000);
    });

    function simpan_jurnal(name){
        $('#simpan').val(name);
        if(total_debit!=total_credit){
            alert('Jurnal Tidak Balance debit = '+total_debit+' & credit = '+total_credit+' ! Harap check lagi')
        }else{
            if($('#tipe').val()){
                if(confirm('are you sure')){
                    if(check_order_id.length==0){
                        check_order_id = [0];
                    }
                    $.ajax({
                        type: "POST",
                        url: "{{ url('api/jurnal/check-omset') }}",
                        data: {
                            order_id:check_order_id
                        },
                        success: function (response) {
                            if (response.status==1) {
                                alert(response.message);
                            }else{
                                // alert('submit');
                                $('#form-submit').submit();
                            }
                        }
                    })
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
                        <td style="width: 200px">
                            <select class="form-control select2" id="job-${debit}" onchange="getOrder()" name="job[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($orders as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 200px">
                            <select class="form-control select2" onchange="total()" id="debit-${debit}" name="debit_coa_id[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 200px">
                            <select class="form-control select2" onchange="total()" id="credit-${debit}" name="credit_coa_id[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 300px"><input name="name[]" id="keterangan-${debit}" style="width: 300px" type="text"></td>
                        <td><input type="number" name="amount[]" onkeyup="total()" id="amount-${debit}"></td>
                    </tr>`;
        $('#table-debit').append(html);
        setTimeout(() => {
            $('.select2').select2();
        }, 1000);
        debit++;
    }


    function getOrder(){
        var order_id = $("select[name='job[]']").map(function(){return $(this).val();}).get();
        check_order_id = [];
        $.ajax({
            type: "POST",
            url: "{{ url('api/get-array-id') }}",
            data: {
                id:order_id,
                type:'job'
            },
            success: function (response) {
                let html = '';
                $.each(response, function (idx, item) {
                    check_order_id.push(item.id ?? 0);
                    html  +=
                    `
                    <tr>
                        <td>${item.no}</td>
                        <td>${item.container}</td>
                        <td>${item.seal}</td>
                        <td>${item.kapal}</td>
                        <td>${item.voyage}</td>
                        <td>${item.shipment}</td>
                        <td>${item.pembayar}</td>
                        <td>${item.customer_trucking}</td>
                        <td>${item.shipment_trucking}</td>
                        <td>${item.tujuan_trucking}</td>
                    </tr>
                    `
                });

                $('#table-order').html(html);
            }
        });
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
        let total_debit_prev = 0;
        let total_credit_prev = 0;
        let c16 = @json($c16);
        let c45 = @json($c45);
        let c175 = @json($c175);
        for (let i = 0; i < check.length; i++) {
            const item = check[i];
            var d = $('#debit-'+item).val();
            var c = $('#credit-'+item).val();
            var a = parseInt($('#amount-'+item).val());
            if(d==c16 || d==c45 || d==c175){
                total_debit_prev+=a;
            }
            if(c==c16 ||c==c45 || c==c175){
                total_credit_prev+=a;
            }
            if(d!=""){
                total_debit+=a;
            }
            if(c!=""){
                total_credit+=a;
            }
        }
        let voucher = total_debit_prev - total_credit_prev;
        if(voucher<0){
            voucher = voucher * -1;
        }
        $('#total_voucher').html('Rp. '+voucher.toLocaleString('en-US'));
        $('#total_debit').html('Rp. '+total_debit.toLocaleString('en-US'));
        $('#total_credit').html('Rp. '+total_credit.toLocaleString('en-US'));
    }

    $('#apply').click(function (e) {
        $('.init-table').remove();
        addTemplate();
    });
    $('#addBarisTemplate').click(function (e) {
        $('.init-table').remove();
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
                    html += `<tr>
                        <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                        <td>
                            <select class="form-control select2" onchange="total()" id="job-${debit}" onchange="getOrder()" name="job[]" style="font-size:.9rem !important; width:170px">
                                <option value=""></option>
                                @foreach ($orders as $item)
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
                        <td style="width: 250px"><input name="name[]" id="keterangan-${debit}" value="${item.keterangan}" style="width: 300px" type="text"></td>
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
