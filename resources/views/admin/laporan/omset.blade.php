@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<style>
    @media print{
        @page {
            size: landscape
        }
        body * {
            visibility: hidden;
        }
        body{
            width: 100%;
        }
        #print, #print * {
            visibility: visible;
            font-family: 'Open Sans', sans-serif;
            font-size: .7rem !important;
            color: black !important;
        }
        #print{
            position: absolute;
            top: -80px;
        }
        tr th, tr{
            border: 1px solid black;
        }
    }
    thead{
        position: sticky;
        z-index: 12;
        top: 0px;
        background: white;
    }
    th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
    #table th,
    #table td {
        vertical-align: middle;
        height: 20px;
        padding: 0 5px!important;
        border: 1px solid black;
        color: black;
    }
    .dataTables_scroll
    {
        overflow:auto;
        height: 400px;
    }
    thead input {
        width: 100%;
        padding: 0px;
        box-sizing: border-box;
    }
</style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-sm btn-success" onclick="window.print()"><i class="fas fa-print"></i> PRINT</button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="sync()"><i class="fas fa-print"></i> SYNC</button>
                        </div>
                        <form action="{{ url()->current() }}" method="get">
                            <div class="d-flex gap-3">
                                <select name="month" id="month" class="form-select" style="width: 150px" onchange="submit()">
                                    @foreach ($months as $idx=> $item)
                                    <option value="{{ $idx+1 }}" {{ ($idx+1)==$month?'selected':'' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                                <select name="year" id="year" class="form-select" style="width: 150px" onchange="submit()">
                                    <option {{ $year=='2023'?'selected':'' }} value="2023">2023</option>
                                    <option {{ $year=='2024'?'selected':'' }} value="2024">2024</option>
                                    <option {{ $year=='2025'?'selected':'' }} value="2025">2025</option>
                                    <option {{ $year=='2026'?'selected':'' }} value="2026">2026</option>
                                    <option {{ $year=='2027'?'selected':'' }} value="2027">2027</option>
                                </select>
                                <div>
                                    <label for="radio1">
                                        <input type="radio" name="tipe" id="radio1" value="inv" {{ $tipe=='inv'?'checked':'' }} onchange="submit()"> Periode Invoice
                                    </label>
                                    <label for="radio2">
                                        <input type="radio" name="tipe" id="radio2" value="job" {{ $tipe=='job'?'checked':'' }} onchange="submit()"> Periode JOB
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="print">
                        <div class="mt-3">
                            <table class="table table-sm table-bordered mt-3" id="table" style="font-size: .7rem">
                                <thead>
                                    <tr>
                                        <th style="min-width:40px !important">ID</th>
                                        <th style="min-width:40px !important">ORDER_ID</th>
                                        <th style="min-width:40px !important">Tanggal</th>
                                        <th style="min-width:40px !important">Invoice</th>
                                        <th style="min-width:40px !important">Group JOB</th>
                                        <th style="min-width:40px !important">ID JOB</th>
                                        <th style="min-width:40px !important">Asuransi</th>
                                        <th style="min-width:40px !important">Pembayar</th>
                                        <th style="min-width:40px !important">Marketing</th>
                                        <th style="min-width:40px !important">CS</th>
                                        <th style="min-width:40px !important">Pengirim</th>
                                        <th style="min-width:40px !important">Penerima</th>
                                        <th style="min-width:40px !important">Dari</th>
                                        <th style="min-width:40px !important">Tujuan</th>
                                        <th style="min-width:40px !important">Shipment</th>
                                        <th style="min-width:40px !important">Kondisi</th>
                                        <th style="min-width:40px !important">Barang</th>
                                        <th style="min-width:40px !important">Pelayaran</th>
                                        <th style="min-width:40px !important">Kapal</th>
                                        <th style="min-width:40px !important">Voyage</th>
                                        <th style="min-width:40px !important">ETD</th>
                                        <th style="min-width:40px !important">TD</th>
                                        <th style="min-width:40px !important">BA Kirim</th>
                                        <th style="min-width:40px !important">Nopol</th>
                                        <th style="min-width:40px !important">Trucking</th>
                                        <th style="min-width:40px !important">No Container</th>
                                        <th style="min-width:40px !important">No Seal</th>
                                        <th style="min-width:40px !important">Stuffing</th>
                                        <th style="min-width:40px !important">Tipe Stuffing</th>
                                        <th style="min-width:40px !important">Tgl Full</th>
                                        <th style="min-width:40px !important">Barang Diantar</th>
                                        <th style="min-width:40px !important">BA Kembali</th>
                                        <th style="min-width:40px !important">Koli</th>
                                        <th style="min-width:40px !important">M3</th>
                                        <th style="min-width:40px !important">Berat</th>
                                        <th style="min-width:40px !important">Satuan</th>
                                        <th style="min-width:40px !important">Unit</th>
                                        <th style="min-width:40px !important">Agen</th>
                                        <th style="min-width:40px !important">Penerima BL</th>
                                        <th style="min-width:40px !important">Trucking</th>
                                        <th style="min-width:40px !important">THC Muat</th>
                                        <th style="min-width:40px !important">THC Tujuan</th>
                                        <th style="min-width:40px !important">U# Tambang</th>
                                        <th style="min-width:40px !important">BL</th>
                                        <th style="min-width:40px !important">APBS</th>
                                        <th style="min-width:40px !important">CLEANING</th>
                                        <th style="min-width:40px !important">LSS</th>
                                        <th style="min-width:40px !important">STORAGE</th>
                                        <th style="min-width:40px !important">JASA DOOR</th>
                                        <th style="min-width:40px !important">ASURANSI</th>
                                        <th style="min-width:40px !important">OPS</th>
                                        <th style="min-width:40px !important">SEGEL</th>
                                        <th style="min-width:40px !important">OPS & SEGEL</th>
                                        <th style="min-width:40px !important">OPS & SEGEL & CLEANING</th>
                                        <th style="min-width:40px !important">BURUH</th>
                                        <th style="min-width:40px !important">CHECKER</th>
                                        <th style="min-width:40px !important">KARANTINA</th>
                                        <th style="min-width:40px !important">DEMMURAGE</th>
                                        <th style="min-width:40px !important">KRM DOK</th>
                                        <th style="min-width:40px !important">BIAYA LAIN-LAIN</th>
                                        <th style="min-width:40px !important">FLEXIBAG</th>
                                        <th style="min-width:40px !important">RC</th>
                                        <th style="min-width:40px !important">BIAYA</th>
                                        <th style="min-width:40px !important">TARIF</th>
                                        <th style="min-width:40px !important">LABA KOTOR</th>
                                        <th style="min-width:40px !important">PROSENTASE MARGIN</th>
                                    </tr>
                                    {{-- <tr>
                                        <th>Tanggal</th>
                                        <th>Invoice</th>
                                        <th>Group JOB</th>
                                        <th>ID JOB</th>
                                        <th>Asuransi</th>
                                        <th>Pembayar</th>
                                        <th>Marketing</th>
                                        <th>CS</th>
                                        <th>Pengirim</th>
                                        <th>Penerima</th>
                                        <th>Dari</th>
                                        <th>Tujuan</th>
                                        <th>Shipment</th>
                                        <th>Kondisi</th>
                                        <th>Jenis Barang</th>
                                        <th>Barang</th>
                                        <th>Pelayaran</th>
                                        <th>Kapal</th>
                                        <th>Voyage</th>
                                        <th>ETD</th>
                                        <th>TD</th>
                                        <th>BA Kirim</th>
                                        <th>Nopol</th>
                                        <th>Trucking</th>
                                        <th>No Container</th>
                                        <th>No Seal</th>
                                        <th>Stuffing</th>
                                        <th>Tipe Stuffing</th>
                                        <th>Tgl Full</th>
                                        <th>Barang Diantar</th>
                                        <th>BA Kembali</th>
                                        <th>Koli</th>
                                        <th>M3</th>
                                        <th>Berat</th>
                                        <th>Satuan</th>
                                        <th>Unit</th>
                                        <th>Tarif</th>
                                        <th>Agen</th>
                                        <th>Penerima BL</th>
                                        <th>Trucking</th>
                                        <th>THC Muat</th>
                                        <th>THC Tujuan</th>
                                        <th>U# Tambang</th>
                                        <th>BL</th>
                                        <th>APBS</th>
                                        <th>CLEANING</th>
                                        <th>LSS</th>
                                        <th>STORAGE</th>
                                        <th>JASA DOOR</th>
                                        <th>ASURANSI</th>
                                        <th>OPS</th>
                                        <th>SEGEL</th>
                                        <th>BURUH</th>
                                        <th>CHECKER</th>
                                        <th>KARANTINA</th>
                                        <th>DEMMURAGE</th>
                                        <th>KRM DOK</th>
                                        <th>BIAYA LAIN-LAIN</th>
                                        <th>FLEXIBAG</th>
                                        <th>RC</th>
                                        <th>BIAYA</th>
                                        <th>LABA KOTOR</th>
                                        <th>PROSENTASE MARGIN</th>
                                    </tr> --}}
                                </thead>
                                <tbody>
                                    @foreach ($data as $order)
                                        <tr class="table-{{ $order->omset ? ($order->omset->margin <= 0.03 ? 'warning' : '') : '' }}">
                                            <td>{{ $order->omset->id ?? null }}</td>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ date('d/m/y',strtotime($order->created_at)) }}</td>
                                            <td>{{ $order->invoice }}</td>
                                            <td>{{ $order->job }}</td>
                                            <td>{{ $order->job }}-{{ sprintf('%02d',$order->no_job) }}</td>
                                            <td>{{ $order->asuransi }}</td>
                                            <td>{{ $order->tarif->customer->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->customer->marketing->name ?? '-' }}</td>
                                            <td>{{ $order->tarif->customer->cs->name ?? '-' }}</td>
                                            <td>{{ $order->pengirim->nama ?? '-' }}</td>
                                            <td>{{ $order->penerima->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->dari_lokasi->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->tujuan_lokasi->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->shipmentInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->kondisiInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->barang->nama ?? '-' }}</td>
                                            <td>{{ $order->jadwal_kapal->pelayaran->nama ?? '-' }}</td>
                                            <td>{{ $order->jadwal_kapal->kapal->nama ?? '-' }}</td>
                                            <td>{{ $order->jadwal_kapal->voyage ?? '-' }}</td>
                                            <td>{{ $order->jadwal_kapal->etd ?? '-' }}</td>
                                            <td>{{ $order->jadwal_kapal->td ?? '-' }}</td>
                                            <td>{{ is_null($order->ba_kirim)?'-':date('d-m-Y',strtotime($order->ba_kirim)) }}</td>
                                            <td>{{ $order->nopol }}</td>
                                            <td>{{ $order->trucking }}</td>
                                            <td>{{ $order->container }}</td>
                                            <td>{{ $order->seal }}</td>
                                            <td>{{ is_null($order->stuffing)?'-':date('d-m-Y',strtotime($order->stuffing)) }}</td>
                                            <td>{{ $order->tarif->stuffing ?? '-' }}</td>
                                            <td>{{ is_null($order->full)?'-':date('d-m-Y',strtotime($order->full)) }}</td>
                                            <td>{{ is_null($order->barang_diantar)?'-':date('d-m-Y',strtotime($order->barang_diantar)) }}</td>
                                            <td>{{ is_null($order->ba_kembali)?'-':date('d-m-Y',strtotime($order->ba_kembali)) }}</td>
                                            <td>{{ $order->bttb->sum('qty') }}</td>
                                            <td>{{ $order->bttb->sum('vol') }}</td>
                                            <td>{{ $order->bttb->sum('berat') }}</td>
                                            <td>{{ $order->satuanInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->satuanInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->agen }}</td>
                                            <td>{{ $order->agen=='AGEN'?($order->agent->nama??'-'):($order->penerima_bl->nama??'-') }}</td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_trucking ?? '[]'}}')">
                                                    {{ number_format(($order->omset->trucking ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_opp ?? '[]'}}')">
                                                    {{ number_format(($order->omset->opp ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_opt ?? '[]'}}')">
                                                    {{ number_format(($order->omset->opt ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_ut ?? '[]'}}')">
                                                    {{ number_format(($order->omset->ut ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_bl ?? '[]'}}')">
                                                    {{ number_format(($order->omset->bl ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_apbs ?? '[]'}}')">
                                                    {{ number_format(($order->omset->apbs ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_cleaning ?? '[]'}}')">
                                                    {{ number_format(($order->omset->cleaning ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_lss ?? '[]'}}')">
                                                    {{ number_format(($order->omset->lss ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_storage ?? '[]'}}')">
                                                    {{ number_format(($order->omset->storage ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_jasa_door ?? '[]'}}')">
                                                    {{ number_format(($order->omset->jasa_door ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_asuransi ?? '[]'}}')">
                                                    {{ number_format(($order->omset->asuransi ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_ops ?? '[]'}}')">
                                                    {{ number_format(($order->omset->ops ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_segel ?? '[]'}}')">
                                                    {{ number_format(($order->omset->segel ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_ops_seal ?? '[]'}}')">
                                                    {{ number_format(($order->omset->ops_seal ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_ops_seal_cleaning ?? '[]'}}')">
                                                    {{ number_format(($order->omset->ops_seal_cleaning ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_buruh ?? '[]'}}')">
                                                    {{ number_format(($order->omset->buruh ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_checker ?? '[]'}}')">
                                                    {{ number_format(($order->omset->checker ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_karantina ?? '[]'}}')">
                                                    {{ number_format(($order->omset->karantina ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_demmurage ?? '[]'}}')">
                                                    {{ number_format(($order->omset->demmurage ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_kirim_dokumen ?? '[]'}}')">
                                                    {{ number_format(($order->omset->kirim_dokumen ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_biaya_lain ?? '[]'}}')">
                                                    {{ number_format(($order->omset->biaya_lain ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_flexibag ?? '[]'}}')">
                                                    {{ number_format(($order->omset->flexibag ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_rc ?? '[]'}}')">
                                                    {{ number_format(($order->omset->rc ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" onclick="showJurnal('{{ $order->omset->j_biaya ?? '[]'}}')">
                                                    {{ number_format(($order->omset->biaya ?? 0),2,',','.') }}
                                                </a>
                                            </td>
                                            <td>
                                                @php
                                                    $cbm = $order->tarif->satuanInfo->nama ?? '-';
                                                    $tarif = $order->tarif->tarif ?? 0;
                                                    if($cbm=='CBM'){
                                                        $tarif *= $order->bttb->sum('vol');
                                                    }
                                                @endphp
                                                {{ number_format(($tarif ?? 0),2,',','.') }}
                                            </td>
                                            <td>{{ number_format(($order->omset->laba_kotor ?? 0),2,',','.') }}</td>
                                            <td>{{ number_format(($order->omset->margin ?? 0),3,',','.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-jurnal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">List Jurnal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" style="font-size: .7rem">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tgl</th>
                                    <th>Nomor</th>
                                    <th>COA</th>
                                    <th>Akun</th>
                                    <th>JOB</th>
                                    <th>INV</th>
                                    <th>Cont</th>
                                    <th>Nopol</th>
                                    <th>Keterangan</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                            </thead>
                            <tbody id="list-jurnal">

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-end" colspan="10"><b>TOTAL</b></td>
                                    <td class="text-end"><b id="debit-total"></b></td>
                                    <td class="text-end"><b id="credit-total"></b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script>
        let table = $('#table').DataTable({
            fixedColumns: {
                left: 4,
                right: 0
            },
            autoWidth:false,
            paging: false,
            scrollCollapse: true,
            fixedHeader: true,
            // select: true,
            // scrollX:true,
            // scrollY: 400,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend:'excel'
                },
            ],
            search: {
                return: true
            }
        });
        table.column( 0 ).visible( false );
        table.column( 1 ).visible( false );
        jQuery('.dataTable').wrap('<div class="dataTables_scroll" />');

        function sync(){
            syncAction(0,50);
        }

        function syncAction(start,end){
            $.ajax({
                type: "POST",
                url: "{{ route('omset.sync') }}",
                data: {
                    id:@json($ids),
                    start:start,
                    end:end,
                },
                success: function (response) {
                    if(response=='complete'){
                        alert("SINKRONISASI BERHASIL!");
                        location.reload();
                    }else{
                        syncAction(response,50)
                    }
                }
            });
        }

        // table.on('select', function (e, dt, type, indexes) {
        //     let rowData = table.rows(indexes).data().toArray();
        //     console.log(rowData);
        // })

        var modal_jurnal = new bootstrap.Modal(document.getElementById('modal-jurnal'))

        function showJurnal(id){
            $.ajax({
                type: "POST",
                url: "{{ route('omset.jurnal') }}",
                data: {
                    id:id
                },
                success: function (response) {
                    let html = '';
                    let debit = 0;
                    let credit = 0;
                    $.each(response, function (idx, item) {
                        debit += item.debit_num;
                        credit += item.credit_num;
                        html += `<tr>
                                    <td>${idx+1}</td>
                                    <td>${item.created_at}</td>
                                    <td>${item.nomor}</td>
                                    <td>${item.coa_kode}</td>
                                    <td>${item.coa_nama}</td>
                                    <td>${item.no_job}</td>
                                    <td>${item.invoice}</td>
                                    <td>${item.container}</td>
                                    <td>${item.nopol}</td>
                                    <td>${item.nama}</td>
                                    <td class="text-end">${item.debit}</td>
                                    <td class="text-end">${item.credit}</td>
                                </tr>`;
                    });

                    $('#list-jurnal').html(html);
                    $('#debit-total').html(debit.toLocaleString('id-ID'));
                    $('#credit-total').html(credit.toLocaleString('id-ID'));
                    modal_jurnal.show();
                }
            });
        }
    </script>
@endsection

{{-- initComplete: function () {
    this.api()
        .columns()
        .every(function () {
            let column = this;
            let title = column.header().textContent;

            // Create input element
            let input = document.createElement('input');
            input.placeholder = title;
            column.header().replaceChildren(input);

            // Event listener for user input
            input.addEventListener('keyup', () => {
                if (column.search() !== this.value) {
                    column.search(input.value).draw();
                }
            });
        });
} --}}
