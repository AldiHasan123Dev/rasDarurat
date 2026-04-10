@extends('layouts.admin')
@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/selectize.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/selectize.bootstrap5.css') }}">
    <style>
        @media print {
            @page {
                size: landscape
            }

            body * {
                visibility: hidden;
            }

            body {
                width: 100%;
            }

            #print,
            #print * {
                visibility: visible;
                font-family: 'Open Sans', sans-serif;
                font-size: .7rem !important;
                color: black !important;
            }

            #print {
                position: absolute;
                top: -80px;
            }

            tr th,
            tr {
                border: 1px solid black;
            }
        }

        thead {
            position: sticky;
            z-index: 12;
            top: 0px;
            background: white;
        }

        th {
            text-transform: uppercase;
        }

        th,
        td {
            white-space: nowrap;
        }

        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }

        #table th,
        #table td {
            vertical-align: middle;
            height: 20px;
            padding: 0 5px !important;
            border: 1px solid black;
            color: black;
        }

        .dataTables_scroll {
            overflow: auto;
            height: 400px;
        }

        thead input {
            width: 100%;
            padding: 0px;
            box-sizing: border-box;
        }

        .bg-warning1 {
            background-color: #f3ff0dfc;
        }

        /* Untuk baris utama */
        #table tbody tr.selected1>td {
            /* Dikosongkan agar tidak ganggu inline style dari PHP */
        }

        #table tbody tr.selected1:hover>td {
            /* Dikosongkan juga */
        }

        /* Tetap dipakai jika kamu butuh FixedColumns/FixedHeader */
        div.DTFC_LeftWrapper .DTFC_Cloned tbody tr.selected1 td,
        div.DTFC_RightWrapper .DTFC_Cloned tbody tr.selected1 td,
        table.dataTable.fixedHeader-floating tbody tr.selected1 td,
        table.dataTable tbody>tr.selected1,
        table.dataTable tbody>tr.selected1 td {
            /* Ini bisa kamu hapus juga kalau mau full kontrol di JS */
            /* background-color: #adf8dc !important; */
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
                            <button type="button" class="btn btn-sm btn-success" onclick="window.print()"><i
                                    class="fas fa-print"></i> PRINT</button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="sync()"> SYNC</button>
                            @if ($is_pra)
                                <button type="button" class="btn btn-sm btn-warning" onclick="syncJurnalBalik()"> GENERATE
                                    JURNAL BALIK</button>
                            @endif
                        </div>
                        <form action="{{ url()->current() }}" method="get">
                            <div class="d-flex gap-3">
                                <select name="month" id="month" class="form-select" style="width: 150px"
                                    onchange="submit()">
                                    @foreach ($months as $idx => $item)
                                        <option value="{{ $idx + 1 }}" {{ $idx + 1 == $month ? 'selected' : '' }}>
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                                <select name="year" id="year" class="form-select" style="width: 150px"
                                    onchange="submit()">
                                    <option {{ $year == '2023' ? 'selected' : '' }} value="2023">2023</option>
                                    <option {{ $year == '2024' ? 'selected' : '' }} value="2024">2024</option>
                                    <option {{ $year == '2025' ? 'selected' : '' }} value="2025">2025</option>
                                    <option {{ $year == '2026' ? 'selected' : '' }} value="2026">2026</option>
                                    <option {{ $year == '2027' ? 'selected' : '' }} value="2027">2027</option>
                                </select>
                                <div>
                                    <input type="radio" name="tipe" id="radio1" hidden value="inv"
                                        {{ $tipe == 'inv' ? 'checked' : '' }} onchange="submit()">
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
                                        <th style="min-width:40px !important">#</th>
                                        <th style="min-width:40px !important">Shipment</th>
                                        <th style="min-width:40px !important">Kondisi</th>
                                        <th style="min-width:40px !important">Invoice</th>
                                        <th style="min-width:40px !important">ID JOB</th>
                                        <th style="min-width:40px !important">Asuransi</th>
                                        <th style="min-width:40px !important">Komisi</th>
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
                                        <th style="min-width:40px !important">J-Trash</th>
                                        <th style="min-width:40px !important">Tipe</th>
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
                                        {{-- <th style="min-width:40px !important">OPS & SEGEL</th>
                                        <th style="min-width:40px !important">OPS & SEGEL & CLEANING</th> --}}
                                        <th style="min-width:40px !important">BURUH</th>
                                        <th style="min-width:40px !important">CHECKER</th>
                                        <th style="min-width:40px !important">KARANTINA</th>
                                        <th style="min-width:40px !important">DEMMURAGE</th>
                                        <th style="min-width:40px !important">DO POD</th>
                                        <th style="min-width:40px !important">JOB SLIP</th>
                                        <th style="min-width:40px !important">lolo pod</th>
                                        <th style="min-width:40px !important">cleaning pod</th>
                                        <th style="min-width:40px !important">ops pod</th>
                                        <th style="min-width:40px !important">truck pod</th>
                                        <th style="min-width:40px !important">kuli pod</th>
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
                                    @php
                                        $totalTarif = 0;
                                    @endphp
                                    @foreach ($data as $order)
                                        <tr
                                            class="table-{{ $order->omset ? ($order->omset->margin <= 0.03 && $order->omset->margin >= 0 ? 'secondary' : ($order->omset->margin < 0 ? 'danger' : '')) : '' }}">
                                            <td>{{ $order->omset->id ?? null }}</td>
                                            <td>{{ $order->id }}</td>
                                            @if ($order->lock_omset == 2)
                                                <td class="text-center" id="lock-{{ $order->id }}"><button
                                                        class="text-danger bg-transparent" style="border: none"
                                                        onclick="unlock({{ $order->id }},1)"><i
                                                            class="fas fa-lock"></i></button></td>
                                            @else
                                                <td class="text-center" id="lock-{{ $order->id }}"><button
                                                        class="text-success bg-transparent" style="border: none"
                                                        onclick="lock({{ $order->id }},2)"><i
                                                            class="fas fa-unlock"></i></button></td>
                                            @endif
                                             <td>{{ $order->tarif->shipmentInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->kondisiInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->invoice }}</td>
                                            <td>{{ $order->job }}-{{ sprintf('%02d', $order->no_job) }}</td>
                                            <td>{{ $order->asuransi }}{{ $order->asuransi_id ? ' (1)' : '(0)' }}</td>
                                            <td>{{ number_format(($order->komisi ?? 0),2,',','.') }}</td>
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
                                            <td>{{ is_null($order->ba_kirim) ? '-' : date('d-m-Y', strtotime($order->ba_kirim)) }}
                                            </td>
                                            <td>{{ $order->nopol }}</td>
                                            <td>{{ $order->trucking }}</td>
                                            <td>{{ $order->container }}</td>
                                            <td>{{ $order->seal }}</td>
                                            <td>{{ is_null($order->stuffing) ? '-' : date('d-m-Y', strtotime($order->stuffing)) }}
                                            </td>
                                            <td>{{ $order->tarif->stuffing ?? '-' }}</td>
                                            <td>{{ is_null($order->full) ? '-' : date('d-m-Y', strtotime($order->full)) }}</td>
                                            <td>{{ is_null($order->barang_diantar) ? '-' : date('d-m-Y', strtotime($order->barang_diantar)) }}
                                            </td>
                                            <td>{{ is_null($order->ba_kembali) ? '-' : date('d-m-Y', strtotime($order->ba_kembali)) }}
                                            </td>
                                            <td>{{ $order->bttb->sum('qty') }}</td>
                                            <td>{{ round($order->bttb->sum('vol'), 2) }}</td>
                                            <td>{{ $order->bttb->sum('berat') }}</td>
                                            <td>{{ $order->satuanInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->tarif->satuanInfo->nama ?? '-' }}</td>
                                            <td>{{ $order->agen }}</td>
                                            <td>{{ $order->agen == 'AGEN' ? $order->agent->nama ?? '-' : $order->penerima_bl->nama ?? '-' }}
                                            </td>
                                            <td id="j_none-{{ $order->id }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_none',{{ $order->omset->id ?? null }},'{{ $order->omset->j_none ?? '[]' }}')">
                                                    {{ number_format($order->omset->none ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_none ?? '[]')) }}
                                                </a>
                                            </td>

                                            @php
                                                $jOppCount = count(json_decode($order->omset->j_opp ?? '[]'));
                                                $jKarantinaCount = count(
                                                    json_decode($order->omset->j_karantina ?? '[]'),
                                                );
                                                $jOptCount = count(json_decode($order->omset->j_opt ?? '[]'));
                                                $jUtCount = count(json_decode($order->omset->j_ut ?? '[]'));
                                                $jBlCount = count(json_decode($order->omset->j_bl ?? '[]'));
                                                $jApbsCount = count(json_decode($order->omset->j_apbs ?? '[]'));
                                                $jCleaningCount = count(json_decode($order->omset->j_cleaning ?? '[]'));
                                                $jLssCount = count(json_decode($order->omset->j_lss ?? '[]'));
                                                $jStorageCount = count(json_decode($order->omset->j_strorage ?? '[]'));
                                                $jJDCount = count(json_decode($order->omset->j_jasa_door ?? '[]'));
                                                $jAsuransiCount = count(json_decode($order->omset->j_asuransi ?? '[]'));
                                                $jOpsCount = count(json_decode($order->omset->j_ops ?? '[]'));
                                                $jSegelCount = count(json_decode($order->omset->j_segel ?? '[]'));
                                                $jOpsSealCount = count(json_decode($order->omset->j_ops_seal ?? '[]'));
                                                $jBuruhCount = count(json_decode($order->omset->j_buruh ?? '[]'));
                                                $jCheckerCount = count(json_decode($order->omset->j_checker ?? '[]'));
                                                $jDemurageCount = count(json_decode($order->omset->j_demurage ?? '[]'));
                                                $jJSPCount = count(json_decode($order->omset->j_job_slip_pod ?? '[]'));
                                                $jOPTPCount = count(json_decode($order->omset->j_opt_pod ?? '[]'));
                                                $jLPCount = count(json_decode($order->omset->j_lolo_pod ?? '[]'));
                                                $jCLPCount = count(json_decode($order->omset->j_cleaning_pod ?? '[]'));
                                                $jOPSPCount = count(json_decode($order->omset->j_ops_pod ?? '[]'));
                                                $jTruckPCount = count(json_decode($order->omset->j_truck_pod ?? '[]'));
                                                $jKuliPCount = count(json_decode($order->omset->j_kuli_pod ?? '[]'));
                                                $jKirimDokCount = count(
                                                    json_decode($order->omset->j_kirim_dokumen ?? '[]'),
                                                );
                                                $jBiayaLainCount = count(
                                                    json_decode($order->omset->j_biaya_lain ?? '[]'),
                                                );
                                                $jFlexibagCount = count(json_decode($order->omset->j_flexibag ?? '[]'));
                                                $jRCCount = count(json_decode($order->omset->j_rc ?? '[]'));

                                                $styleOpp =
                                                    $jOppCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleUt =
                                                    $jUtCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleBl =
                                                    $jBlCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleOpt =
                                                    $jOptCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleApbs =
                                                    $jApbsCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleCleaning =
                                                    $jCleaningCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleLss =
                                                    $jLssCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleKarantina =
                                                    $jKarantinaCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleStorage =
                                                    $jStorageCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleJD =
                                                    $jJDCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleAsuransi =
                                                    $jAsuransiCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleOps =
                                                    $jOpsCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleSegel =
                                                    $jSegelCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleOpsSeal =
                                                    $jOpsSealCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleBuruh =
                                                    $jBuruhCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleChecker =
                                                    $jCheckerCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleDemurage =
                                                    $jDemurageCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleJSP =
                                                    $jJSPCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleOPTP =
                                                    $jOPTPCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleLP =
                                                    $jLPCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleCLP =
                                                    $jCLPCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleOPSP =
                                                    $jOPSPCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                                $styleTruckP =
                                                    $jTruckPCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleKuliP =
                                                    $jKuliPCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleKirimDok =
                                                    $jKirimDokCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleBiayaLain =
                                                    $jBiayaLainCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleFlexibag =
                                                    $jFlexibagCount > 1
                                                        ? 'background-color: #f3ff0dfc; color: white;'
                                                        : '';
                                                $styleRC =
                                                    $jRCCount > 1 ? 'background-color: #f3ff0dfc; color: white;' : '';
                                            @endphp

                                            <td>{{ $order->truckingInfo->kendaraan->milik ?? '-' }}</td>
                                            <td id="j_trucking-{{ $order->id }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_trucking',{{ $order->omset->id ?? null }},'{{ $order->omset->j_trucking ?? '[]' }}')">
                                                    {{ number_format($order->omset->trucking ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_trucking ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_opp-{{ $order->id }}" style="{{ $styleOpp }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_opp',{{ $order->omset->id ?? null }},'{{ $order->omset->j_opp ?? '[]' }}')">
                                                    {{ number_format($order->omset->opp ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_opp ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_opt-{{ $order->id }}" style="{{ $styleOpt }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_opt',{{ $order->omset->id ?? null }},'{{ $order->omset->j_opt ?? '[]' }}')">
                                                    {{ number_format($order->omset->opt ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_opt ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_ut-{{ $order->id }}" style="{{ $styleUt }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_ut',{{ $order->omset->id ?? null }},'{{ $order->omset->j_ut ?? '[]' }}')">
                                                    {{ number_format($order->omset->ut ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_ut ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_bl-{{ $order->id }}" style="{{ $styleBl }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_bl',{{ $order->omset->id ?? null }},'{{ $order->omset->j_bl ?? '[]' }}')">
                                                    {{ number_format($order->omset->bl ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_bl ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_apbs-{{ $order->id }}" style="{{ $styleApbs }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_apbs',{{ $order->omset->id ?? null }},'{{ $order->omset->j_apbs ?? '[]' }}')">
                                                    {{ number_format($order->omset->apbs ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_apbs ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_cleaning-{{ $order->id }}" style="{{ $styleCleaning }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_cleaning',{{ $order->omset->id ?? null }},'{{ $order->omset->j_cleaning ?? '[]' }}')">
                                                    {{ number_format($order->omset->cleaning ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_cleaning ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_lss-{{ $order->id }}" style="{{ $styleLss }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_lss',{{ $order->omset->id ?? null }},'{{ $order->omset->j_lss ?? '[]' }}')">
                                                    {{ number_format($order->omset->lss ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_lss ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_storage-{{ $order->id }}" style="{{ $styleStorage }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_storage',{{ $order->omset->id ?? null }},'{{ $order->omset->j_storage ?? '[]' }}')">
                                                    {{ number_format($order->omset->storage ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_storage ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_jasa_door-{{ $order->id }}" style="{{ $styleJD }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_jasa_door',{{ $order->omset->id ?? null }},'{{ $order->omset->j_jasa_door ?? '[]' }}')">
                                                    {{ number_format($order->omset->jasa_door ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_jasa_door ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_asuransi-{{ $order->id }}" style="{{ $styleAsuransi }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_asuransi',{{ $order->omset->id ?? null }},'{{ $order->omset->j_asuransi ?? '[]' }}')">
                                                    {{ number_format($order->omset->asuransi ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_asuransi ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_ops-{{ $order->id }}" style="{{ $styleOps }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_ops',{{ $order->omset->id ?? null }},'{{ $order->omset->j_ops ?? '[]' }}')">
                                                    {{ number_format($order->omset->ops ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_ops ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_segel-{{ $order->id }}" style="{{ $styleSegel }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_segel',{{ $order->omset->id ?? null }},'{{ $order->omset->j_segel ?? '[]' }}')">
                                                    {{ number_format($order->omset->segel ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_segel ?? '[]')) }}
                                                </a>
                                            </td>
                                            {{-- <td id="j_ops_seal-{{ $order->id }}">
                                                <a href="#" onclick="showJurnal({{ $order->id }},'j_ops_seal',{{ $order->omset->id ?? null }},'{{ $order->omset->j_ops_seal ?? '[]'}}')">
                                                    {{ number_format(($order->omset->ops_seal ?? 0),2,',','.') }} / {{ count(json_decode($order->omset->j_ops_seal ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_ops_seal_cleaning-{{ $order->id }}">
                                                <a href="#" onclick="showJurnal({{ $order->id }},'j_ops_seal_cleaning',{{ $order->omset->id ?? null }},'{{ $order->omset->j_ops_seal_cleaning ?? '[]'}}')">
                                                    {{ number_format(($order->omset->ops_seal_cleaning ?? 0),2,',','.') }} / {{ count(json_decode($order->omset->j_ops_seal_cleaning ?? '[]')) }}
                                                </a>
                                            </td> --}}
                                            <td id="j_buruh-{{ $order->id }}" style="{{ $styleBuruh }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_buruh',{{ $order->omset->id ?? null }},'{{ $order->omset->j_buruh ?? '[]' }}')">
                                                    {{ number_format($order->omset->buruh ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_buruh ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_checker-{{ $order->id }}" style="{{ $styleChecker }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_checker',{{ $order->omset->id ?? null }},'{{ $order->omset->j_checker ?? '[]' }}')">
                                                    {{ number_format($order->omset->checker ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_checker ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_karantina-{{ $order->id }}" style="{{ $styleKarantina }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_karantina',{{ $order->omset->id ?? null }},'{{ $order->omset->j_karantina ?? '[]' }}')">
                                                    {{ number_format($order->omset->karantina ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_karantina ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_demmurage-{{ $order->id }}" style="{{ $styleDemurage }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_demmurage',{{ $order->omset->id ?? null }},'{{ $order->omset->j_demmurage ?? '[]' }}')">
                                                    {{ number_format($order->omset->demmurage ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_demmurage ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_job_slip_pod-{{ $order->id }}" style="{{ $styleJSP }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'',{{ $order->omset->id ?? null }},'{{ $order->omset->j_job_slip_pod ?? '[]' }}')">
                                                    {{ number_format($order->omset->job_slip_pod ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_job_slip_pod ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_opt_pod-{{ $order->id }}" style="{{ $styleOPTP }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'',{{ $order->omset->id ?? null }},'{{ $order->omset->j_opt_pod ?? '[]' }}')">
                                                    {{ number_format($order->omset->opt_pod ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_opt_pod ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_lolo_pod-{{ $order->id }}" style="{{ $styleLP }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'',{{ $order->omset->id ?? null }},'{{ $order->omset->j_lolo_pod ?? '[]' }}')">
                                                    {{ number_format($order->omset->lolo_pod ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_lolo_pod ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_cleaning_pod-{{ $order->id }}" style="{{ $styleCLP }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'',{{ $order->omset->id ?? null }},'{{ $order->omset->j_cleaning_pod ?? '[]' }}')">
                                                    {{ number_format($order->omset->cleaning_pod ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_cleaning_pod ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_ops_pod-{{ $order->id }}" style="{{ $styleOPSP }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'',{{ $order->omset->id ?? null }},'{{ $order->omset->j_ops_pod ?? '[]' }}')">
                                                    {{ number_format($order->omset->ops_pod ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_ops_pod ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_truck_pod-{{ $order->id }}" style="{{ $styleTruckP }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'',{{ $order->omset->id ?? null }},'{{ $order->omset->j_truck_pod ?? '[]' }}')">
                                                    {{ number_format($order->omset->truck_pod ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_truck_pod ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_kuli_pod-{{ $order->id }}" style="{{ $styleKuliP }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'',{{ $order->omset->id ?? null }},'{{ $order->omset->j_kuli_pod ?? '[]' }}')">
                                                    {{ number_format($order->omset->kuli_pod ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_kuli_pod ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_kirim_dokumen-{{ $order->id }}" style="{{ $styleKirimDok }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_kirim_dokumen',{{ $order->omset->id ?? null }},'{{ $order->omset->j_kirim_dokumen ?? '[]' }}')">
                                                    {{ number_format($order->omset->kirim_dokumen ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_kirim_dokumen ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_biaya_lain-{{ $order->id }}" style="{{ $styleBiayaLain }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_biaya_lain',{{ $order->omset->id ?? null }},'{{ $order->omset->j_biaya_lain ?? '[]' }}')">
                                                    {{ number_format($order->omset->biaya_lain ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_biaya_lain ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_flexibag-{{ $order->id }}" style="{{ $styleFlexibag }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_flexibag',{{ $order->omset->id ?? null }},'{{ $order->omset->j_flexibag ?? '[]' }}')">
                                                    {{ number_format($order->omset->flexibag ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_flexibag ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_rc-{{ $order->id }}" style="{{ $styleRC }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_rc',{{ $order->omset->id ?? null }},'{{ $order->omset->j_rc ?? '[]' }}')">
                                                    {{ number_format($order->omset->rc ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_rc ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td id="j_biaya-{{ $order->id }}">
                                                <a href="#"
                                                    onclick="showJurnal({{ $order->id }},'j_biaya',{{ $order->omset->id ?? null }},'{{ $order->omset->j_biaya ?? '[]' }}')">
                                                    {{ number_format($order->omset->biaya ?? 0, 2, ',', '.') }} /
                                                    {{ count(json_decode($order->omset->j_biaya ?? '[]')) }}
                                                </a>
                                            </td>
                                            <td>
                                                @php
                                                    $cbm = $order->tarif->satuanInfo->nama ?? '-';
                                                    $tarif = $order->tarif->tarif ?? 0;

                                                    if ($cbm == 'CBM') {
                                                        $totalVol = round($order->bttb->sum('vol'), 2);

                                                        // Jika total volume kurang dari 1, set menjadi 1
                                                        if ($totalVol < 1) {
                                                            $totalVol = 1;
                                                        }

                                                        $tarif *= $totalVol;
                                                        $tarif = round($tarif);
                                                    }
                                                    $totalTarif += $tarif;

                                                @endphp
                                                {{ number_format($tarif ?? 0, 2, ',', '.') }}
                                            </td>
                                            <td>{{ number_format($order->omset->laba_kotor ?? $tarif, 2, ',', '.') }}</td>
                                            <td
                                                style="{{ $order->pra_omset
                                                    ? ($order->omset && $order->omset->margin !== null
                                                        ? (($order->omset->margin <= 0.03 && $order->omset->margin >= 0) || $order->omset->margin <= 0
                                                            ? 'background-color: #f3ff0dfc;'
                                                            : ($order->omset->margin < 0
                                                                ? 'danger'
                                                                : ''))
                                                        : '')
                                                    : '' }}">
                                                {{ $order->omset && $order->omset->margin !== null
                                                    ? ($order->omset->margin == 0
                                                        ? '-'
                                                        : number_format($order->omset->margin * 100, 3, ',', '.'))
                                                    : '-' }}
                                            </td>

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

    <div class="card-footer py-2">
        <div class="d-flex gap-3 mt-2 justify-content-center">
            @php
                $totalBiaya = $data->sum(function ($o) {
                    return $o->omset->biaya ?? 0;
                });

                $totalLB = $data->sum(function ($o) {
                    return $o->omset->laba_kotor ?? 0;
                });

            @endphp
            <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                <li class="list-group-item fw-bold">Total COA 6.1</li>
                <li class="list-group-item fw-bold">{{ number_format($jurnal61 ?? 0, 2, ',', '.') }}</li>
            </ul>
            <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                <li class="list-group-item fw-bold">Total Biaya</li>
                <li class="list-group-item fw-bold">{{ number_format($totalBiaya ?? 0, 2, ',', '.') }}</li>
            </ul>
            <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                <li class="list-group-item fw-bold">Total Tarif</li>

                <li class="list-group-item fw-bold">{{ number_format($totalTarif ?? 0, 0, ',', '.') }}</li>
            </ul>
            <ul class="list-group list-group-horizontal border border-primary" style="font-size: .7rem">
                <li class="list-group-item fw-bold">Total Laba-Kotor</li>
                <li class="list-group-item fw-bold">{{ number_format($totalLB ?? 0, 0, ',', '.') }}</li>
            </ul>
        </div>
    </div>

    {{-- <div class="container my-2">
    <div class="card border-0 shadow-sm">
        <div class="card-header py-2 px-3 text-white" style="background: linear-gradient(90deg, #007bff, #0056b3);">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-bar-chart-fill me-2"></i>Rincian Jurnal - COA 6.1
            </h6>
        </div>

        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark text-center small">
                        <tr>
                            <th style="width: 25%">Periode</th>
                            <th style="width: 25%">Total Debit (Rp)</th>
                            <th style="width: 25%">Total Kredit (Rp)</th>
                            <th style="width: 25%">Net Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapPerBulan as $row)
                            <tr class="small">
                                <td class="text-center fw-medium">
                                    {{ \Carbon\Carbon::parse($row['periode'].'-01')->isoFormat('MMMM Y') }}
                                </td>
                                <td class="text-end text-primary">
                                    {{ number_format($row['total_debit'], 0, ',', '.') }}
                                </td>
                                <td class="text-end text-danger">
                                    {{ number_format($row['total_kredit'], 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold {{ $row['net_total'] < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($row['net_total'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-2">Data tidak tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> --}}

    <div class="modal fade" id="modal-jurnal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">List Jurnal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- <div class="mb-2">
                        <div class="card p-3 shadow-lg border-warning">
                            <div class="row">
                                <div class="col mb-2">
                                    <label class="label-text" style="font-size: .7rem" for="coa">COA</label>
                                    <select name="coa" id="coa" class="select2 form-select" style="width: 200px">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col mb-2">
                                    <label class="label-text" style="font-size: .7rem" for="nomor">Nomor Jurnal</label>
                                    <input type="text" name="nomor" class="form-control form-control-sm" id="nomor">
                                </div>
                                <div class="col mb-2">
                                    <label class="label-text" style="font-size: .7rem" for="nama">Keterangan</label>
                                    <input type="text" name="nama" class="form-control form-control-sm" id="nama">
                                </div>
                                <div class="col mb-2">
                                    <label class="label-text" style="font-size: .7rem" for="tanggal">Tanggal Jurnal Awal</label>
                                    <input type="date" name="tanggal_awal" class="form-control form-control-sm" id="tanggal_awal">
                                </div>
                                <div class="col mb-2">
                                    <label class="label-text" style="font-size: .7rem" for="tanggal">Tanggal Jurnal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control form-control-sm" id="tanggal_akhir">
                                </div>
                                <div class="col mb-2">
                                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="filterSearch()">Filter</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="filter-table" style="font-size: .7rem">
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
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="list-jurnal-filter">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr> --}}
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
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody id="list-jurnal">

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-end" colspan="10"><b>TOTAL</b></td>
                                    <td class="text-end"><b id="debit-total"></b></td>
                                    <td class="text-end"><b id="credit-total"></b></td>
                                    <td></td>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/selectize.js') }}"></script>
    <script>
        let table = $('#table').DataTable({
            fixedColumns: {
                left: 7,
                right: 0
            },
            autoWidth: false,
            paging: false,
            scrollCollapse: true,
            fixedHeader: true,
            // select: true,
            // scrollX:true,
            // scrollY: 400,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel'
            }, ],
            search: {
                return: true
            }
        });

        $('#table tbody').on('click', 'tr', function() {
            // Hapus seleksi lama
            $('#table tbody tr').removeClass('selected1');
            $('#table tbody tr td').each(function() {
                // Hapus background-color hanya jika ditambahkan via JS sebelumnya
                if ($(this).data('clicked') === true) {
                    $(this).css('background-color', '');
                    $(this).removeData('clicked');
                }
            });

            // Tambah seleksi baru
            $(this).addClass('selected1');
            $(this).find('td').each(function() {
                const inlineStyle = $(this).attr('style') || '';
                if (!inlineStyle.includes('background-color')) {
                    $(this).css('background-color', '#adf8dc');
                    $(this).data('clicked', true); // Flag sebagai td yang diwarnai via JS
                }
            });
        });

        table.column(0).visible(false);
        table.column(1).visible(false);
        table.column(40).visible(false);
        jQuery('.dataTable').wrap('<div class="dataTables_scroll" />');

        $('.select2').select2({
            dropdownParent: $('#modal-jurnal')
        });


        function sync() {
            syncAction(0, 50);
        }

        function syncJurnalBalik() {
            syncJurnalBalikAction(0, 50);
        }

        function syncAction(start, end) {
            $.ajax({
                type: "POST",
                url: "{{ route('omset.sync') }}",
                data: {
                    id: @json($ids),
                    start: start,
                    end: end,
                },
                success: function(response) {
                    if (response == 'complete') {
                        alert("SINKRONISASI BERHASIL!");
                        location.reload();
                    } else {
                        syncAction(response, 50)
                    }
                }
            });
        }

        function syncJurnalBalikAction(start, end) {
            $.ajax({
                type: "POST",
                url: "{{ route('omset.sync.jurnal_balik') }}",
                data: {
                    id: @json($ids),
                    start: start,
                    end: end,
                    month: @json($month),
                    year: @json($year),
                },
                success: function(response) {
                    if (response == 'complete') {
                        alert("SINKRONISASI JURNAL BALIK BERHASIL!");
                    } else {
                        syncJurnalBalikAction(response, 50)
                    }
                }
            });
        }

        // table.on('select', function (e, dt, type, indexes) {
        //     let rowData = table.rows(indexes).data().toArray();
        //     console.log(rowData);
        // })

        var modal_jurnal = new bootstrap.Modal(document.getElementById('modal-jurnal'))

        var omset_id = null;
        var type = null;
        var order_id = null;

        function showJurnal(order_id_, type_, omset_id_, id) {
            omset_id = omset_id_;
            type = type_;
            order_id = order_id_;
            getJurnal(id, type);
            modal_jurnal.show();
        }

        function getJurnal(id, type) {
            $.ajax({
                type: "POST",
                url: "{{ route('omset.jurnal1') }}",
                data: {
                    id: id
                },
                success: function(response) {
                    let html = '';
                    let debit = 0;
                    let credit = 0;
                    let options = '';
                    let arr = [
                        'j_trucking', 'j_opp', 'j_opt', 'j_ut', 'j_bl', 'j_apbs', 'j_cleaning', 'j_lss',
                        'j_storage',
                        'j_jasa_door', 'j_asuransi', 'j_ops', 'j_segel', 'j_buruh', 'j_checker',
                        'j_karantina', 'j_demmurage',
                        'j_lolo_pod', 'j_cleaning_pod', 'j_ops_pod', 'j_opt_pod', 'j_truck_pod',
                        'j_kuli_pod',
                        'j_kirim_dokumen', 'j_biaya_lain', 'j_flexibag', 'j_rc', 'j_biaya_lain',
                        // 'j_job_slip_pod',
                    ];

                    if (type != 'j_biaya') {
                        $.each(arr, function(idx, item) {
                            let label = item.substr(2); // default label tanpa "j_"

                            // kalau item 'j_opt_pod' → ganti label jadi 'job_slip'
                            if (item === 'j_opt_pod') {
                                label = 'job_slip';
                            }

                            options +=
                                `<option value="${item}" ${ type == item ? 'selected' : '' }>${label}</option>`;
                        });
                    }

                    $.each(response, function(idx, item) {
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
                                    <td>
                                        <select class="form-select form-select-sm" ${type=='j_biaya'?'disabled':''} style="width: 150px" onchange="addItemJurnal(${item.id},this.value)">
                                            ${options}
                                        </select>
                                    </td>
                                </tr>`;
                    });

                    $('#list-jurnal').html(html);
                    $('#debit-total').html(debit.toLocaleString('id-ID'));
                    $('#credit-total').html(credit.toLocaleString('id-ID'));
                }
            });
        }

        function substr(string, num) {
            return string.substr(num);
        }

        function filterSearch() {
            var coa = $('#coa').val();
            var nomor = $('#nomor').val();
            var nama = $('#nama').val();
            var tgl_awal = $('#tanggal_awal').val();
            var tgl_akhir = $('#tanggal_akhir').val();
            $.ajax({
                type: "POST",
                url: "{{ route('api.jurnal.filter') }}",
                data: {
                    coa_id: coa,
                    nomor: nomor,
                    nama: nama,
                    tgl_awal: tgl_awal,
                    tgl_akhir: tgl_akhir,
                },
                success: function(response) {
                    var html = '';
                    $.each(response, function(idx, item) {
                        html += `<tr id="item-jurnal-${item.id}">
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
                                    <td><button onclick="addItemJurnal(${item.id})" class="bg-success p-2 text-white" style="border:none" type="button">+</button></td>
                                </tr>`;
                    });

                    $('#list-jurnal-filter').html(html);
                }
            });
        }

        function addItemJurnal(id, to) {
            if (confirm('Apakah anda yakin?')) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('omset.add.item') }}",
                    data: {
                        jurnal_id: id,
                        omset_id: omset_id,
                        type: type,
                        to: to,

                    },
                    success: function(response) {
                        alert(response.message);
                        getJurnal(response.jurnal);
                        $('#item-jurnal-' + id).remove();
                        if (response.reload) {
                            location.reload();
                        }
                        if (response.status) {
                            let ke = `<a href="#" onclick="showJurnal(${order_id},'${to}',${omset_id},'${response.a_jurnal}')">
                                        ${rp(response.a_debit)}
                                    </a>`;
                            let dari = `<a href="#" onclick="showJurnal(${order_id},'${type}',${omset_id},'${response.b_jurnal}')">
                                        ${rp(response.b_debit)}
                                    </a>`;
                            $('#' + to + '-' + order_id).html(ke);
                            $('#' + type + '-' + order_id).html(dari);
                        }
                    }
                });
            }
        }

        function rp(num) {
            return num.toLocaleString('id-ID');
        }

        function lock(id, val) {
            $.ajax({
                type: "POST",
                url: "{{ url('api/update-order-request') }}",
                data: {
                    id: id,
                    lock_omset: val,
                },
                success: function(response) {
                    var html =
                        `<td class="text-center" id="lock-${id}"><button class="text-danger bg-transparent" style="border: none" onclick="unlock(${id})"><i class="fas fa-lock"></i></button></td>`;
                    $('#lock-' + id).html(html);
                    alert('Lock berhasil!')
                }
            });
        }

        function unlock(id, val) {
            $.ajax({
                type: "POST",
                url: "{{ url('api/update-order-request') }}",
                data: {
                    id: id,
                    lock_omset: val,
                },
                success: function(response) {
                    var html =
                        `<td class="text-center" id="lock-${id}"><button class="text-success bg-transparent" style="border: none" onclick="lock(${id})"><i class="fas fa-unlock"></i></button></td>`;
                    $('#lock-' + id).html(html);
                    alert('Unlock berhasil!')
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
