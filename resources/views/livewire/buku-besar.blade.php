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
                <a href="{{ route('jurnal.buku_besar',['month'=>sprintf('%02d',$idx+1)]) }}" wire:click="changeMonth({{ $idx+1 }})" class="{{ $idx+1==(int)$month?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">{{ $item }}</a>
            @endforeach
        </div>
        <div class="my-3">
            <label for="search">Search</label>
            <input type="text" wire:model="search" class="form-control" placeholder="Cari berdasarkan nomor jurnal/keterangan/akun/job/tanggal">
        </div>
        <div class="table-responsives">
            <table data-rtc-resizable-table="table.{{ $month }}" class="table data table-bordered table-sm mt-3" style="font-size: .7rem; white-space:nowrap">
                <thead>
                    <tr>
                        <th data-rtc-resizable="tanggal">Tanggal</th>
                        <th data-rtc-resizable="no_jurnal">No. Jurnal</th>
                        <th data-rtc-resizable="no_akun">No. Akun</th>
                        <th data-rtc-resizable="akun">Akun</th>
                        @if ($coa->is_cont)
                        <th data-rtc-resizable="cont">No. Cont</th>
                        @endif
                        @if ($coa->is_nopol)
                        <th data-rtc-resizable="nopol">Nopol</th>
                        @endif
                        @if ($coa->is_nojob)
                        <th data-rtc-resizable="job">No. Job</th>
                        @endif
                        @if ($coa->is_invoice)
                        <th data-rtc-resizable="invoice">Invoice</th>
                        @endif
                        @if ($coa->is_invoice_trucking)
                        <th data-rtc-resizable="invoice">Invoice</th>
                        @endif
                        <th data-rtc-resizable="nama">Keterangan</th>
                        <th data-rtc-resizable="debit">Debit</th>
                        <th data-rtc-resizable="credit">Credit</th>
                        @if ($coa->is_nobg)
                        <th data-rtc-resizable="bg">No. BG</th>
                        @endif
                        @if ($coa->is_nobupot)
                        <th data-rtc-resizable="nobupot">No. Bupot PPh 23</th>
                        @endif
                        @if ($coa->is_tglbupot)
                        <th data-rtc-resizable="tglbupot">Tgl Bupot PPh 23</th>
                        @endif
                        <th data-rtc-resizable="tglbupot">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->data() as $item)
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
                        <td>{{ $item->coa->kode }}</td>
                        <td>{{ $item->coa->nama }}</td>
                        @if ($coa->is_cont)
                        <td>{{ $item->container ?? '-' }}</td>
                        @endif
                        @if ($coa->is_nopol)
                        <td>{{ $item->nopol ?? '-' }}</td>
                        @endif
                        @if ($coa->is_nojob)
                        <td>{{ $item->order ? $item->order->job.'-'.sprintf('%02d',$item->order->no_job) : '-' }}</td>
                        @endif
                        @if ($coa->is_invoice)
                        <td>{{ $item->invoice ?? '-' }}</td>
                        @endif
                        @if ($coa->is_invoice_trucking)
                        <td>{{ $item->invoice ?? '-' }}</td>
                        @endif
                        <td>{{ $item->nama }}</td>
                        <td>{{ number_format($item->debit,2,',','.') }}</td>
                        <td>{{ number_format($item->credit,2,',','.') }}</td>
                        @if ($coa->is_nobg)
                        <td>-</td>
                        @endif
                        @if ($coa->is_nobupot)
                        <td>{{ $item->order ? ($item->order->transaksi ? $item->order->transaksi->no_bupot :'') : '-' }}</td>
                        @endif
                        @if ($coa->is_tglbupot)
                        <td>{{ $item->order ? ($item->order->transaksi ? $item->order->transaksi->tgl_bupot :'') : '-' }}</td>
                        @endif
                        <td>{{ number_format($saldo_awal,2,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        {{-- {{ $data->links() }} --}}
        {{-- @if($data->hasMorePages())
            <button wire:click.prevent="loadMore" class="btn btn-sm btn-primary w-100">Load more</button>
        @endif --}}
    </div>
</div>
@push('scripts')
<script src="{{ asset('assets/js/resize-column.js') }}"></script>
<script>

    function load(){
        (function (window, ResizableTableColumns, undefined) {
            var store = window.store && window.store.enabled
                ? window.store
                : null;

            var els = document.querySelectorAll('table.data');
            for (var index = 0; index < els.length; index++) {
                var table = els[index];
                if (table['rtc_data_object']) {
                    continue;
                }

                var options = { store: store };
                if (table.querySelectorAll('thead > tr').length > 1) {
                    options.resizeFromBody = false;
                }

                new ResizableTableColumns(els[index], options);
            }

        })(window, window.validide_resizableTableColumns.ResizableTableColumns, void (0));
    }

    load();
</script>
@endpush
