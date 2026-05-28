@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/resize-column.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.dataTables.min.css">
    <style>
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');

            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
            }

            #print,
            #print * {
                visibility: visible;
                font-size: 0.7rem !important;
            }

            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -70px;
            }

            #table td,
            #table th {
                border: 1px solid black;
            }

            #print {
                color: #000;
            }
        }

        table.data th,
        td {
            padding: 10px;
            white-space: nowrap;
            font-size: 0.7rem;
            /* Menyesuaikan ukuran font */
        }

        .table th {
            background-color: #f8f9fa;
        }

        .table td,
        .table th {
            padding: 0.5rem;
        }

        .form-control {
            font-size: 0.8rem;
            /* Ukuran font input filter lebih kecil */
        }

        .select2-container {
            width: 100% !important;
        }

        /* CSS untuk memberi jarak antara searching dan info */
        .dataTables_filter {
            margin-bottom: 10px;
            /* Jarak bawah pada elemen searching */
        }

        .dataTables_info {
            margin-top: 10px;
            /* Jarak atas pada elemen info */
        }

        .btn-custom {
            font-size: 0.7rem;
            /* Menyesuaikan ukuran font */
            padding: 0.10rem 0.5rem;
            /* Adjust padding for smaller button */
        }

        .table.data td,
        .table.data th {
            border: 1px solid #dee2e6 !important;
            /* Warna dan ketebalan border */
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div id="print">
                    <div class="row mb-4">
                        <!-- Filter Options -->
                        <div class="col-12">
                            <div class="row justify-content-between">
                                <!-- Subjek Dropdown -->
                                <div class="col-12 col-md-3">
                                    <label for="subjek">Subjek</label>
                                    <form action="{{ route('jurnal.buku_besar_pembantu1') }}" method="get">
                                        <input type="hidden" name="month" value="{{ $month }}">
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <input type="hidden" name="coa_id" value="{{ $coa_id }}">
                                        <select class="form-control px-3 py-1" name="subjek" onchange="submit()">
                                            <option value="customer_xpdc"
                                                {{ $subjek == 'customer_xpdc' ? 'selected' : '' }}>Customer XPDC</option>
                                            <option value="customer_trucking"
                                                {{ $subjek == 'customer_trucking' ? 'selected' : '' }}>Customer Trucking
                                            </option>
                                            <option value="pelayaran" {{ $subjek == 'pelayaran' ? 'selected' : '' }}>
                                                Pelayaran</option>
                                            <option value="agen" {{ $subjek == 'agen' ? 'selected' : '' }}>Agen</option>
                                            @if ($coa_id != 46 && $coa_id != 47 && $coa_id != 62 && $coa_id != 63 && $coa_id != 131 && $coa_id != 48)
                                                <option value="relasi" {{ $subjek == 'relasi' ? 'selected' : '' }}>Relasi
                                                </option>
                                            @endif
                                            <option value="vendor" {{ $subjek == 'vendor' ? 'selected' : '' }}>Vendor
                                                Trucking</option>
                                            <option value="lain-lain" {{ $subjek == 'lain-lain' ? 'selected' : '' }}>
                                                Lain-lain (External Inv)</option>
                                            <option value="jurnal-balik" {{ $subjek == 'jurnal-balik' ? 'selected' : '' }}>
                                                Jurnal Balik</option>
                                            {{-- <option value="kendaraan" {{ $subjek == 'kendaraan' ? 'selected' : '' }}>Vendor</option> --}}
                                        </select>
                                    </form>
                                </div>

                                <!-- COA Dropdown -->
                                <div class="col-12 col-md-3">
                                    <label for="akun">Akun</label>
                                    <form action="{{ route('jurnal.buku_besar_pembantu1') }}" method="get">
                                        <input type="hidden" name="month" value="{{ $month }}">
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <input type="hidden" name="subjek" value="{{ $subjek }}">
                                        <select class="form-control px-3 py-1" name="coa_id" onchange="submit()">
                                            @foreach ($coas as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ $coa_id == $item->id ? 'selected' : '' }}>
                                                    {{ $item->kode }} - {{ $item->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>

                                <!-- Tahun Dropdown -->
                                <div class="col-12 col-md-3">
                                    <label for="tahun">Tahun</label>
                                    <form action="{{ route('jurnal.buku_besar_pembantu1') }}" method="get">
                                        <input type="hidden" name="month" value="{{ $month }}">
                                        <input type="hidden" name="coa_id" value="{{ $coa_id }}">
                                        <input type="hidden" name="subjek" value="{{ $subjek }}">
                                        <select class="form-control select21 px-3 py-1" name="year" onchange="submit()">
                                            @for ($i = 2023; $i <= 2030; $i++)
                                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Bulan Navigation -->
                        <div class="col-12 mt-3">
                            <hr>
                            <div class="d-flex gap-2">
                                <b class="mt-2">Bulan:</b>
                                @foreach ($months as $idx => $item)
                                    <a href="{{ route('jurnal.buku_besar_pembantu1', ['month' => sprintf('%02d', $idx + 1), 'coa_id' => $coa_id, 'year' => $year, 'subjek' => $subjek]) }}"
                                        class="text-center text-dark {{ $idx + 1 == (int) $month ? 'bg-light-success' : '' }}"
                                        style="border: solid 1px gray; width:50px; text-decoration:none">
                                        {{ $item }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="col-12 mt-4">
                        <div style="overflow-x: auto; overflow-y: auto; max-height: 500px;">
                            <table class="table data table-bordered table-sm data-table" style="font-size: .8rem;">
                                <thead>
                                    <tr>
                                        @if ($subjek === 'relasi' || $subjek === 'jurnal-balik')
                                            <th class="text-center">No</th>

                                            @if ($coa_id == 65 || $coa_id == 66 || $coa_id == 28)
                                                <th class="text-center">No Jurnal</th>
                                            @else
                                                <th class="text-center">No Jurnal (D)</th>
                                            @endif
                                            <th class="text-center">Invoice</th>

                                            @if ($coa_id == 65 || $coa_id == 66 || $coa_id == 28)
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Keterangan</th>
                                            @else
                                                <th class="text-center">Tgl (D)</th>
                                                <th class="text-center">Ket (D)</th>
                                            @endif

                                            <th class="text-center">Debit</th>
                                            <th class="text-center">Credit</th>

                                            @if (!in_array($coa_id, [65, 66,28]))
                                                <th class = "text-center">Nomor Jurnal (C)</th>
                                                <th class="text-center">Tgl (C)</th>
                                                <th class="text-center">Ket (C)</th>
                                            @endif

                                            <th class="text-center">Saldo</th>
                                        @else
                                            <th class="text-center">No</th>
                                            <th class="text-center">
                                                @if ($subjek === 'pelayaran')
                                                    Pelayaran
                                                @elseif ($subjek === 'agen')
                                                    Agen
                                                @elseif ($subjek == 'customer_trucking')
                                                    Customer Trucking
                                                @elseif ($subjek == 'vendor')
                                                    Vendor
                                                @else
                                                    Customer XPDC
                                                @endif
                                            </th>
                                            <th class="text-center">Debit</th>
                                            <th class="text-center">Credit</th>
                                            <th class="text-center">Saldo</th>
                                            <th class="text-center">Action</th>
                                        @endif

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedData as $data)
                                        <tr>
                                            @if ($subjek === 'relasi' || $subjek === 'jurnal-balik')
                                                @php
                                                    $debit = $data['total_debit'];
                                                    $credit = $data['total_credit'];
                                                    $isMismatch = $debit !== $credit && !in_array($coa_id, [65, 66,28]);
                                                @endphp

                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                @if (in_array($coa_id, [65, 66,28]))
                                                    <td>{{ $data['customer_name'] }}</td>
                                                @elseif($subjek === 'jurnal-balik')
                                                    <td>{!! is_array($data['no_d']) ? implode('<br>', $data['no_d']) : $data['no_d'] !!}</td>
                                                @else
                                                    <td>{!! $data['no_d']->implode('<br>') !!}</td>
                                                @endif
                                                <td>{{ $data['invoice'] }}</td>

                                                {{-- Tanggal dan Keterangan --}}
                                                @if (in_array($coa_id, [65, 66,28]))
                                                    {{-- Tampilkan hanya satu kolom untuk tgl_d dan ket_d --}}
                                                    <td>{!! $data['tgl_d']->implode('<br>') !!}</td>
                                                    <td>{!! $data['ket_d']->implode('<br>') !!}</td>
                                                @elseif($subjek === 'jurnal-balik')
                                                    <td>{!! is_array($data['tgl_d']) ? implode('<br>', $data['tgl_d']) : $data['tgl_d'] !!}</td>
                                                    <td>{!! is_array($data['ket_d']) ? implode('<br>', $data['ket_d']) : $data['ket_d'] !!}</td>
                                                @else
                                                    <td>{!! $data['tgl_d']->implode('<br>') !!}</td>
                                                    <td>{!! $data['ket_d']->implode('<br>') !!}</td>
                                                @endif

                                                {{-- Nominal Debit dan Kredit --}}
                                                @if ($subjek === 'jurnal-balik')
                                                    <td class="text-end {!! $isMismatch ? 'bg-danger text-white fw-bold' : '' !!}">
                                                        {{ number_format((float) str_replace([',', ' '], '', $debit), 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-end {!! $isMismatch ? 'bg-danger text-white fw-bold' : '' !!}">
                                                        {{ number_format((float) str_replace([',', ' '], '', $credit), 2, ',', '.') }}
                                                    </td>
                                                @else
                                                    <td class="text-end {!! $isMismatch ? 'bg-danger text-white fw-bold' : '' !!}">
                                                        {{ number_format($debit, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-end {!! $isMismatch ? 'bg-danger text-white fw-bold' : '' !!}">
                                                        {{ number_format($credit, 2, ',', '.') }}
                                                    </td>
                                                @endif
                                                 @if ($subjek === 'jurnal-balik')
                                                 <td>{!! implode('<br>', $data['no_c'] ?? []) !!}</td>
                                                 <td>{!! implode('<br>', $data['tgl_c'] ?? []) !!}</td>
                                                 <td>{!! implode('<br>', $data['ket_c'] ?? []) !!}</td>
                                                 @else

                                                {{-- Tanggal dan Keterangan Kredit (jika bukan coa 65/66) --}}
                                                @unless ($subjek === 'relasi' && in_array($coa_id, [65, 66,28]))
                                                    <td>{!! $data['no_c']->implode('<br>') !!}</td>
                                                    <td>{!! $data['tgl_c']->implode('<br>') !!}</td>
                                                    <td>{!! $data['ket_c']->implode('<br>') !!}</td>
                                                @endunless
@endif

                                                {{-- Saldo --}}
                                                <td class="text-end">
                                                    {{ number_format($data['saldo'], 2, ',', '.') }}
                                                </td>
                                            @else
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $subjek === 'pelayaran' ? $data['pelayaran'] : $data['customer_name'] }}
                                                </td>
                                                <td class="text-end ">
                                                    {{ number_format($data['total_debit'], 2, ',', '.') }}</td>
                                                <td class="text-end">
                                                    {{ number_format($data['total_credit'], 2, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($data['saldo'], 2, ',', '.') }}</td>
                                                @if ($subjek != 'relasi')
                                                    <td class="text-center">
                                                        @php

                                                            $pelayaranName =
                                                                $data['pelayaran'] ?? $data['customer_name'];

                                                        @endphp

                                                        <a target="_blank"
                                                            href="{{ route('jurnal.buku_besar_pembantu_rincian', [
                                                                'subjek' => $subjek,
                                                                'coa_id' => $coa_id,
                                                                'month' => $month,
                                                                'year' => $year,
                                                                'customer' => $pelayaranName,
                                                            ]) }}"
                                                            class="btn btn-success btn-custom">
                                                            Rincian
                                                        </a>
                                                    </td>
                                                @endif
                                            @endif
                                        </tr>
                                    @endforeach
                                <tfoot>
                                    <tr class="fw-bold">
                                        @if ($subjek === 'relasi' || $subjek === 'jurnal-balik')
                                            @if ($coa_id == 65 || $coa_id == 66 || $coa_id == 28)
                                                <td colspan="5" class="text-center">Total</td>
                                                <td class="text-end">
                                                    {{ number_format($groupedData->sum('total_debit'), 2, ',', '.') }}
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($groupedData->sum('total_credit'), 2, ',', '.') }}
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($groupedData->sum('total_credit') - $groupedData->sum('total_debit'), 2, ',', '.') }}
                                                </td>
                                            @else
                                                <td colspan="5" class="text-center">Total</td>
                                                <td class="text-end">
                                                    {{ number_format($groupedData->sum('total_debit'), 2, ',', '.') }}
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($groupedData->sum('total_credit'), 2, ',', '.') }}
                                                </td>
                                                <td colspan="4" class="text-end">
                                                    {{ number_format($groupedData->sum('saldo'), 2, ',', '.') }}
                                                </td>
                                            @endif
                                        @else
                                            <td colspan="2" class="text-center">Total</td>
                                            <td class="text-end">
                                                {{ number_format($groupedData->sum('total_debit'), 2, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($groupedData->sum('total_credit'), 2, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($groupedData->sum('saldo'), 2, ',', '.') }}
                                            </td>
                                            <td></td>
                                        @endif
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/resize-column.js') }}"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                "paging": true,
                "searching": true,
                "info": true,
                "lengthMenu": [10, 25, 50, 100],
                "responsive": true,
                "ordering": false // Nonaktifkan sorting kolom
            });
        });
    </script>
@endpush
