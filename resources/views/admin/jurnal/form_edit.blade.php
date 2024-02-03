@extends('layouts.iframe')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/selectize.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/selectize.bootstrap5.css') }}">
@endsection
@section('content')
<form action="{{ route('jurnal.update.one',$jurnal) }}" method="post">
    @csrf
    @method('PUT')
    <div class="row">
        @if ($tipe=='xpdc')
            <div class="col-12 mb-3">
                <label for="order_id">JOB</label>
                <select class="form-control select2" id="job-{{ $jurnal->id }}" name="order_id" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders as $item)
                    <option {{ $jurnal->order_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if ($tipe=='trucking')
            <div class="col-12 mb-3">
                <label for="order_id">Trucking</label>
                <select class="form-control select2" id="job-{{ $jurnal->id }}" name="order_trucking_id" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders as $item)
                        <option {{ $jurnal->order_trucking_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->container }} - {{ $item->seal }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-12 mb-3">
            <label for="coa_id">COA</label>
            <select class="form-control select2" id="coa_id-{{ $jurnal->id }}" name="coa_id" style="font-size:.9rem !important">
                <option value=""></option>
                @foreach ($coa as $item)
                <option {{ $jurnal->coa_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 mb-3">
            <label for="order_id">No BG</label>
            <select class="form-control select2" id="bg-{{ $jurnal->id }}" name="no_bg" style="font-size:.9rem !important">
                <option value=""></option>
                @foreach ($bgs as $item)
                <option {{ $jurnal->no_bg==$item?'selected':'' }} value="{{ $item }}">{{ $item }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 mb-3">
            <label for="nama">Keterangan</label>
            <input class="form-control" onclick="this.select()" name="nama" id="nama-{{ $jurnal->id }}" value="{{ $jurnal->nama }}" type="text">
        </div>
        <div class="col-12 mb-3">
            <label for="debit">Debit</label>
            <input class="form-control" onclick="this.select()" type="text" onkeyup="total()" name="debit" id="debit-{{ $jurnal->id }}" value="{{ $jurnal->debit }}">
        </div>
        <div class="col-12 mb-3">
            <label for="credit">Credit</label>
            <input class="form-control" onclick="this.select()" type="text" onkeyup="total()" name="credit" id="credit-{{ $jurnal->id }}" value="{{ $jurnal->credit }}">
        </div>
        <div class="col-12 mb-3">
            <button type="submit" class="btn btn-success w-100">Simpan</button>
        </div>
    </div>
</form>
@endsection
@section('script')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('.select2').select2();
    </script>
@endsection
