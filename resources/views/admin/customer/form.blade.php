<div class="row px-3">
    <div class="col-6 mb-2 px-1">
        <label for="nama">Nama Customer</label>
        <input type="text" value="{{ $cus->nama ?? '' }}" name="nama" id="nama" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="marketing_id">Marketing</label>
        <select name="marketing_id" id="marketing_id" class="select form-control">
            <option value="">None</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="cs_id">CS</label>
        <select name="cs_id" id="cs_id" class="select form-control">
            <option value="">None</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nik">NIK</label>
        <input type="text" value="{{ $cus->nik ?? '' }}" name="nik" id="nik" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="npwp">NPWP</label>
        <input type="text" value="{{ $cus->npwp ?? '' }}" name="npwp" id="npwp" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="pic">PIC</label>
        <input type="text" value="{{ $cus->pic ?? '' }}" name="pic" id="pic" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="email">Email</label>
        <input type="email" value="{{ $cus->email ?? '' }}" name="email" id="email" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="telp">Telp</label>
        <input type="text" value="{{ $cus->telp ?? '' }}" name="telp" id="telp" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="hp">HP</label>
        <input type="text" value="{{ $cus->hp ?? '' }}" name="hp" id="hp" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="fax">Fax</label>
        <input type="text" value="{{ $cus->faq ?? '' }}" name="fax" id="fax" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="kota">Kota</label>
        <input type="text" value="{{ $cus->kota ?? '' }}" name="kota" id="kota" class="form-control" required>
    </div>
    {{-- <div class="col-12 mb-2 px-1">
        <div class="d-flex gap-2">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe" id="tipe1" value="pembayar" {{ empty($cus)?'checked':($cus->tipe=='pembayar'?'checked':'') }}>
                <label class="form-check-label" for="tipe1"> Pembayar</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe" id="tipe2" value="penerima" {{ empty($cus)?'':($cus->tipe=='penerima'?'checked':'') }}>
                <label class="form-check-label" for="tipe2"> Penerima</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe" id="tipe3" value="pengirim" {{ empty($cus)?'':($cus->tipe=='pengirim'?'checked':'') }}>
                <label class="form-check-label" for="tipe3"> Pengirim</label>
            </div>
        </div>
    </div> --}}
    <div class="col-12 mb-2 px-1">
        <label for="alamat">Alamat</label>
        <textarea name="alamat" id="alamat" cols="30" rows="3" class="form-control" required>{{ $cus->alamat??'' }}</textarea>
    </div>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">{{ empty($cus)?'Tambah':'Update' }} Data</button>
    </div>
</div>
