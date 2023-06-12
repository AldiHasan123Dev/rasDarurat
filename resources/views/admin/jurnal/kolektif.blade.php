@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
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
                    <form action="{{ route('jurnal.kolektif.store') }}" method="post" class="row" id="form-submit">
                        @csrf
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <input name="nomor" placeholder="Nomor Jurnal" style="width: 300px" type="text">
                                <button class="btn btn-info btn-sm" type="button" onclick="addColumnDebit()">Tambah Kolom</button>
                            </div>
                            <hr>
                            <table class="table table-sm" id="table-debit">
                                <tr>
                                    <td>#</td>
                                    <td>Group JOB</td>
                                    <td>Akun Debet</td>
                                    <td>Akun Credit</td>
                                    <td>Keterangan</td>
                                    <td>Nominal</td>
                                    <td>Tanggal</td>
                                </tr>
                                @for ($i = 0; $i < 2; $i++)
                                    <tr>
                                        <td><input type="checkbox" onchange="check(this)" name="id[]" id="{{ $i }}" value="{{ $i }}"></td>
                                        <td style="width: 200px">
                                            <select class="form-control select2" name="job[]" style="font-size:.9rem !important">
                                                <option value=""></option>
                                                @foreach ($job as $item)
                                                <option value="{{ $item }}">{{ $item }}</option>
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
                                        <td><input type="number" name="amount[]" id="amount-{{ $i }}"></td>
                                        <td><input type="date" name="created_at[]" value="{{ date('Y-m-d') }}"></td>
                                    </tr>
                                @endfor
                            </table>
                        </div>
                        <button type="button" class="btn btn-success btn-sm w-100" id="btn-save">Simpan Jurnal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.select2').select2();
    </script>
@endsection
@push('scripts')
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
                            <select class="form-control select2" name="job[]" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($job as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
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
