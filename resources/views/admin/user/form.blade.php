@php
    $roles = \App\Models\Role::all();
@endphp
<div class="row px-3">
    @if (Auth::user()->role_id==1)
    <div class="col-12 mb-2 px-1">
        <label for="role_id">Role</label>
        <select name="role_id" id="role_id" class="form-control" required>
                <option value=""></option>
            @if (empty($user))
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            @else
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id==$role->id?'selected':'' }}>{{ $role->name }}</option>
                @endforeach
            @endif
        </select>
    </div>
    @endif
    <div class="col-6 mb-2 px-1">
        <label for="name">Nama User</label>
        <input type="text" value="{{ $user->name ?? '' }}" name="name" id="name" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Email</label>
        <input type="email" value="{{ $user->email ?? '' }}" name="email" id="email" class="form-control" required>
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Password</label>
        <input type="password" name="password" id="password" class="form-control">
    </div>
    <div class="col-6 mb-2 px-1">
        <label for="nama">Telp.</label>
        <input type="text" value="{{ $user->phone ?? '' }}" name="phone" id="phone" class="form-control">
    </div>
    <div class="col-12 mb-2 px-1">
        <label for="nama">Alamat.</label>
        <textarea name="address" id="address" cols="30" rows="3" class="form-control">{{ $user->address ?? '' }}</textarea>
    </div>
    <div class="col-12 mb-2 px-1">
        <button type="submit" class="btn btn-success btn-sm">{{ empty($user)?'Tambah':'Update' }} Data</button>
    </div>
</div>
