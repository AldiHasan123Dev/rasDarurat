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
                                    @foreach ($coa as $item)
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
                    <th>No. Akun</th>
                    <th>Nama Akun</th>
                    <th>JOB</th>
                    <th>Keterangan</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4">-</td>
                    <td>SALDO AWAL</td>
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
                        <td>{{ $item->coa->kode }}</td>
                        <td>{{ $item->coa->nama }}</td>
                        @if ($item->order)
                            <td>{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{ $item->nama }}</td>
                        <td>{{ number_format($item->debit,2,',','.') }}</td>
                        <td>{{ number_format($item->credit,2,',','.') }}</td>
                        <td class="text-end">{{ number_format($saldo_awal,2,',','.') }}</td>
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
