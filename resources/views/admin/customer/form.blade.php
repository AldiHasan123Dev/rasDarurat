<div class="row px-3">
    <div class="col-12 mb-2 px-1">
        <label for="nama">Nama Customer</label>
        <input type="text" value="{{ $cus->nama ?? '' }}" name="nama" id="nama-{{ $cus->id??'' }}" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="marketing_id">Marketing</label>
        <select name="marketing_id" id="marketing_id-{{ $cus->id??'' }}" class="select form-control">
            <option value="">None</option>
            @foreach ($users as $user)
                <option {{ !empty($cus)?($cus->marketing_id==$user->id?'selected':''):'' }} value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="cs_id">CS</label>
        <select name="cs_id" id="cs_id-{{ $cus->id??'' }}" class="select form-control">
            <option value="">None</option>
            @foreach ($users as $user)
                <option {{ !empty($cus)?($cus->cs_id==$user->id?'selected':''):'' }} value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nik">NIK</label>
        <input type="text" value="{{ $cus->nik ?? '' }}" name="nik" id="nik-{{ $cus->id??'' }}" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="npwp">NPWP</label>
        <input type="text" value="{{ $cus->npwp ?? '' }}" name="npwp" id="npwp-{{ $cus->id??'' }}" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama_npwp">Nama NPWP</label>
        <input type="text" value="{{ $cus->nama_npwp ?? '' }}" name="nama_npwp" id="nama_npwp-{{ $cus->id??'' }}" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="alamat_npwp">Alamat NPWP</label>
        <input type="text" value="{{ $cus->alamat_npwp ?? '' }}" name="alamat_npwp" id="alamat_npwp-{{ $cus->id??'' }}" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="pic">PIC</label>
        <input type="text" value="{{ $cus->pic ?? '' }}" name="pic" id="pic-{{ $cus->id??'' }}" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="email">Email</label>
        <input type="email" value="{{ $cus->email ?? '' }}" name="email" id="email-{{ $cus->id??'' }}" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="telp">Telp</label>
        <input type="text" value="{{ $cus->telp ?? '' }}" name="telp" id="telp-{{ $cus->id??'' }}" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="hp">HP</label>
        <input type="text" value="{{ $cus->hp ?? '' }}" name="hp" id="hp-{{ $cus->id??'' }}" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="fax">Fax</label>
        <input type="text" value="{{ $cus->faq ?? '' }}" name="fax" id="fax-{{ $cus->id??'' }}" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="kota">Kota</label>
        <input type="text" value="{{ $cus->kota ?? '' }}" name="kota" id="kota-{{ $cus->id??'' }}" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="top">TOP (hari)</label>
        <input type="number" value="{{ $cus->top ?? '' }}" name="top" id="top-{{ $cus->id??'' }}" class="form-control">
    </div>
    {{-- <div class="col-12 mb-2 px-1">
        <div class="d-flex gap-2">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe" id="tipe1-{{ $cus->id??'' }}" value="pembayar" {{ empty($cus)?'checked':($cus->tipe=='pembayar'?'checked':'') }}>
                <label class="form-check-label" for="tipe1"> Pembayar</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe" id="tipe2-{{ $cus->id??'' }}" value="penerima" {{ empty($cus)?'':($cus->tipe=='penerima'?'checked':'') }}>
                <label class="form-check-label" for="tipe2"> Penerima</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe" id="tipe3-{{ $cus->id??'' }}" value="pengirim" {{ empty($cus)?'':($cus->tipe=='pengirim'?'checked':'') }}>
                <label class="form-check-label" for="tipe3"> Pengirim</label>
            </div>
        </div>
    </div> --}}
    <div class="col-12 mb-2 px-1">
        <label for="alamat">Alamat</label>
        <textarea name="alamat" id="alamat-{{ $cus->id??'' }}" cols="30" rows="3" class="form-control" required>{{ $cus->alamat??'' }}</textarea>
    </div>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">{{ empty($cus)?'Tambah':'Update' }} Data</button>
    </div>
</div>
