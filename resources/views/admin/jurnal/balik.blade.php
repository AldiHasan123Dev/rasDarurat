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
            <div class="col-12 mt-3">
                <div class="card p-2">
                    <form action="{{ route('jurnal.balik.create') }}" method="get" class="row" id="form-submit">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                {{-- <input name="nomor" placeholder="Nomor Jurnal" style="width: 300px" type="text"> --}}
                                {{-- <button class="btn btn-info btn-sm" type="button" onclick="addColumnDebit()">Tambah Kolom</button> --}}
                                <span>Form Jurnal Balik</span>
                            </div>
                            <hr>
                            <table class="table table-sm" id="table-debit">
                                <tr>
                                    <td>ID JOB</td>
                                    <td>Akun Debet Tujuan</td>
                                    <td>Akun Credit Tujuan</td>
                                    <td>Akun Debet Baru</td>
                                    <td>Akun Credit Baru</td>
                                </tr>
                                <tr>
                                    <td>
                                        <select class="form-control select2" name="order_id" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($orders as $item)
                                            <option {{  request('order_id')==$item->id?'selected':''  }} value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control select2" name="debit_coa_id_tujuan" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option {{ request('debit_coa_id_tujuan')==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control select2" name="credit_coa_id_tujuan" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option {{ request('credit_coa_id_tujuan')==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control select2" name="debit_coa_id" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option {{ request('debit_coa_id')==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control select2" name="credit_coa_id" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option {{ request('credit_coa_id')==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100" name="draf" value="1" id="btn-save">Buat Draf</button>
                    </form>
                </div>
            </div>
            @if (request('draf'))
            <div class="col-6 mt-3">
                <div class="card p-2">
                    <span class="border-bottom border-3 border-dark fw-bold" style="font-size: 1rem">Jurnal Tujuan</span>
                    <div class="table-responsive">
                        <table class="table table-sm" style="font-size: .7rem; white-space:nowrap">
                            <thead>
                                <tr>
                                    <th>No. Jurnal</th>
                                    <th>ID Job</th>
                                    <th>Account</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->nomor }}</td>
                                        @if ($item->order)
                                            <td>{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{ $item->coa->kode }} - {{ $item->coa->nama }}</td>
                                        <td>{{  $item->debit == 0 ? '-' : number_format($item->debit,2,'.',',') }}</td>
                                        <td>{{  $item->credit == 0 ? '-' : number_format($item->credit,2,'.',',') }}</td>
                                        <td>{{ $item->nama }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6 mt-3">
                <div class="card p-2">
                    <form action="{{ route('jurnal.balik.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ request('order_id') }}">
                        <input type="hidden" name="debit_coa_id_tujuan" value="{{ request('debit_coa_id_tujuan') }}">
                        <input type="hidden" name="credit_coa_id_tujuan" value="{{ request('credit_coa_id_tujuan') }}">
                        <span class="border-bottom border-3 border-dark fw-bold" style="font-size: 1rem">Jurnal Balik</span>
                        <div class="row my-2">
                            <div class="col">
                                <input name="nomor" placeholder="Nomor Jurnal" required style="width: 100%" type="text">
                            </div>
                            <div class="col">
                                <input type="date" style="width: 100%" name="created_at" required value="{{ request('created_at') ?? date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm" style="font-size: .7rem; white-space:nowrap">
                                <thead>
                                    <tr>
                                        <th>No. Jurnal</th>
                                        <th>ID Job</th>
                                        <th>Account</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $idx => $item)
                                        <input type="hidden" value="{{ $item->order_id }}" name="jurnal[{{ $idx }}][order_id]">
                                        <input type="hidden" value="{{ $item->debit }}" name="jurnal[{{ $idx }}][debit]">
                                        <input type="hidden" value="{{ $item->credit }}" name="jurnal[{{ $idx }}][credit]">
                                        <input type="hidden" value="{{ $item->nama }}" name="jurnal[{{ $idx }}][nama]">
                                        <tr>
                                            <td>-</td>
                                            @if ($item->order)
                                                <td>{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                            @else
                                                <td>-</td>
                                            @endif
                                            @if ($item->debit==0)
                                                <input type="hidden" value="{{ $coa_credit->id }}" name="jurnal[{{ $idx }}][coa_id]">
                                                <td>{{ $coa_credit->kode }} - {{ $coa_credit->nama }}</td>
                                            @else
                                                <input type="hidden" value="{{ $coa_debit->id }}" name="jurnal[{{ $idx }}][coa_id]">
                                                <td>{{ $coa_debit->kode }} - {{ $coa_debit->nama }}</td>
                                            @endif
                                            <td>{{  $item->debit == 0 ? '-' : number_format($item->debit,2,'.',',') }}</td>
                                            <td>{{  $item->credit == 0 ? '-' : number_format($item->credit,2,'.',',') }}</td>
                                            <td>{{ $item->nama }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm mt-3" onclick="return confirm('Are you sure?')">Simpan Jurnal Balik</button>
                    </form>
                </div>
            </div>
            @endif
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

    setTimeout(() => {
            $('.select2').select2();
        }, 2000);


    $('#btn-save').click(function (e) {
        if(confirm('are you sure')){
            $('#form-submit').submit();
        }
    });

</script>
@endpush
