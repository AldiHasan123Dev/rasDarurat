@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('hutang-pelayaran.index') }}" class="btn btn-sm btn-primary">Kembali</a>
                <div class="card p-3 shadow-lg mt-2">
                    <table class="table table-bordered" style="font-size: .8rem">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode</th>
                                <th>No. Jurnal</th>
                                <th>Tanggal Cetak</th>
                                <th>Kapal</th>
                                <th>BG OPP</th>
                                <th>BG OPT</th>
                                <th>BG UT</th>
                                <th><i class="fas fa-print"></i></th>
                                {{-- <th><i class="fas fa-upload"></i></th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->first()->invoice }}</td>
                                    <td>{{ $item->first()->jurnal_opp }}; {{ $item->first()->jurnal_opt }}; {{ $item->first()->jurnal_ut }}</td>
                                    <td>{{ date('d/m/y',strtotime($item->first()->tgl_invoice)) }}</td>
                                    <td>{{ $item->first()->order->jadwal_kapal->kapal->nama ?? '-' }}</td>
                                    <td>{{ $item->first()->no_bg_opp?date('d/m/y',strtotime($item->first()->tgl_bg_opp)):'' }} - {{ $item->first()->no_bg_opp }} - {{ number_format($item->first()->nominal_bg_opp,2,',','.') }}</td>
                                    <td>{{ $item->first()->no_bg_opt?date('d/m/y',strtotime($item->first()->tgl_bg_opt)):'' }} - {{ $item->first()->no_bg_opt }} - {{ number_format($item->first()->nominal_bg_opt,2,',','.') }}</td>
                                    <td>{{ $item->first()->no_bg_ut?date('d/m/y',strtotime($item->first()->tgl_bg_ut)):'' }} - {{ $item->first()->no_bg_ut }} - {{ number_format($item->first()->nominal_bg_ut,2,',','.') }}</td>
                                    <td>
                                        <a href="{{ route('hutang-pelayaran.print',['invoice'=>$item->first()->invoice]) }}" class="btn btn-sm btn-success">Print</a>
                                    </td>
                                    {{-- <td>
                                        <form action="{{ route('hutang-pelayaran.tarik') }}" method="post">
                                            @csrf
                                            <button type="submit" name="invoice" value="{{ $item->first()->invoice }}" onclick="return confirm('are you sure?')" class="btn btn-sm btn-danger">Tarik</button>
                                        </form>
                                    </td> --}}
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
        $('table').dataTable();
    </script>
@endsection
