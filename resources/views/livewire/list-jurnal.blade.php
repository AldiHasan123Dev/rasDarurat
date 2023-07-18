<div class="row">
    {{-- @if (count($balances)>0)
    <div class="col-12">
        <div class="card shadow-lg my-2 p-2">
            <h4 class="text-danger">List Jurnal Tidak Balance!</h4>
            <hr>
            <table class="table-sm table">
                <thead>
                    <tr>
                        <td>No.</td>
                        <td>Nomor Jurnal</td>
                        <td>Total Debit</td>
                        <td>Total Credit</td>
                        <td>#</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($balances as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nomor }}</td>
                        <td>{{ number_format($item->debit) }}</td>
                        <td>{{ number_format($item->credit) }}</td>
                        <td><a href="{{ route('jurnal.edit',['jurnal'=>$item->nomor]) }}" class="btn btn-sm btn-primary">Edit</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif --}}
    <div class="col-12 mt-3">
        <div class="row">
            <div class="col-9">
                <div class="d-flex gap-2">
                    <b class="mt-2">Bulan: </b>
                    @foreach ($months as $idx => $item)
                        <a href="{{ route('jurnal.index',['month'=>sprintf('%02d',$idx+1)]) }}" wire:click="changeMonth({{ $idx+1 }})" class="{{ $idx+1==(int)$month?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">{{ $item }}</a>
                    @endforeach
                </div>
            </div>
            <div class="col-3">
                <select class="form-control px-3 py-1" wire:model="year" style="font-size:.8rem">
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2028">2028</option>
                    <option value="2029">2029</option>
                    <option value="2030">2030</option>
                </select>
            </div>
            <div class="col-6">
                <div class="my-3">
                    <label for="search">Search</label>
                    <input type="text" wire:model="search" class="form-control" placeholder="Cari berdasarkan nomor jurnal/keterangan/akun/job/tanggal">
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex gap-2 mt-5">
                    <b class="mt-2">Tipe: </b>
                    <a href="{{ route('jurnal.index',['tipe'=>'BB','month'=>request('month')]) }}" class="{{ $tipe=='BB'?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">BANK</a>
                    <a href="{{ route('jurnal.index',['tipe'=>'BK','month'=>request('month')]) }}" class="{{ $tipe=='BK'?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">KAS</a>
                    <a href="{{ route('jurnal.index',['tipe'=>'JNL','month'=>request('month')]) }}" class="{{ $tipe=='JNL'?'bg-light-success':'' }} text-center text-dark" style="border: solid 1px gray; width:50px; text-decoration:none">JNL</a>
                </div>
            </div>
        </div>
        <div class="table-responsive" style="height: 400px">
            <table data-rtc-resizable-table="table.{{ $month }}" class="data table table-sm mt-3 table-bordered" style="font-size: .7rem; white-space:nowrap">
                <thead>
                    <tr>
                        <th data-rtc-resizable="tanggal">Tanggal</th>
                        <th data-rtc-resizable="nomor">Nomor</th>
                        <th data-rtc-resizable="akun">No. Akun</th>
                        <th data-rtc-resizable="akun_name">Nama Akun</th>
                        <th data-rtc-resizable="container">Cont</th>
                        <th data-rtc-resizable="nopol">Nopol</th>
                        <th data-rtc-resizable="invoice">Invoice</th>
                        <th data-rtc-resizable="job">JOB</th>
                        <th data-rtc-resizable="keterangan">Keterangan</th>
                        <th data-rtc-resizable="debit">Debit</th>
                        <th data-rtc-resizable="credit">Credit</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr class="{{ $item->is_balance()?'':'bg-danger text-white' }}">
                            <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                            <td>{{ $item->nomor }}</td>
                            <td>{{ $item->coa->kode }}</td>
                            <td>{{ $item->coa->nama }}</td>
                            @if ($item->coa->kode=='1.1.3.1'||$item->coa->kode=='5.1.1'||$item->coa->kode=='1.1.6.2')
                                <td></td>
                            @else
                                <td>{{ $item->container ?? '-' }}</td>
                            @endif
                            <td>{{ $item->nopol ?? '-' }}</td>
                            <td>{{ $item->invoice ?? '-' }}</td>
                            @if ($item->order)
                                @if ($item->coa->kode=='1.1.3.1'||$item->coa->kode=='5.1.1'||$item->coa->kode=='1.1.6.2')
                                    <td>{{ $item->order->job }}</td>
                                @else
                                    <td>{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                @endif
                            @else
                                <td>-</td>
                            @endif
                            <td>{{ $item->nama }}</td>
                            <td>{{ number_format($item->debit,2,',','.') }}</td>
                            <td>{{ number_format($item->credit,2,',','.') }}</td>
                            <td><a href="{{ route('jurnal.edit',$item->nomor,['jurnal'=>$item->nomor]) }}" class="text-primary"><i class="fas fa-pencil"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- {{ $data->links() }} --}}
        @if($data->hasMorePages())
        <button wire:click.prevent="loadMore" class="btn btn-sm btn-primary w-100">Load more</button>
        @endif
        <table class="table table-sm mt-2">
            @if ($total_debit!=$total_credit)
                <tr>
                    <td colspan="2" class="text-center text-danger"><div class="alert alert-danger">JURNAL TIDAK BALANCE</div></td>
                </tr>
            @endif
            <tr>
                <td>Debit</td>
                <td>: {{ number_format($total_debit,2,',','.') }}</td>
            </tr>
            <tr>
                <td>Credit</td>
                <td>: {{ number_format($total_credit,2,',','.') }}</td>
            </tr>
        </table>
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
