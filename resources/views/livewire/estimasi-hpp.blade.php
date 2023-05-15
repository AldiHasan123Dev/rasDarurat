<div>
    <div class="row">
        <div class="col-4">
            <div class="mb-2">
                <label>Cont</label>
                <select class="form-control" wire:model="cont" id="cont">
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
                    <option value="{{ $item->lokasi_id }}" {{ $loop->first?'selected':'' }}>{{ $item->lokasi->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>Tujuan</label>
                <select class="form-control" wire:model="tujuan" id="tujuan">
                    @foreach ($lokasi as $item)
                    <option value="{{ $item->lokasi_id }}" {{ $loop->first?'selected':'' }}>{{ $item->lokasi->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>Pelayaran</label>
                <select class="form-control" wire:model="pelayaran" id="pelayaran">
                    @foreach ($pelayarans as $item)
                    <option value="{{ $item->id }}" {{ $loop->first?'selected':'' }}>{{ $item->nama }}</option>
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
                @php
                    $total = 0;
                @endphp
                @foreach ($data as $idx => $item)
                @php
                    $total += (int)$item;
                @endphp
                <tr>
                    <td>{{ $idx }}</td>
                    <td>{{ number_format($item) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td><b>Jumlah</b></td>
                    <td><b>{{ number_format($total) }}</b></td>
                </tr>
            </table>
            @endif
        </div>
        <div class="col-4">
            @if ($active)
            @php
                $r = $cont==20?600000:1300000;
            @endphp
            <table class="table table-sm table-bordered border border-dark">
                <tr class="bg-light-info">
                    <td><b>HPP</b></td>
                    <td><b>{{ number_format($total) }}</b></td>
                </tr>
                <tr class="bg-light-info">
                    <td><b>Margin</b></td>
                    <td><b>{{ number_format(($r/$total*100),2,'.','') }}</b></td>
                </tr>
                <tr class="bg-light-info">
                    <td><b></b></td>
                    <td><b>{{ number_format($r) }}</b></td>
                </tr>
                <tr>
                    <td><b>TOTAL</b></td>
                    <td><b>{{ number_format( $r+$total ) }}</b></td>
                </tr>
                <tr class="bg-light-warning">
                    <td><b>PPH (2%)</b></td>
                    <td><b>{{ number_format( ($r+$total)*0.02 ) }}</b></td>
                </tr>
                <tr class="bg-light-warning">
                    <td><b>Include PPH</b></td>
                    <td><b>{{ number_format( (($r+$total)*0.02) + ($r+$total)) }}</b></td>
                </tr>
                <tr class="bg-light-danger">
                    <td><b>PPN (1.1%)</b></td>
                    <td><b>{{ number_format( ((($r+$total)*0.02) + ($r+$total)) * 0.01 )  }}</b></td>
                </tr>
                <tr class="bg-light-danger">
                    <td><b>PPN (1.1%)</b></td>
                    <td><b>{{ number_format( (((($r+$total)*0.02) + ($r+$total)) * 0.01) +  (($r+$total)*0.02) + ($r+$total))  }}</b></td>
                </tr>
            </table>
            @endif
        </div>
    </div>
</div>
