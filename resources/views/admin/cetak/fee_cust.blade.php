@extends('layouts.admin')
@section('style')
    <style>
        @media print {
            /* @page {size: landscape} */
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');

            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
            }

            .first-page{
                width: 100%;
                height: 100%;
                position: absolute;
                top: -180px;
            }
            #print, #print * {
                visibility: visible;
                font-size: .7rem !important;
            }
            #print {
                width: 100%;
                position: relative;
                left: 0;
                /* top: -20px; */
            }
            #table td, #table th{
                border: 1px solid black;
            }
            #print {
                color: #000;
            }
            /* .table tr td{
                padding: 0px 2px;
                border-left: 1px solid !important;
                border-right: 1px solid !important;
                border-bottom: none;
                border-top: none;
            }
            .table>tbody>tr>td:first-child{
                padding: 0px 2px !important;
            } */
            .table-responsive{
                overflow: visible;
            }
            .page-break {
                page-break-after: always;
                overflow:hidden;
            }
        }
        tr.heading td{
            border: 1px solid black;
            text-align: center;
        }
        .table tr td{
            vertical-align: middle;
            padding: 3px 3px;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="card p-3 shadow">
            @if (is_null($order->komisi_print))
            <div class="d-flex" style="gap:5px">
                <a href="{{ route('keuangan.fee_cust') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
                <form action="{{ route('keuangan.fee_cust.bayar') }}" method="post">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ implode(',',$id) }}">
                    <input type="hidden" name="komisi_print" value="{{ date('Y-m-d') }}">
                    <button type="submit" onclick="return confirm('Apa anda yakin?')" class="btn btn-sm btn-success mb-3">Submit Tanggal Komisi Print</button>
                </form>
            </div>
            @else
            <script>
                window.print();
            </script>
            <div class="d-flex gap-3">
                <a href="{{ route('keuangan.fee_cust') }}" class="btn btn-sm btn-secondary mb-3">Kembali</a>
                <button type="button" onclick="window.print()" class="btn btn-sm btn-success mb-3">Print</button>
            </div>
            @endif
        </div>
        <div class="card p-3 mt-3">
            <div id="print">
                <div class="invoice-box first-page">
                    <div class="row mt-3">
                        <div class="col-6">
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 120px">Tanggal</td>
                                    <td style="width:5px">:</td>
                                    <td>{{ $order->komisi_print ? date('d/m/Y',strtotime($order->komisi_print)):'-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="mt-2 w-100 table table-sm nowrap" style="font-size: .7rem; white-space:nowrap">
                            <thead>
                                <tr>
                                    <th>ID JOB</th>
                                    <th>Invoice</th>
                                    <th>Pembayar</th>
                                    {{-- <th>Pelayaran</th> --}}
                                    <th>Shippment</th>
                                    {{-- <th>Kapal</th> --}}
                                    {{-- <th>Voyage</th> --}}
                                    <th>Container</th>
                                    <th>Tgl inv dibayar</th>
                                    <th>Tgl Komisi</th>
                                    <th>Fee</th>
                                </tr>
                            </thead>
                            @foreach ($orders as $item)
                                <tr>
                                    <td>{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }}</td>
                                    <td>{{ $item->invoice ?? '-' }}</td>
                                    <td>{{ $item->tarif->customer->nama ?? '-' }}</td>
                                    {{-- <td>{{ $item->jadwal_kapal->pelayaran->nama ?? '-' }}</td> --}}
                                    <td>{{ $item->tarif->shipmentInfo->nama ?? '-' }}</td>
                                    {{-- <td>{{ $item->jadwal_kapal->kapal->nama ?? '-' }}</td> --}}
                                    {{-- <td>{{ $item->jadwal_kapal->voyage ?? '-' }}</td> --}}
                                    <td>{{ $item->container ?? '-' }}</td>
                                    <td>{{ is_null($item->invoice_bayar) ? '-' : date('d/m/y',strtotime($item->invoice_bayar)) }}</td>
                                    <td>{{ is_null($item->tgl_komisi) ? '-' : date('d/m/y',strtotime($item->tgl_komisi)) }}</td>
                                    <td>{{ number_format($item->komisi) }}</td>
                                </tr>
                            @endforeach
                            <tr style="height: 20px !important">
                                <td colspan="7" style="border-bottom: 1px solid black"></td>
                            </tr>
                            <tr class="border-bottom border-dark">
                                <td colspan="4"></td>
                                <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                <td class="fw-bold">
                                    <div class="price d-flex justify-content-between px-2">
                                        <span>Rp</span>
                                        <span>{{ number_format($orders->sum('komisi')) }}</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
