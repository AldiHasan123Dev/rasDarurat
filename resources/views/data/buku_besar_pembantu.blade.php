<table class="table data table-bordered table-sm mt-3 data-table" style="font-size: .7rem;">
    <thead>
        <tr>
            <td>No</td>
            <td>Customer</td>
            <td>Debit</td>
            <td>Credit</td>
            <td>Saldo</td>
            <td>#</td>
        </tr>
    </thead>
    <tbody id="data-component">
        @php
    $no = 1;
    $total = 0;
    $debit = 0;
    $credit = 0;
    function monthName ($number){
        $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','July','Agustus','September','Oktober','November','Desember'];
        return $bulan[$number];
    }
@endphp
@if ($subjek=='pelayaran')
    @foreach ($data as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->nama }}</td>
            <td class="text-end">{{ number_format($item->jurnals($month,$year,$coa_id)->sum('debit')) }}</td>
            <td class="text-end">{{ number_format($item->jurnals($month,$year,$coa_id)->sum('credit')) }}</td>
            <td class="text-end">
                @php
                    if ($tipe=='D') {
                        $saldo = $item->jurnals($month,$year,$coa_id)->sum('debit') - $item->jurnals($month,$year,$coa_id)->sum('credit');
                    } else {
                        $saldo = $item->jurnals($month,$year,$coa_id)->sum('credit') - $item->jurnals($month,$year,$coa_id)->sum('debit');
                    }
                    $total += $saldo;
                    $debit += $item->jurnals($month,$year,$coa_id)->sum('debit');
                    $credit += $item->jurnals($month,$year,$coa_id)->sum('credit');
                @endphp
                {{ number_format($saldo) }}
            </td>
            <td>
                <a target="d_blank" href="{{ route('jurnal.buku_besar_pembantu_detail',['coa_id'=>$coa_id,'month'=>$month,'year'=>$year,'pelayaran'=>$item->nama]) }}" class="text-primary">
                    Detail
                </a>
            </td>
        </tr>
    @endforeach
    <tfoot>
        <tr class="fw-bold">
            <td class="text-center" colspan="2">Total</td>
            <td class="text-end">{{ number_format($debit) }}</td>
            <td class="text-end">{{ number_format($credit) }}</td>
            <td class="text-end">{{ number_format($total) }}</td>
            <td>-</td>
        </tr>
    </tfoot>
@else
    @foreach ($data as $idx => $jurnals)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $idx }}</td>
        <td class="text-end">{{ number_format($jurnals->sum('debit')) }}</td>
        <td class="text-end">{{ number_format($jurnals->sum('credit')) }}</td>
        <td class="text-end">
            @php
                if ($tipe=='D') {
                    $saldo = $jurnals->sum('debit') - $jurnals->sum('credit');
                } else {
                    $saldo = $jurnals->sum('credit') - $jurnals->sum('debit');
                }
                $total += $saldo;
                $debit += $jurnals->sum('debit');
                $credit += $jurnals->sum('credit');
            @endphp
            {{ number_format($saldo) }}
        </td>
        <td>
            @if ($subjek=='pelayaran')
            <a target="d_blank" href="{{ route('jurnal.buku_besar_pembantu_detail',['coa_id'=>$coa_id,'month'=>$month,'year'=>$year,'pelayaran'=>$idx]) }}" class="text-primary">
                Detail
            </a>
            @else
            <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#detail-{{ $loop->iteration }}">
                Detail
            </a>
            <div class="modal fade" id="detail-{{ $loop->iteration }}" tabindex="-1" aria-labelledby="detail-{{ $loop->iteration }}Label" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detail-{{ $loop->iteration }}Label">{{ $idx }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-detail" style="font-size: .7rem">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tgl</th>
                                        <th>Nomor</th>
                                        <th>JOB</th>
                                        <th>INV</th>
                                        <th>NO BG</th>
                                        <th>Cont</th>
                                        <th>Nopol</th>
                                        <th>Keterangan</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Tanggal</th>
                                        <th>Nomor</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (
                                        $jurnals->groupBy(['invoice']) as $id => $jurnal
                                    )
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @forelse ($jurnal->where('debit','>',0) as $tgl)
                                                    <span>{{ date('d/m/y',strtotime($tgl->created_at)) }}; </span>
                                                @empty
                                                <span>-</span>
                                                @endforelse
                                            </td>
                                            <td>{{ implode('; ',$jurnal->where('debit','>',0)->pluck('nomor')->toArray()) }}</td>
                                            <td>{{ $jurnal->first()->order->job ?? '-'}}-{{ sprintf('%02d',$jurnal->first()->order->no_job ?? 0) }}</td>
                                            <td>{{ $jurnal->first()->invoice ?? '-' }}</td>
                                            <td>{{ $jurnal->first()->no_bg ?? '-' }}</td>
                                            <td>{{ $jurnal->first()->container ?? '-' }}</td>
                                            <td>{{ $jurnal->first()->nopol ?? '-' }}</td>
                                            <td>{{ implode('; ',$jurnal->where('debit','>',0)->pluck('nama')->toArray()) }}</td>
                                            <td>{{ number_format($jurnal->where('debit','>',0)->sum('debit')) }}</td>
                                            <td>{{ number_format($jurnal->where('credit','>',0)->sum('credit')) }}</td>
                                            <td>
                                                @forelse ($jurnal->where('credit','>',0) as $tgl)
                                                    <span>{{ date('d/m/y',strtotime($tgl->created_at)) }}; </span>
                                                @empty
                                                    <span>-</span>
                                                @endforelse
                                            </td>
                                            <td>{{ implode('; ',$jurnal->where('credit','>',0)->pluck('nomor')->toArray())  }}</td>
                                            <td>{{ implode('; ',$jurnal->where('credit','>',0)->pluck('nama')->toArray())  }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <td class="text-end" colspan="8"><b>TOTAL</b></td>
                                        <td class="text-end"><b id="debit-total">{{ number_format($jurnals->sum('debit')) }}</b></td>
                                        <td class="text-end"><b id="credit-total">{{ number_format($jurnals->sum('credit')) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot> --}}
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                    </div>
                </div>
                </div>
            </div>
            @endif
        </td>
    </tr>
    @php
        $no++;
    @endphp
    @endforeach
    <tr>
        <td>{{ $no }}</td>
        <td><b>{{ $subjek=='customer_trucking'?'TANPA ID TRUCKING':($subjek=='kendaraan'?'TIDAK ADA INVOICE':'TANPA JOB') }}</b></td>
        <td class="text-end">{{ number_format($no_data->sum('debit')) }}</td>
        <td class="text-end">{{ number_format($no_data->sum('credit')) }}</td>
        <td class="text-end">
            @php
                if ($tipe=='D') {
                    $saldo_no_data = $no_data->sum('debit') - $no_data->sum('credit');
                } else {
                    $saldo_no_data = $no_data->sum('credit') - $no_data->sum('debit');
                }
                $total += $saldo_no_data;
                $debit += $no_data->sum('debit');
                $credit += $no_data->sum('credit');
            @endphp
            {{ number_format($saldo_no_data) }}
        </td>
        <td>-</td>
    </tr>
    <tfoot>
        <tr class="fw-bold">
            <td class="text-center" colspan="2">Total</td>
            <td class="text-end">{{ number_format($debit) }}</td>
            <td class="text-end">{{ number_format($credit) }}</td>
            <td class="text-end">{{ number_format($total) }}</td>
            <td>-</td>
        </tr>
    </tfoot>
@endif
    </tbody>
</table>

