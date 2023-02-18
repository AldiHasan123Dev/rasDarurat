<div class="row px-3">
    <div class="col-6 mb-2 px-1">
        <label for="nama">Nama Customer</label>
        <input type="text" value="{{ $cus->nama ?? '' }}" name="nama" id="nama" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Nama Customer</label>
        <input type="text" value="{{ $cus->nama ?? '' }}" name="nama" id="nama" class="form-control" required>
    </div>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">{{ empty($cus)?'Tambah':'Update' }} Data</button>
    </div>
</div>
