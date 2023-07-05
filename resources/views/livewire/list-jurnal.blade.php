<div class="row">
    <div class="col-12 mt-3">
        <div class="row">
            <div class="col-9">
                <div class="d-flex gap-2">
                    <b class="mt-2">Bulan: </b>
                    @foreach ($months as $idx => $item)
                        <button wire:click="changeMonth({{ $idx+1 }})" class="{{ $idx+1==(int)$month?'bg-light-success':'' }}" style="background: transparent; border: solid 1px gray; width:50px">{{ $item }}</button>
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
        </div>
        <table class="table table-sm mt-3" style="font-size: .7rem; white-space:nowrap">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nomor</th>
                    <th>No. Akun</th>
                    <th>Nama Akun</th>
                    <th>JOB</th>
                    <th>Keterangan</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
                        <td>{{ $item->nomor }}</td>
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
                        <td><a href="{{ route('jurnal.edit',$item) }}" class="text-primary"><i class="fas fa-pencil"></i></a></td>
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
