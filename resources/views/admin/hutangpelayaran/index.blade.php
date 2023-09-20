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
                        <input type="hidden" name="order_id" id="order_id">
                        <button class="py-2 px-3 btn btn-success" onclick="return confirm('are you sure?')"
                            id="generate-invoice"><i class="fas fa-print"></i> Buat BBK</button>
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
                                <th>Kapal</th>
                                <th>Voyage</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Container</th>
                                <th>Seal</th>
                                <th>Tarif Pelayaran</th>
                                {{-- <th>Status</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $first = true;
                            @endphp
                            @forelse ($data as $hutpel => $orders)
                                <tr style="height: 30px; border:2px solid black; vertical-align:middle">
                                    <td colspan="11" class="text-center fw-bold text-uppercase">{{ $orders->first()->nama }}</td>
                                </tr>
                                @foreach ($orders->groupBy('job') as $order)
                                    @foreach ($order as $item)
                                        <tr class="{{ $item->tipe=='bongkaran'?'table-info':'' }}">
                                            @if ($loop->first)
                                            <td style="vertical-align: middle; text-align:center" rowspan="{{ $order->count() }}">
                                                <input type="checkbox" id="g-{{ $order->first()->job }}" onchange="checkGroup('{{ $order->first()->job }}')"> {{ $order->first()->job }}
                                            </td>
                                            @php
                                                $first = false;
                                            @endphp
                                            @endif
                                            <td class="text-center"><input onchange="individualCheck('{{ $item->job }}')" type="checkbox" class="c-{{ $item->job }}" name="order_id" value="{{ $item->id }}"></td>
                                            <td>{{ $item->job }}-{{ sprintf('%02d', $item->no_job) }}</td>
                                            <td id="pelayaran">{{ $item->nama }}</td>
                                            <td>{{ $item->nama_kapal }}</td>
                                            <td>{{ $item->voyage }}</td>
                                            <td>{{ $item->dari }}</td>
                                            <td>{{ $item->tujuan }}</td>
                                            <td>{{ $item->container }}</td>
                                            <td>{{ $item->seal }}</td>
                                            <td id="tarif-{{ $item->id }}">
                                                @if ($item->is_lock==0)
                                                    <div class="d-flex">
                                                        <select class="form-selects" id="select-tarif-{{ $item->id }}">
                                                            <option value="">-</option>
                                                            @foreach ($item->tarifPelayaranHutang($item->pelayaran_id,$item->dari,$item->tujuan) as $tarif)
                                                                <option value="{{ $tarif->tarif }}">{{ number_format($tarif->tarif) }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button class="text-success border-white" onclick="lock({{ $item->id }})"><i class="fas fa-lock"></i></button>
                                                    </div>
                                                @else
                                                    {{ number_format($item->ut) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                @php
                                    $first = true;
                                @endphp
                                {{-- <tr class="border-bottom border-dark">
                                    <td colspan="5" class="text-center"><b>TOTAL</b></td>
                                    <td colspan="8" class="border border-dark"><b>Rp. {{ number_format($total) }}</b>
                                    </td>
                                </tr> --}}
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

        $('input:checkbox[name=order_id]').change(function(e) {
            check()
        });

        function check() {
            id1 = [];
            $("input:checkbox[name=order_id]:checked").each(function() {
                id1.push($(this).val());
            });
            $('#order_id').val(id1);
        }

        function checkGroup(job){
            if ($('#g-'+job).is(':checked')) {
                $(".c-"+job).prop('checked',true);
            } else {
                $(".c-"+job).prop('checked',false);
            }
            check()
        }

        function individualCheck(job){
            if ($(".c-"+job+":checked").length == $(".c-"+job+"").length) {
                $("#g-"+job).prop('checked', true);
            } else {
                $("#g-"+job).prop('checked', false);
            }
            check()
        }

        function lock(id){
            if(confirm('are you sure')){
                let val = parseInt($('#select-tarif-'+id).val());
                $.ajax({
                    type: "POST",
                    url: "{{ route('api.hutang-pelayaran.update') }}",
                    data: {
                        order_id:id,
                        jumlah:val,
                        ut:val,
                        is_lock:1,
                    },
                    success: function (response) {
                        $('#tarif-'+id).html(val.toLocaleString('id-ID'));
                    }
                });
            }
        }
    </script>
@endsection
