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
            <table class="table table-sm table-bordered" style="font-size:.7rem">
                <tr>
                    <td class="fw-bold">Param</td>
                    <td>[1] Pembayar (XPDC)</td>
                    <td>[2] Pengirim (XPDC)</td>
                    <td>[3] Penerima (XPDC)</td>
                    <td>[4] Pelayaran (XPDC)</td>
                    <td>[5] Customer (TRUCKING)</td>
                </tr>
                {{-- <tr>
                    <td class="fw-bold">Output</td>
                    <td id="pembayar">-</td>
                    <td id="pengirim">-</td>
                    <td id="penerima">-</td>
                    <td id="pelayaran">-</td>
                    <td id="customer">-</td>
                </tr> --}}
            </table>
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
                        <button class="btn btn-info btn-sm" type="button" onclick="addColumnDebit()">Tambah Kolom</button>
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
                                <td><input type="checkbox" onchange="check(this)" name="id[]" id="{{ $i }}" value="{{ $i }}"></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="order_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($orders as $item)
                                        <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="debit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="credit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="name[]" style="width: 300px" type="text"></td>
                                <td><input type="number" name="amount[]" onkeyup="amount('debit')" id="amount-{{ $i }}"></td>
                                <td><input type="date" name="created_at[]" value="{{ date('Y-m-d') }}"></td>
                            </tr>
                            @endfor
                        @else
                            @foreach ($template as $i => $temp)
                            <tr>
                                <td><input type="checkbox" onchange="check(this)" name="id[]" id="{{ $i }}" value="{{ $i }}"></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="order_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($orders as $item)
                                        <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="debit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}" {{ $item->id==$temp->coa_debit_id?'selected':'' }}>{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" name="credit_coa_id[]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}" {{ $item->id==$temp->coa_credit_id?'selected':'' }}>{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="name[]" value="{{ $temp->keterangan }}" style="width: 300px" type="text"></td>
                                <td><input type="number" name="amount[]" id="amount-{{ $i }}"></td>
                                <td><input type="date" name="created_at[]" value="{{ date('Y-m-d') }}"></td>
                            </tr>
                            @endforeach
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
        let html = `<tr>
                        <td><input type="checkbox" onchange="check(this)" name="id[]" id="${debit}" value="${debit}"></td>
                        <td style="width: 200px">
                            <select class="form-control select2" name="order_id[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($orders as $item)
                                <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 200px">
                            <select class="form-control select2" name="debit_coa_id[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 200px">
                            <select class="form-control select2" name="credit_coa_id[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 300px"><input name="name[]" style="width: 300px" type="text"></td>
                        <td><input type="number" name="amount[]" onkeyup="amount('debit')" id="amount-${debit}"></td>
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
        let deb = 0;
        let cre = 0;
        for (let i = 0; i < amounts.length; i++) {
            const amount = parseInt(amounts[i]);
            if(debits[i]!=''){
                deb += amount;
            }
            if(credits[i]!=''){
                cre += amount;
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

</script>
@endpush
