@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="card p-3 shadow">
            <span>Merge No. Jurnal</span>
            <hr>
            <!-- Form untuk memilih tipe -->
            <div class="row">
                <!-- Form Tipe Jurnal Awal -->
                <form id="filterForm" method="GET" class="col-md-6">
                    <div class="mb-2">
                        <input type="hidden" name="tipe_tujuan" value="{{ request('tipe_tujuan') }}">
                        <label for="tipe_awal">Tipe Jurnal Awal</label>
                        <select name="tipe_awal" id="tipe_awal" class="form-control select2" required>
                            <option value="">Pilih Tipe</option>
                            @foreach ($tipe as $item)
                                <option value="{{ $item }}" {{ request('tipe_awal') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
    
                <!-- Form Tipe Jurnal Tujuan -->
                <form id="filterForm1" method="GET" class="col-md-6">
                    <div class="mb-2">
                        <input type="hidden" name="tipe_awal" value="{{ request('tipe_awal') }}">
                        <label for="tipe_tujuan">Tipe Jurnal Tujuan</label>
                        <select name="tipe_tujuan" id="tipe_tujuan" class="form-control select2" required>
                            <option value="">Pilih Tipe</option>
                            @foreach ($tipe as $item)
                                <option value="{{ $item }}" {{ request('tipe_tujuan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- Form untuk mengirim data merge -->
            <form action="{{ route('jurnal.merge.store') }}" method="POST" class="row">
                @csrf
                <div class="col-4 mb-2">
                    <label for="awal">No. Jurnal Awal</label>
                    <select name="awal" id="awal" class="form-control select2" required>
                        <option value="">Pilih Tipe Awal Terlebih Dahulu</option>
                        @foreach ($data as $item)
                            <option value="{{ $item }}" {{ request('awal') == $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 mb-2">
                    <label for="tujuan">No. Jurnal Tujuan</label>
                    <select name="tujuan" id="tujuan" class="form-control select2" required>
                        <option value="">Pilih Tipe Tujuan Terlebih Dahulu</option>
                        @foreach ($data1 as $item)
                            <option value="{{ $item }}" {{ request('tujuan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 mb-2">
                    <button type="submit" class="btn btn-sm btn-success mt-3" onclick="return confirm('Are you sure?')">Merge</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        // Inisialisasi Select2
        $('.select2').select2();

        // Event ketika tipe dipilih
        $('#tipe_awal').on('change', function () {
            // Submit form filter tipe untuk mendapatkan nomor jurnal
            $('#filterForm').submit();
        });
        $('#tipe_tujuan').on('change', function () {
            // Submit form filter tipe untuk mendapatkan nomor jurnal
            $('#filterForm1').submit();
        });
    });
</script>
@endsection
