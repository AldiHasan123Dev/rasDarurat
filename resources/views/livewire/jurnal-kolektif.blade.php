<div>
    <div class="col-12">
        <h4>FORM JURNAL GROUP JOB</h4>
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
                        <div class="col-6">
                            <label for="tipe_jurnal">Tipe Jurnal</label>
                            <select name="tipe" id="tipe_jurnal" class="form-control">
                                @if ($template)
                                    <option selected value="{{ $template->tipe }}">{{ $template->tipe }}</option>
                                @else
                                    <option selected value="JNL">Jurnal Umum</option>
                                    <option value="BBK">Bank Keluar</option>
                                    <option value="BBM">Bank Masuk</option>
                                    <option value="BKK">Kas Keluar</option>
                                    <option value="BKM">Kas Masuk</option>
                                @endif
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
                            <td>#</td>
                            <td>JOB</td>
                            <td>Akun Debet</td>
                            <td>Akun Credit</td>
                            <td>Keterangan</td>
                            <td>Nominal</td>
                            <td>Tanggal</td>
                        </tr>
                        @if (is_null($template))
                            @for ($i = 0; $i < $debit_idx; $i++)
                            <tr>
                                <td><input type="checkbox" name="id[]" onchange="uncheck(this,{{ $i }})" checked id="{{ $i }}" value="{{ $i }}"></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="job-{{ $i }}" name="job[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($orders as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="debit-{{ $i }}" name="debit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="credit-{{ $i }}" name="credit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="name[]" id="keterangan-{{ $i }}" style="width: 300px" type="text"></td>
                                <td><input type="number" name="amount[]" id="amount-{{ $i }}"></td>
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
                                    <td style="width: 200px">
                                        <select class="form-control select2" id="job-{{ $k }}" name="job[]" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($orders as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control select2" id="debit-{{ $k }}" name="debit_coa_id[]" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option value="{{ $item->id }}" {{ $item->id==$temp->coa_debit_id?'selected':'' }}>{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control select2" id="credit-{{ $k }}" name="credit_coa_id[]" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option value="{{ $item->id }}" {{ $item->id==$temp->coa_credit_id?'selected':'' }}>{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 300px"><input name="name[]" id="keterangan-{{ $k }}" value="{{ $temp->keterangan }}" style="width: 300px" type="text"></td>
                                    <td><input type="number" name="amount[]" id="amount-{{ $k }}"></td>
                                    <td><input type="date" name="created_at[]" value="{{ date('Y-m-d') }}"></td>
                                </tr>
                                @php
                                    $k++;
                                @endphp
                                @endforeach
                            @endfor
                        @endif
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
        debit = amounts + 1;
        let html = `<tr>
                        <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                        <td style="width: 200px">
                            <select class="form-control select2" id="job-${debit}" name="job[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($orders as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 200px">
                            <select class="form-control select2" id="debit-${debit}" name="debit_coa_id[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 200px">
                            <select class="form-control select2" id="credit-${debit}" name="credit_coa_id[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 300px"><input name="name[]" id="keterangan-${debit}" style="width: 300px" type="text"></td>
                        <td><input type="number" name="amount[]" id="amount-${debit}"></td>
                        <td><input type="date" name="created_at[]" value="{{ date('Y-m-d') }}"></td>
                    </tr>`;
        $('#table-debit').append(html);
        setTimeout(() => {
            $('.select2').select2();
        }, 1000);
        debit++;
    }

    $('#btn-save').click(function (e) {
        var debits = $("select[name='debit_coa_id[]']").map(function(){return $(this).val();}).get();
        var credits = $("select[name='credit_coa_id[]']").map(function(){return $(this).val();}).get();
        var amounts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
        var ids = $("input[name='id[]']").map(function(){return $(this).val();}).get();
        let deb = 0;
        let cre = 0;
        for (let i = 0; i < amounts.length; i++) {
            const amount = parseInt(amounts[i]);
            if (isNaN(amount)) {
                if(debits[i]!=''){
                    deb += amount;
                }
                if(credits[i]!=''){
                    cre += amount;
                }
            }
        }

        if(deb!=cre){
            alert('Jurnal Tidak Balance debit = '+deb+' & credit = '+cre+' ! Harap check lagi')
        }else{
            if(confirm('are you sure')){
                $('#form-submit').submit();
            }
        }
    });

    function getOrder(){
        var order_id = $("select[name='job[]']").map(function(){return $(this).val();}).get();
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
