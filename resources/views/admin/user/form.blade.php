<div class="row px-3">
    <div class="col-6 mb-2 px-1">
        <label for="name">Nama User</label>
        <input type="text" value="{{ $cus->name ?? '' }}" name="name" id="name" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Email</label>
        <input type="email" value="{{ $cus->email ?? '' }}" name="email" id="email" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Password</label>
        <input type="password" name="password" id="password" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Telp.</label>
        <input type="text" value="{{ $cus->phone ?? '' }}" name="phone" id="phone" class="form-control" required>
    </div>
    <div class="col-12 mb-2 px-1">
        <label for="nama">Alamat.</label>
        <textarea name="address" id="address" cols="30" rows="3" class="form-control" required>{{ $cus->address ?? '' }}</textarea>
    </div>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">{{ empty($cus)?'Tambah':'Update' }} Data</button>
    </div>
</div>
