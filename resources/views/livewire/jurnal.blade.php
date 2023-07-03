<div>
    <div class="col-6">
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
                            <th class="fw-bold">Param</th>
                            <th>[1] ID JOB</th>
                            <th>[2] Cont</th>
                            <th>[3] Seal</th>
                            <th>[4] Shipment</th>
                            <th>[5] Pembayar (XPDC)</th>
                            <th>[6] Pengirim (XPDC)</th>
                            <th>[7] Penerima (XPDC)</th>
                            <th>[8] Pelayaran (XPDC)</th>
                            <th>[9] Customer (TRUCKING)</th>
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
                    <div class="d-flex justify-content-between">
                        <input name="nomor" placeholder="Nomor Jurnal" required style="width: 300px" type="text">
                        <div class="d-flex gap-2">
                            <button class="btn btn-info btn-sm" type="button" id="addBarisTemplate" wire:click="addBarisTemplate">Tambah Baris Template</button>
                            <button class="btn btn-info btn-sm" type="button" onclick="addColumnDebit()">Tambah Baris</button>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-sm" id="table-debit">
                        <tr>
                            <td>#</td>
                            <td>ID Job/Seal</td>
                            <td>Akun Debet</td>
                            <td>Akun Credit</td>
                            <td>Keterangan</td>
                            <td>Nominal</td>
                            <td>Tanggal</td>
                        </tr>
                        @if (is_null($template))
                            @for ($i = 0; $i < $debit_idx; $i++)
                            <tr>
                                <td><input type="checkbox" name="id[]" onchange="uncheck({{ $i }})" checked id="{{ $i }}" value="{{ $i }}"></td>
                                <td style="width: 200px">
                                    <select class="form-control selecttize" id="job-{{ $i }}" onchange="getOrder()" name="order_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($orders as $item)
                                        <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control selecttize" id="debit-{{ $i }}" name="debit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control selecttize" id="credit-{{ $i }}" name="credit_coa_id[]" style="font-size:.9rem !important">
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
                                @foreach ($template as $i => $temp)
                                <tr>
                                    <td><input type="checkbox" name="id[]" onchange="uncheck({{ $k }})" checked id="{{ $k }}" value="{{ $k }}"></td>
                                    <td style="width: 200px">
                                        <select class="form-control selecttize" id="job-{{ $k }}" name="order_id[]" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($orders as $item)
                                            <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control selecttize" id="debit-{{ $k }}" name="debit_coa_id[]" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option value="{{ $item->id }}" {{ $item->id==$temp->coa_debit_id?'selected':'' }}>{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control selecttize" id="credit-{{ $k }}" name="credit_coa_id[]" style="font-size:.9rem !important">
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
    $('.selecttize').selectize({
        sortField: 'text'
    });
    let credit = 2;
    let debit = 2;
    $('.select2').select2();
    $('#reset').click(function (e) {
        location.reload();
    });
    $('#template_id').click(function (e) {
        setTimeout(() => {
            $('.select2').select2();
            $('.selecttize').selectize({
                sortField: 'text'
            });
        }, 2000);
    });

    function addColumnDebit(){
        let html = `<tr>
                        <td><input type="checkbox" name="id[]" onchange="uncheck(${debit})" checked id="${debit}" value="${debit}"></td>
                        <td style="width: 200px">
                            <select class="form-control selecttize" id="job-${debit}" name="order_id[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($orders as $item)
                                <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 200px">
                            <select class="form-control selecttize" id="debit-${debit}" name="debit_coa_id[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 200px">
                            <select class="form-control selecttize" id="credit-${debit}" name="credit_coa_id[]" style="font-size:.9rem !important">
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
            $('.selecttize').selectize({
                sortField: 'text'
            });
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
                        <td>#</td>
                        <td>${item.no}</td>
                        <td>${item.container}</td>
                        <td>${item.seal}</td>
                        <td>${item.shipment}</td>
                        <td>${item.pembayar}</td>
                        <td>${item.pengirim}</td>
                        <td>${item.penerima}</td>
                        <td>${item.pelayaran}</td>
                        <td>${item.customer_trucking}</td>
                    </tr>
                    `
                });

                $('#table-order').html(html);
            }
        });
    }

    function uncheck (id) {
        $('#job-'+id).val('').trigger('change');
        $('#debit-'+id).val('').trigger('change');
        $('#credit-'+id).val('').trigger('change');
        $('#keterangan-'+id).val('');
        $('#amount-'+id).val('');
    }

    $('#apply').click(function (e) {
        setTimeout(() => {
            $('.select2').select2();
            $('.selecttize').selectize({
                sortField: 'text'
            });
        }, 3000);
    });
    $('#addBarisTemplate').click(function (e) {
        setTimeout(() => {
            $('.select2').select2();
            $('.selecttize').selectize({
                sortField: 'text'
            });
        }, 3000);
    });
</script>
@endpush
