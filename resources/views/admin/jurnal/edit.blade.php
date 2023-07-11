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
                <form action="{{ route('jurnal.update', $data[0]) }}" method="POST" class="card p-3">
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
                    </div>
                    <table class="table table-sm mt-3" id="table-debit">
                        <tr>
                            <td>ID Job/Seal</td>
                            <td>COA</td>
                            <td>Keterangan</td>
                            <td>Debit</td>
                            <td>Credit</td>
                        </tr>
                        @foreach ($data as $i => $temp)
                            <tr>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="job-{{ $i }}" name="jurnal[{{ $temp->id }}][order_id]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($orders as $item)
                                        <option {{ $temp->order_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="coa_id-{{ $i }}" name="jurnal[{{ $temp->id }}][coa_id]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option {{ $temp->coa_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="jurnal[{{ $temp->id }}][nama]" id="nama-{{ $i }}" value="{{ $temp->nama }}" style="width: 300px" type="text"></td>
                                <td><input type="text" name="jurnal[{{ $temp->id }}][debit]" id="debit-{{ $i }}" value="{{ $temp->debit }}"></td>
                                <td><input type="text" name="jurnal[{{ $temp->id }}][credit]" id="credit-{{ $i }}" value="{{ $temp->credit }}"></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="6">
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('are you sure?')">Simpan</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.select2').select2();
    </script>
@endsection
