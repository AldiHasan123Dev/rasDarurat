@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="card p-3 shadow-xl">
            <form action="{{ route('jurnal.slip_totalan_sopir') }}" method="post">
                @csrf
                <input type="hidden" name="ids" id="ids">
                <div class="d-flex gap-3">
                    <input type="date" name="created_at" id="created_at" class="form-control" style="width: 15%" required>
                    <button class="btn btn-sm btn-success" type="submit">Buat Draf Jurnal</button>
                    <div style="width: 70%"></div>
                </div>
            </form>
        </div>
        <div class="card p-3 shadow my-3">
            <div class="row">
                <div class="col-12">
                    <span><b>List Slip Belum Terjurnal</b></span>
                    <hr>
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>TGL Slip</th>
                                <th>Slip</th>
                                <th>Sopir</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td><input type="checkbox" class="id" name="id" value="{{ $item->id }}" onchange="check()" id="id-{{ $item->id }}"></td>
                                    <td>{{ date('d/m/y', strtotime($item->tgl_invoice)) }}</td>
                                    <td>{{ $item->invoice }}</td>
                                    <td>{{ $item->sopir->nama }}</td>
                                    <td>{{ number_format($item->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card p-3 shadow my-3">
            <div class="row">
                <div class="col-12">
                    <span><b>List Slip Sudah Terjurnal</b></span>
                    <hr>
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>Tgl Submit</th>
                                <th>Tgl Jurnal</th>
                                <th>Jurnal</th>
                                <th>Tgl Slip</th>
                                <th>Slip</th>
                                <th>Sopir</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data1 as $item)
                                <tr>
                                    <td>{{ date('d/m/y H:i',strtotime($item->jurnal_submit)) }}</td>
                                    <td>{{ date('d/m/y',strtotime($item->jurnal_tgl)) }}</td>
                                    <td><a href="{{ route('jurnal.edit',['jurnal'=>$item->jurnal]) }}">{{ $item->jurnal }}</a></td>
                                    <td>{{ date('d/m/y', strtotime($item->tgl_invoice)) }}</td>
                                    <td>{{ $item->invoice }}</td>
                                    <td>{{ $item->sopir->nama }}</td>
                                    <td>{{ number_format($item->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function check() {
            id1 = [];
            $(".id:checked").each(function() {
                id1.push($(this).val());
            });
            console.log(id1);
            $('#ids').val(id1);
        }
    </script>
@endsection
