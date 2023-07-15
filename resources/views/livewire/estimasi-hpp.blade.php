<div>
    <div class="row">
        <div class="col-4">
            <div class="mb-2">
                <label>Cont</label>
                <select class="form-control" wire:change="changeCont" wire:model="cont" id="cont">
                    <option value="20" selected>20'</option>
                    <option value="40">40'</option>
                </select>
            </div>
            <div class="mb-2">
                <label>Stuffing</label>
                <select class="form-control" wire:model="stuffing" id="stuffing">
                    <option value="dalam" selected>DALAM</option>
                    <option value="luar">LUAR</option>
                </select>
            </div>
            <div class="mb-2">
                <label>Dari</label>
                <select class="form-control" wire:model="dari" id="dari">
                    @foreach ($lokasi as $item)
                    <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>Tujuan</label>
                <select class="form-control" wire:model="tujuan" wire:change="changeTujuan" id="tujuan">
                    @foreach ($lokasiPelayaran as $item)
                    <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>Pelayaran</label>
                <select class="form-control" wire:model="pelayaran" id="pelayaran">
                    @foreach ($pelayarans as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>Agen</label>
                <select class="form-control" wire:model="agen" id="agen">
                    @foreach ($agens as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>Pembayar</label>
                <select class="form-control" wire:model="pembayar_id" id="pembayar_id">
                    @foreach ($customers as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <button type="button" class="btn btn-primary btn-sm w-100" wire:click="hitung()">Hitung</button>
            </div>
        </div>
        <div class="col-4">
            @if ($active)
            <table class="table table-sm table-bordered border border-dark">
                @foreach ($data as $idx => $item)
                <tr>
                    <td>{{ $idx }}</td>
                    <td><input type="number" class="px-3 py-1" style="text-align: right" wire:keyup="hitungData" wire:change="hitungData" wire:model="data.{{ $idx }}" value="{{ $item }}"></td>
                </tr>
                @endforeach
                <tr class="text-end">
                    <td><b>Jumlah</b></td>
                    <td><b>{{ number_format($total) }}</b></td>
                </tr>
            </table>
            @endif
        </div>
        <div class="col-4">
            @if ($active)
            <table class="table table-sm table-bordered border border-dark">
                <tr class="text-end bg-light-info">
                    <td><b>HPP</b></td>
                    <td><b>{{ number_format($hpp) }}</b></td>
                </tr>
                <tr class="text-end bg-light-info">
                    <td><b>Margin</b></td>
                    <td><b>{{ number_format($margin,2,'.','') }}</b></td>
                </tr>
                <tr class="text-end bg-light-info">
                    <td><b></b></td>
                    <td><input type="number" class="py-1" style="text-align: right" wire:keyup="hitungData" wire:change="hitungData" wire:model="r" value="{{ $r }}"></td>
                </tr>
                <tr class="text-end">
                    <td><b>TOTAL</b></td>
                    <td><b>{{ number_format( $total ) }}</b></td>
                </tr>
                <tr class="text-end bg-light-warning">
                    <td><b>PPH (2%)</b></td>
                    <td><b>{{ number_format($pph) }}</b></td>
                </tr>
                <tr class="text-end bg-light-warning">
                    <td><b>Include PPH</b></td>
                    <td><b>{{ number_format($total_pph) }}</b></td>
                </tr>
                <tr class="text-end bg-light-danger">
                    <td><b>PPN (1.1%)</b></td>
                    <td><b>{{ number_format($ppn)  }}</b></td>
                </tr>
                <tr class="text-end bg-light-danger">
                    <td><b>Include PPN</b></td>
                    <td><b>{{ number_format($total_ppn)  }}</b></td>
                </tr>
            </table>
            @endif
        </div>
    </div>
</div>
