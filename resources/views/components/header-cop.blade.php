<div class="header d-flex" style="gap:5px; width:100%;">
    <img src="{{ asset('logo.png') }}" alt="logo" style="height: 60px; width: 30%" class="img-fluid">
    <div style="width: 40%; margin-left:35px">
        <table style="font-size:.7rem">
            <tr><td class="fw-bold">{{ $setting->name }}</td></tr>
            <tr><td>{{ $setting->address }}</td></tr>
            @if (($setting->phone) || ($setting->fax))
                <tr><td>Telp & Fax {{ $setting->phone }} / {{ $setting->fax }}</td></tr>
            @endif
            <tr><td>Email : {{ $setting->email }}</td></tr>
        </table>
    </div>
    {{ $slot }}
</div>
