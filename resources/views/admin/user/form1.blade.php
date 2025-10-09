@php
    $roles = \App\Models\Role::all();
@endphp
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
<div class="row px-3">
    @if (Auth::user()->role_id==1)
    <div class="col-12 mb-2 px-1">
        <label for="role_id">Role</label>
        <select name="role_id" id="role_id" class="form-control" required>
                <option value=""></option>
            @if (empty($uservaleg55))
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            @else
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ $uservaleg55->role_id==$role->id?'selected':'' }}>{{ $role->name }}</option>
                @endforeach
            @endif
        </select>
    </div>
    @endif
    <div class="col-6 mb-2 px-1">
        <label for="name">Nama User</label>
        <input type="text" value="{{ $uservaleg55->name ?? '' }}" name="name" id="name" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Email</label>
        <input type="email" value="{{ $uservaleg55->email ?? '' }}" name="email" id="email" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Password</label>
        <input type="password" name="password" id="password" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Telp.</label>
        <input type="text" value="{{ $uservaleg55->phone ?? '' }}" name="phone" id="phone" class="form-control">
    </div>
    <div class="col-12 mb-2 px-1">
        <label for="nama">Alamat.</label>
        <textarea name="address" id="address" cols="30" rows="3" class="form-control">{{ $uservaleg55->address ?? '' }}</textarea>
    </div>
     <div class="col-12 mb-2 px-1">
        <label for="nama">Kota Lahir</label>
        <input type="text" name="kota_lahir" id="kota_lahir" cols="30" rows="3" value="{{ $uservaleg55->kota_lahir ?? '' }}" class="form-control">
    </div>
    <div class="col-12 mb-2 px-1">
        <label for="nama">Tgl Lahir</label>
        <input type="date" name="tgl_lahir" id="tgl_lahir" cols="30" rows="3" value="{{ $uservaleg55->tgl_lahir ?? '' }}" class="form-control">
    </div>
        <div class="col-12 mb-2 px-1">
        <label for="nama">Tgl Masuk</label>
        <input type="date" name="tgl_masuk" id="tgl_masuk" cols="30" rows="3" value="{{ $uservaleg55->tgl_masuk ?? '' }}" class="form-control">
    </div>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">{{ empty($uservaleg55)?'Tambah':'Update' }} Data</button>
    </div>
</div>
