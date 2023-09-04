@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/selectize.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/selectize.bootstrap5.css') }}">
<style>
    input{
        font-size: .7rem;
    }
    select{
        font-size: .7rem;
        width: 200px;
    }
</style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
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
            <div class="col-12 mt-2">
                <div class="card p-3" id="form-jurnal">
                    <form action="{{ route('jurnal.update', $data[0]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <span>EDIT JURNAL</span>
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <label for="tipe_jurnal">Nomor Jurnal</label>
                                <input type="text" name="nomor" id="nomor" class="form-control" disabled value="{{ $data[0]->nomor }}">
                            </div>
                            <div class="col-4">
                                <label for="created_at">Tanggal Jurnal</label>
                                <input type="date" name="created_at" id="created_at" value="{{ date('Y-m-d',strtotime($data[0]->created_at)) }}" class="form-control">
                            </div>
                            <div class="col-2">
                                <button class="btn btn-info btn-sm mx-2 mt-3" type="submit" onclick="return confirm('are you sure?')">Simpan Tanggal</button>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-info btn-sm mx-2 mt-3" type="button" onclick="addColumnDebit()">Tambah Baris</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-sm mt-3">
                        <tbody id="table-debit">
                            <tr>
                                <td>#</td>
                                @if ($tipe=='xpdc')
                                    <td>ID Job/Seal</td>
                                @endif
                                @if ($tipe=='trucking')
                                    <td>Cont / Seal</td>
                                @endif
                                <td>COA</td>
                                <td>Keterangan</td>
                                <td>Debit</td>
                                <td>Credit</td>
                            </tr>
                            @foreach ($data as $i => $temp)
                                <tr>
                                    <td style="width: 50px"><input id="{{ $temp->id }}" type="checkbox" onchange="uncheck(this,{{ $temp->id }})" name="id[]" value="{{ $temp->id }}" checked></td>
                                    @if ($tipe=='xpdc')
                                        <td style="width: 200px">
                                            <select class="form-control select2" id="job-{{ $temp->id }}" name="jurnal[{{ $temp->id }}][order_id]" style="font-size:.9rem !important">
                                                <option value=""></option>
                                                @foreach ($orders as $item)
                                                <option {{ $temp->order_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endif
                                    @if ($tipe=='trucking')
                                        <td style="width: 200px">
                                            <select class="form-control select2" id="job-{{ $temp->id }}" name="jurnal[{{ $temp->id }}][order_trucking_id]" style="font-size:.9rem !important">
                                                <option value=""></option>
                                                @foreach ($orders as $item)
                                                    <option {{ $temp->order_trucking_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->container }} - {{ $item->seal }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endif
                                    <td style="width: 200px">
                                        <select class="form-control select2" id="coa_id-{{ $temp->id }}" name="jurnal[{{ $temp->id }}][coa_id]" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option {{ $temp->coa_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 300px"><input name="jurnal[{{ $temp->id }}][nama]" id="nama-{{ $temp->id }}" value="{{ $temp->nama }}" style="width: 300px" type="text"></td>
                                    <td><input type="text" onkeyup="total()" name="jurnal[{{ $temp->id }}][debit]" id="debit-{{ $temp->id }}" value="{{ $temp->debit }}"></td>
                                    <td><input type="text" onkeyup="total()" name="jurnal[{{ $temp->id }}][credit]" id="credit-{{ $temp->id }}" value="{{ $temp->credit }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tr>
                            <td colspan="6">
                                <button type="button" class="btn btn-sm btn-success" id="btn-save">Simpan</button>
                            </td>
                        </tr>
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
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.select2').select2();
        var total_credit = 0;
        var total_debit = 0;
        function uncheck (e,id) {
            if($('#' + id).is(":checked")){
                $('#job-'+id).attr('disabled',false);
                $('#coa_id-'+id).attr('disabled',false);
                $('#nama-'+id).attr('disabled',false)
                $('#amount-'+id).attr('disabled',false)
                $('#debit-'+id).attr('disabled',false)
                $('#credit-'+id).attr('disabled',false)
            }else{
                $('#job-'+id).attr('disabled',true);
                $('#coa_id-'+id).attr('disabled',true);
                $('#nama-'+id).attr('disabled',true)
                $('#amount-'+id).attr('disabled',true)
                $('#debit-'+id).attr('disabled',true)
                $('#credit-'+id).attr('disabled',true)
            }
            total();
        }

        function addColumnDebit(){
            let tipe = @json($tipe);
            var amounts = $("input[name='id[]']").map(function(){return $(this).val();}).get();
            debit = amounts.length + 1;
            let html;
            if(tipe=='trucking'){
                html = `<tr>
                                <td style="width: 50px"><input id="add-${debit}" type="checkbox" onchange="uncheck(this,${debit})" name="id[]" value="${debit}" checked></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="add-job-id-${debit}" name="jurnal_create[${debit}][order_trucking_id]" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($orders as $item)
                                                <option value="{{ $item->id }}">{{ $item->container }} - {{ $item->seal }}</option>
                                            @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="add-coa_id-${debit}" name="jurnal_create[${debit}][coa_id]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="jurnal_create[${debit}][nama]" id="add-nama-${debit}" style="width: 300px" type="text"></td>
                                <td><input type="text" onkeyup="total()" value="0" name="jurnal_create[${debit}][debit]" id="debit-${debit}"></td>
                                <td><input type="text" onkeyup="total()" value="0" name="jurnal_create[${debit}][credit]" id="credit-${debit}"></td>
                            </tr>`;
            }else{
                html = `<tr>
                                <td style="width: 50px"><input id="add-${debit}" type="checkbox" onchange="uncheck(this,${debit})" name="id[]" value="${debit}" checked></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="add-job-${debit}" name="jurnal_create[${debit}][order_id]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($orders as $item)
                                        <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="add-coa_id-${debit}" name="jurnal_create[${debit}][coa_id]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="jurnal_create[${debit}][nama]" id="add-nama-${debit}" style="width: 300px" type="text"></td>
                                <td><input type="text" onkeyup="total()" value="0" name="jurnal_create[${debit}][debit]" id="debit-${debit}"></td>
                                <td><input type="text" onkeyup="total()" value="0" name="jurnal_create[${debit}][credit]" id="credit-${debit}"></td>
                            </tr>`;

            }
            $('#table-debit').append(html);
            setTimeout(() => {
                $('.select2').select2();
            }, 1000);
            debit++;
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
                var d = parseInt($('#debit-'+item).val());
                var c = parseInt($('#credit-'+item).val());
                if(d!=""){
                    total_debit+=d;
                }
                if(c!=""){
                    total_credit+=c;
                }
            }
            $('#total_debit').html('Rp. '+total_debit.toLocaleString('en-US'));
            $('#total_credit').html('Rp. '+total_credit.toLocaleString('en-US'));
        }

        $('#btn-save').click(function (e) {
            if(total_debit!=total_credit){
                alert('Jurnal Tidak Balance debit = '+total_debit+' & credit = '+total_credit+' ! Harap check lagi')
            }else{
                if(confirm('are you sure')){
                    $('#form-jurnal').submit();
                }
            }
        });
    </script>
@endsection
