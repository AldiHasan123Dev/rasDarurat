<div class="col-4">
    <div class="card p-3">
        <div class="mb-2">
            <label>Akun</label>
            <select class="form-control select2" style="font-size:.9rem !important">
                <option value=""></option>
                @foreach ($coa as $item)
                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <label>Tipe</label>
            <select wire:model="tipe" class="form-control select2" style="font-size:.9rem !important">
                <option value=""></option>
                <option value="debit">Debit</option>
                <option value="kredit">Kredit</option>
            </select>
        </div>
        <div class="mb-2">
            <button class="btn btn-success btn-sm w-100" type="button" wire:click="addColumn">Tambah</button>
        </div>
    </div>
</div>
<div class="col-12 mt-3">
    <div class="card p-2">
        <div class="row">
            <div class="col-12">
                <span class="fw-bold" style="font-size: .8rem !important">Debit Account</span>
                <hr>
                <table class="table table-sm">
                    <tr>
                        <td>#</td>
                        <td>Nomor</td>
                        <td>Akun</td>
                        <td>Keterangan</td>
                        <td>Debit</td>
                        <td>Credit</td>
                    </tr>
                    @for ($i = 0; $i < $idx; $i++)
                    <tr>
                        <td><input type="checkbox" name="" id=""></td>
                        <td><input type="text"></td>
                        <td> <select class="form-control select2" style="font-size:.9rem !important">
                            <option value=""></option>
                            @foreach ($coa as $item)
                            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                            @endforeach
                        </select></td>
                        <td><input type="text"></td>
                        <td><input type="number"></td>
                        <td><input type="number" disabled></td>
                    </tr>
                    @endfor
                </table>
            </div>
            <div class="col-12">
                <span class="fw-bold" style="font-size: .8rem !important">Credit Account</span>
                <hr>
                <table class="table table-sm">
                    <tr>
                        <td>#</td>
                        <td>Nomor</td>
                        <td>Akun</td>
                        <td>Keterangan</td>
                        <td>Debit</td>
                        <td>Credit</td>
                    </tr>
                    @for ($i = 0; $i < $idx; $i++)
                    <tr>
                        <td><input type="checkbox" name="" id=""></td>
                        <td><input type="text"></td>
                        <td><input type="text"></td>
                        <td><input type="text"></td>
                        <td><input type="number" disabled></td>
                        <td><input type="number"></td>
                    </tr>
                    @endfor
                </table>
            </div>
        </div>
    </div>
</div>
