@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="card p-3 shadow">
            <span>Merge No. Jurnal</span>
            <hr>
            <!-- Form untuk memilih tipe -->
            <form id="filterForm" method="GET">
                <div class="col-4 mb-2">
                    <label for="tipe">Tipe Jurnal</label>
                    <select name="tipe" id="tipe" class="form-control select2" required>
                        <option value="">Pilih Tipe</option>
                        @foreach ($tipe as $item)
                            <option value="{{ $item }}" {{ request('tipe') == $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Form untuk mengirim data merge -->
            <form action="{{ route('jurnal.merge.store') }}" method="POST" class="row">
                @csrf
                <div class="col-4 mb-2">
                    <label for="awal">No. Jurnal Awal</label>
                    <select name="awal" id="awal" class="form-control select2" required>
                        <option value="">Pilih Tipe Terlebih Dahulu</option>
                        @foreach ($data as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 mb-2">
                    <label for="tujuan">No. Jurnal Tujuan</label>
                    <select name="tujuan" id="tujuan" class="form-control select2" required>
                        <option value="">Pilih Tipe Terlebih Dahulu</option>
                        @foreach ($data as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
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
        $('#tipe').on('change', function () {
            // Submit form filter tipe untuk mendapatkan nomor jurnal
            $('#filterForm').submit();
        });
    });
</script>
@endsection
