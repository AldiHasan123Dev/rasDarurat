@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <style>
        .select2.select2-container.select2-container--default {
            width: 100% !important;
        }

        tr td {
            padding: 2px 10px;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2">
                <div class="d-flex gap-2 justify-content-between">
                    <p>List Hutang Pelayaran</p>
                    <form action="{{ route('hutang-pelayaran.cetak.voucher') }}" method="post">
                        {{-- <input type="hidden" name="nama_pel" value="pelayaran"> --}}
                        <input type="hidden" name="order_id" id="order_id1">
                        <button class="py-2 px-3 btn btn-success" onclick="return confirm('are you sure?')"
                            id="generate-invoice"><i class="fas fa-print"></i> Cetak Invoice</button>
                        @csrf
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm nowrap" style="font-size: .7rem; white-space:nowrap">
                        <thead>
                            <tr>
                                <th style="width: 150px">Group JOB</th>
                                <th style="width: 30px">#</th>
                                <th>ID JOB.</th>
                                <th>Pelayaran</th>
                                <th>Container</th>
                                <th>Seal</th>
                                <th>Tarif Pelayaran</th>
                                {{-- <th>Status</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $hutpel => $orders)
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($orders as $order)
                                    @php
                                        $total += $order->jumlah;
                                    @endphp
                                    <tr>
                                        @if ($loop->first)
                                            <td style="vertical-align: middle; text-align:center"
                                                rowspan="{{ $orders->count() }}">{{ $order->order->job }}</td>
                                        @endif
                                        <td class="text-center"><input type="checkbox" name="order_id1"
                                                value="{{ $order->order->id }}"></td>
                                        <td>{{ $order->order->job }}-{{ sprintf('%02d', $order->order->no_job) }}</td>
                                        <td id="pelayaran">{{ $order->order->jadwal_kapal->pelayaran->nama }}</td>
                                        <td>{{ $order->order->container }}</td>
                                        <td>{{ $order->order->seal }}</td>
                                        <td>Rp. {{ number_format($order->jumlah ?? 0) }}</td>
                                        {{-- <td></td> --}}
                                    </tr>
                                @endforeach
                                <tr class="border-bottom border-dark">
                                    <td colspan="5" class="text-center"><b>TOTAL</b></td>
                                    <td colspan="8" class="border border-dark"><b>Rp. {{ number_format($total) }}</b>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">Tidak Ada Data!</td>
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
        let id1 = [];
        let id2 = [];
        let id3 = [];

        $('input:checkbox[name=order_id1]').change(function(e) {
            check1()
        });
        $('input:checkbox[name=order_id2]').change(function(e) {
            check2()
        });
        $('input:checkbox[name=order_id_vendor]').change(function(e) {
            check3()
        });

        function check1() {
            id1 = [];
            $("input:checkbox[name=order_id1]:checked").each(function() {
                id1.push($(this).val());
            });
            $('#order_id1').val(id1);
        }

        function check2() {
            id2 = [];
            $("input:checkbox[name=order_id2]:checked").each(function() {
                id2.push($(this).val());
            });
            $('#order_id2').val(id2);
        }

        function check3() {
            id3 = [];
            $("input:checkbox[name=order_id_vendor]:checked").each(function() {
                id3.push($(this).val());
            });
            $('#order_id_vendor').val(id3);
        }
    </script>
@endsection
