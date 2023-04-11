@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
<style>
    .select2.select2-container.select2-container--default{
        width: 100% !important;
    }
    tr td{
        padding: 2px 10px;
    }
</style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <div class="d-flex gap-2">
                    <form action="{{ route('trucking.invoice.total_sopir') }}" method="post">
                        <input type="hidden" name="order_id" id="order_id">
                        <button class="py-2 px-3 btn btn-success" id="generate-invoice"><i class="fas fa-print"></i> Buat Invoice Totalan Sopir</button>
                        @csrf
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" style="font-size: .7rem; white-space:nowrap">
                        <thead>
                            <tr>
                                <th style="width: 150px">Sopir</th>
                                <th style="width: 30px">#</th>
                                <th>Tanggal Muat</th>
                                <th>SJ Diterima FA</th>
                                <th>Customer</th>
                                <th>Container / Seal</th>
                                <th>Simpanan Sopir</th>
                                <th>Simpanan Kuli</th>
                                <th>TB/TL</th>
                                <th>Stappel</th>
                                <th>Lain-lain</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $orders)
                                @foreach ($orders as $order)
                                    <tr>
                                        @if ($loop->first)
                                            <td style="vertical-align: middle; text-align:center" rowspan="{{ $orders->count() }}">{{ $order->sopir->nama }}</td>
                                        @endif
                                        <td class="text-center"><input type="checkbox" name="order_id" value="{{ $order->id }}"></td>
                                        <td class="text-center">{{ date('d/m/y', strtotime($order->tgl_muat)) }}</td>
                                        <td class="text-center">{{ date('d/m/y', strtotime($order->sj_kembali_fa)) }}</td>
                                        <td>{{ $order->customer->nama }}</td>
                                        <td>{{ $order->container }} / {{ $order->seal }}</td>
                                        <td>{{ number_format($order->simpanan) }}</td>
                                        <td>{{ number_format($order->simpanan_kuli) }}</td>
                                        <td>{{ number_format($order->tb_tl) }}</td>
                                        <td>{{ number_format($order->stappel) }}</td>
                                        <td>{{ number_format($order->lain_lain) }}</td>
                                        <td>{{ number_format($order->total_sopir) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="border-bottom border-dark">
                                    <td colspan="5" class="text-center"><b>TOTAL</b></td>
                                    <td colspan="7" class="border border-dark"><b>Rp. {{ number_format($orders->sum('total_sopir')) }}</b></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">Tidak Ada Data!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let id = [];

        $('input:checkbox[name=order_id]').change(function (e) {
            check()
        });

        function check() {
            id = [];
            $("input:checkbox[name=order_id]:checked").each(function(){
                id.push($(this).val());
            });
            $('#order_id').val(id);
        }
    </script>
@endsection
