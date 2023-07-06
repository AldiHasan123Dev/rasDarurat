<div>
    <div class="col-12">
        <h4>FORM JURNAL</h4>
    </div>
    <div class="col-8">
        <div class="card p-3">
            <div class="row">
                <div class="mb-2 col-8">
                    <label>Template Jurnal</label>
                    <select class="form-control" id="template_id" wire:model="template_id" style="font-size:.9rem !important">
                        <option value=""></option>
                        @foreach ($templates as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 col-4">
                    <div class="btn-group">
                        <button class="btn btn-success btn-sm w-100 mt-3" id="apply" wire:click="apply">Terapkan</button>
                        <button class="btn btn-warning btn-sm w-100 mt-3" id="reset">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-2">
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-sm table-bordered" style="font-size:.7rem; white-space:nowrap">
                    <thead>
                        <tr>
                            <th>[1] ID JOB</th>
                            <th>[2] Cont (XPDC)</th>
                            <th>[3] Seal (XPDC)</th>
                            <th>[4] Kapal (XPDC)</th>
                            <th>[5] Voyage (XPDC)</th>
                            <th>[6] Shipment (XPDC)</th>
                            <th>[7] Pembayar (XPDC)</th>
                            <th>[8] Customer (TRUCKING)</th>
                            <th>[9] Shipment (TRUCKING)</th>
                            <th>[10] Tujuan (TRUCKING)</th>
                        </tr>
                    </thead>
                    <tbody id="table-order">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card p-2">
            <form action="{{ route('jurnal.store') }}" method="post" class="row" id="form-submit">
                @csrf
                <input type="hidden" name="order_id" id="order_id" value="{{ $order }}">
                <input type="hidden" name="jurnal_id" id="jurnal_id" value="{{ json_encode($jurnal_id) }}">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <label for="tipe_jurnal">Tipe Jurnal</label>
                            <select name="tipe" id="tipe_jurnal" class="form-control">
                                <option selected value="JNL">Jurnal Umum - {{ $no_1 }}</option>
                                <option value="BBK">Bank Keluar - {{ $no_2 }}</option>
                                <option value="BBM">Bank Masuk - {{ $no_3 }}</option>
                                <option value="BKK">Kas Keluar - {{ $no_4 }}</option>
                                <option value="BKM">Kas Masuk - {{ $no_5 }}</option>
                            </select>
                        </div>
                        <div class="col-6 mt-4 text-end">
                            <button class="btn btn-primary btn-sm mx-2" type="button" id="addBarisTemplate" wire:click="addBarisTemplate">Tambah Baris Template</button>
                            <button class="btn btn-info btn-sm mx-2" type="button" onclick="addColumnDebit()">Tambah Baris</button>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-sm" id="table-debit">
                        <tr>
                            <th>#</th>
                            <th style="width: 300px">ID Job/Seal</th>
                            <th style="width: 300px">Akun Debet</th>
                            <th style="width: 300px">Akun Credit</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                            <th>Tanggal</th>
                        </tr>
                        @if (is_null($template))
                            @for ($i = 0; $i < $debit_idx; $i++)
                            <tr>
                                <td><input type="checkbox" name="id[]" onchange="uncheck(this,{{ $i }})" checked id="{{ $i }}" value="{{ $i }}"></td>
                                <td>
                                    <select class="form-control select2" id="job-{{ $i }}" onchange="getOrder()" name="order_id[]" style="font-size:.9rem !important; width:170px">
                                        <option value=""></option>
                                        @foreach ($orders as $item)
                                        <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
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
                                <td><input type="date" name="created_at[]" value="{{ date('Y-m-d') }}"></td>
                            </tr>
                            @endfor
                        @else
                            @php
                                $k = 0;
                            @endphp
                            @for ($j = 0; $j < $template_count; $j++)
                                @foreach ($template->template_items as $i => $temp)
                                <tr>
                                    <td><input type="checkbox" name="id[]" onchange="uncheck(this,{{ $k }})" checked id="{{ $k }}" value="{{ $k }}"></td>
                                    <td>
                                        <select class="form-control select2" onchange="getOrder()" id="job-{{ $k }}" name="order_id[]" style="font-size:.9rem !important; width:170px;">
                                            <option value=""></option>
                                            @foreach ($orders as $item)
                                            <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" onchange="total()" id="debit-{{ $k }}" name="debit_coa_id[]" style="font-size:.9rem !important; width:170px;">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option value="{{ $item->id }}" {{ $item->id==$temp->coa_debit_id?'selected':'' }}>{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" onchange="total()" id="credit-{{ $k }}" name="credit_coa_id[]" style="font-size:.9rem !important; width:170px;">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option value="{{ $item->id }}" {{ $item->id==$temp->coa_credit_id?'selected':'' }}>{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 250px"><input name="name[]" id="keterangan-{{ $k }}" value="{{ $temp->keterangan }}" style="width: 300px" type="text"></td>
                                    <td><input type="number" name="amount[]" onkeyup="total()" id="amount-{{ $k }}"></td>
                                    <td><input type="date" name="created_at[]" value="{{ date('Y-m-d') }}"></td>
                                </tr>
                                @php
                                    $k++;
                                @endphp
                                @endforeach
                            @endfor
                        @endif
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
    $('#template_id').click(function (e) {
        setTimeout(() => {
            $('.select2').select2();
        }, 2000);
    });

    function addColumnDebit(){
        var amounts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
        debit = amounts.length + 1;
        let html = `<tr>
                        <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                        <td>
                            <select class="form-control select2" id="job-${debit}" onchange="getOrder()" name="order_id[]" style="font-size:.9rem !important; width:170px">
                                <option value=""></option>
                                @foreach ($orders as $item)
                                <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
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
                        <td><input type="date" name="created_at[]" value="{{ date('Y-m-d') }}"></td>
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
            if(confirm('are you sure')){
                $('#form-submit').submit();
            }
        }
    });

    function getOrder(){
        var order_id = $("select[name='order_id[]']").map(function(){return $(this).val();}).get();
        $.ajax({
            type: "POST",
            url: "{{ url('api/get-array-id') }}",
            data: {
                id:order_id
            },
            success: function (response) {
                let html = '';
                $.each(response, function (idx, item) {
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
        $('#total_debit').html('Rp. '+total_debit);
        $('#total_credit').html('Rp. '+total_credit);
    }

    $('#apply').click(function (e) {
        setTimeout(() => {
            $('.select2').select2();
        }, 3000);
    });
    $('#addBarisTemplate').click(function (e) {
        setTimeout(() => {
            $('.select2').select2();
        }, 2000);
    });

</script>
@endpush
