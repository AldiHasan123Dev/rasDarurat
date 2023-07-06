<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-6">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th style="width:200px">Akun :</th>
                            <th>
                                <select class="form-control px-3 py-1" wire:model="coa_id" wire:change="changeCoa" style="font-size:.8rem">
                                    @foreach ($coas as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th style="width:200px">Tahun :</th>
                            <th>
                                <select class="form-control px-3 py-1" wire:model="year" wire:change="changeCoa" style="font-size:.8rem">
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                </select>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mt-2" style="font-size: .7rem; white-space:nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        @foreach ($months as $item)
                            <th>{{ $item }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>Saldo Awal</b></td>
                        @foreach ($saldo['saldo_awal'] as $idx => $item)
                            <td>{{ number_format($item,2,'.',',') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td><b>Debit</b></td>
                        @foreach ($saldo['debit'] as $idx => $item)
                            <td>{{ number_format($item,2,'.',',') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td><b>Credit</b></td>
                        @foreach ($saldo['credit'] as $idx => $item)
                            <td>{{ number_format($item,2,'.',',') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td><b>Saldo Akhir</b></td>
                        @foreach ($saldo['saldo_akhir'] as $idx => $item)
                            <td>{{ number_format($item,2,'.',',') }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="d-flex gap-2">
            <b class="mt-2">Bulan: </b>
            @foreach ($months as $idx => $item)
                <button wire:click="changeMonth({{ $idx+1 }})" class="{{ $idx+1==(int)$month?'bg-light-success':'' }}" style="background: transparent; border: solid 1px gray; width:50px">{{ $item }}</button>
            @endforeach
        </div>
        <table class="table table-sm mt-3" style="font-size: .7rem; white-space:nowrap">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. Jurnal</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Saldo</th>
                    <th>Keterangan</th>
                    @if ($coa->is_cont)
                    <th>No. Cont</th>
                    @endif
                    @if ($coa->is_nopol)
                    <th>Nopol</th>
                    @endif
                    @if ($coa->is_nojob)
                    <th>No. Job</th>
                    @endif
                    @if ($coa->is_invoice)
                    <th>Invoice</th>
                    @endif
                    @if ($coa->is_nobg)
                    <th>No. BG</th>
                    @endif
                    @if ($coa->is_nobupot)
                    <th>No. Bupot PPh 23</th>
                    @endif
                    @if ($coa->is_tglbupot)
                    <th>Tgl Bupot PPh 23</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">SALDO AWAL</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="text-end">{{ number_format($saldo_awal,2,',','.') }}</td>
                </tr>
                @foreach ($data as $item)
                    @php
                        if ($tipe=='D') {
                            if ($item->debit>0) {
                                $saldo_awal += $item->debit;
                            } else {
                                $saldo_awal -= $item->credit;
                            }
                        } else {
                            if ($item->debit>0) {
                                $saldo_awal -= $item->debit;
                            } else {
                                $saldo_awal += $item->credit;
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                        <td>{{ $item->nomor }}</td>
                        @if ($coa->is_cont)
                        <td>{{ $item->order ? $item->order->container : '-' }}</td>
                        @endif
                        @if ($coa->is_nopol)
                        <td>{{ $item->order ? $item->order->nopol : '-' }}</td>
                        @endif
                        @if ($coa->is_nojob)
                        <td>{{ $item->order ? $item->order->job.'-'.sprintf('%02d',$item->order->no_job) : '-' }}</td>
                        @endif
                        @if ($coa->is_invoice)
                        <td>{{ $item->order ? $item->order->invoice : '-' }}</td>
                        @endif
                        <td>{{ number_format($item->debit,2,',','.') }}</td>
                        <td>{{ number_format($item->credit,2,',','.') }}</td>
                        <td class="text-end">{{ number_format($saldo_awal,2,',','.') }}</td>
                        <td>{{ $item->nama }}</td>
                        @if ($coa->is_nobg)
                        <td>-</td>
                        @endif
                        @if ($coa->is_nobupot)
                        <td>{{ $item->order ? ($item->order->transaksi?$item->order->transaksi->no_bupot:'') : '-' }}</td>
                        @endif
                        @if ($coa->is_tglbupot)
                        <td>{{ $item->order ? ($item->order->transaksi-?$item->order->transaksi->tgl_bupot:'') : '-' }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- {{ $data->links() }} --}}
        @if($data->hasMorePages())
            <button wire:click.prevent="loadMore" class="btn btn-sm btn-primary w-100">Load more</button>
        @endif
    </div>
</div>
