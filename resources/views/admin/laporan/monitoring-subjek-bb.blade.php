    @extends('layouts.admin')

    @section('content')
        <style>
            .container-laporan {
                max-width: 1350px;
                margin: 30px auto;
                background: #ffffff;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
                height: 80vh;
                /* tinggi container */
                overflow-y: auto;
                /* scroll vertical */
                overflow-x: auto;
                /* scroll horizontal */
            }

            /* FILTER */
            .filter-box {
                display: flex;
                gap: 15px;
                align-items: center;
                margin-bottom: 30px;
                flex-wrap: wrap;
            }

            .filter-box select {
                padding: 8px 12px;
                border: 1px solid #ccc;
                border-radius: 6px;
            }

            .filter-box button {
                padding: 8px 20px;
                background: #007bff;
                border: none;
                color: white;
                border-radius: 6px;
                cursor: pointer;
            }

            .filter-box button:hover {
                background: #0056b3;
            }

            /* JUDUL */
            .judul {
                text-align: center;
                font-size: 22px;
                font-weight: bold;
                margin-bottom: 35px;
            }

            /* GRID */
            .grid-coa {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 40px;
            }

            /* CARD */
            .card-coa {
                background: white;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            .card-header {
                background: #f8f9fa;
                padding: 12px;
                font-weight: bold;
                text-align: center;
                border-bottom: 1px solid #ddd;
            }

            /* TABLE */
            .table-coa {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
            }

            .table-coa th,
            .table-coa td {
                padding: 8px 10px;
                border-bottom: 1px solid #eee;
            }

            .table-coa th {
                background: #fafafa;
            }

            .table-coa td:last-child,
            .table-coa th:last-child {
                text-align: right;
            }

            /* TOTAL */
            .total {
                font-weight: bold;
                background: #f8f9fa;
            }

            /* RESPONSIVE */
            @media(max-width:900px) {
                .grid-coa {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <div class="container-laporan">
            <form method="GET" action="{{ route('monitoring-subjek-bb') }}">
                <div class="filter-box">
                    {{-- BULAN --}}
                    @php
                        $months = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ];
                    @endphp
                    <select name="month">
                        <option value="">Pilih Bulan</option>
                        @foreach ($months as $key => $value)
                            <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    {{-- TAHUN --}}
                    <select name="year">
                        <option value="">Pilih Tahun</option>
                        @for ($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                    {{-- SUBMIT --}}
                    <button type="submit">
                        Filter
                    </button>
                </div>
            </form>
            {{-- JUDUL --}}
            <div class="judul">
                Monitoring BB Pembantu Subjek
            </div>
            {{-- GRID --}}
            <div class="grid-coa">
                @foreach ($result as $coa)
                <div class="card-coa">
                    <div class="card-header">
                        COA : {{ $coa['coa_nama'] }}
                    </div>
                    <table class="table-coa">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coa['detail'] as $row)
                            <tr>
                                <td>{{ $row['nama'] }}</td>
                                <td>{{ number_format($row['saldo'],0,',','.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endforeach
            </div>
        </div>
    @endsection
