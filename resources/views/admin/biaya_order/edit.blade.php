@extends('layouts.iframe')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('order_biaya.update',$order) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-6 col-md-3 mb-2">
                            <label for="tgl_dcf">Tanggal DO & Lolo Meratus</label>
                            <input type="date" name="tgl_dcf" id="tgl_dcf" class="form-control" value="{{ $order->tgl_dcf }}">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label for="nominal_do">Nominal DO & Lolo Meratus</label>
                            <input type="number" onclick="this.select()" name="nominal_do" id="nominal_do" class="form-control" value="{{ $order->nominal_do }}">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label for="nominal_cleaning">Nominal Cleaning</label>
                            <input type="number" onclick="this.select()" name="nominal_cleaning" id="nominal_cleaning" class="form-control" value="{{ $order->nominal_cleaning }}">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label for="nominal_fee">Nominal Fee</label>
                            <input type="number" onclick="this.select()" name="nominal_fee" id="nominal_fee" class="form-control" value="{{ $order->nominal_fee }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3 mb-2">
                            <label for="tgl_opt">Tanggal OPT</label>
                            <input type="date" name="tgl_opt" id="tgl_opt" class="form-control" value="{{ $order->tgl_opt }}">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label for="nominal_opt">Nominal OPT</label>
                            <input type="number" onclick="this.select()" name="nominal_opt" id="nominal_opt" class="form-control" value="{{ $order->nominal_opt }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3 mb-2">
                            <label for="tgl_truk">Tanggal Truk</label>
                            <input type="date" name="tgl_truk" id="tgl_truk" class="form-control" value="{{ $order->tgl_truk }}">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label for="nominal_truk">Nominal Truk</label>
                            <input type="number" onclick="this.select()" name="nominal_truk" id="nominal_truk" class="form-control" value="{{ $order->nominal_truk }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3 mb-2">
                            <label for="tgl_kuli">Tanggal Kuli</label>
                            <input type="date" name="tgl_kuli" id="tgl_kuli" class="form-control" value="{{ $order->tgl_kuli }}">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label for="nominal_kuli">Nominal Kuli</label>
                            <input type="number" onclick="this.select()" name="nominal_kuli" id="nominal_kuli" class="form-control" value="{{ $order->nominal_kuli }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3 mb-2">
                            <label for="tgl_jc">Tanggal JC</label>
                            <input type="date" name="tgl_jc" id="tgl_jc" class="form-control" value="{{ $order->tgl_jc }}">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label for="nominal_jc">Nominal JC</label>
                            <input type="number" onclick="this.select()" name="nominal_jc" id="nominal_jc" class="form-control" value="{{ $order->nominal_jc }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('are you sure?')">Simpan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
