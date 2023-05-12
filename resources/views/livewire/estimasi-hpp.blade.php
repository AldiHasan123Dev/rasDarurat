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
                    <option value="{{ $item->id }}" {{ $loop->first?'selected':'' }}>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>Tujuan</label>
                <select class="form-control" wire:model="tujuan" id="tujuan">
                    @foreach ($lokasi as $item)
                    <option value="{{ $item->id }}" {{ $loop->first?'selected':'' }}>{{ $item->nama }}</option>
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
            <div class="mb-2">
                <label>Truck</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>UT</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>THC SBY</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>THC Tujuan</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>LSS</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Dooring</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Toeslag</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Seal</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>BL</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Dook/Fee</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>APBS</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Cleaning</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>OP</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Ijin 2x</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Karantina</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Plastik</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Tally</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Buruh</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Lassing</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Materai Pely</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Asuransi</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Pengiriman Dokumen</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Fee/RC Cust</label>
                <input type="text" id="id" class="form-control">
            </div>
            <div class="mb-2">
                <label>Lain-lain</label>
                <input type="text" id="id" class="form-control">
            </div>
        </div>
    </div>
</div>
