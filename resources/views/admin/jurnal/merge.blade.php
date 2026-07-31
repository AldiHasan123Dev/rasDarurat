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
                        </select>
                    </div>
                </form>
    
                <!-- Form Tipe Jurnal Tujuan -->
                <form id="filterForm1" method="GET" class="col-md-6">
                    <div class="mb-2">
                        <input type="hidden" name="tipe_awal" value="{{ request('tipe_awal') }}">
                        <label for="tipe_tujuan">Tipe Jurnal Tujuan</label>
                        <select name="tipe_tujuan" id="tipe_tujuan" class="form-control select2" required>
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
                    </select>
                </div>
                <div class="col-4 mb-2">
                    <label for="tujuan">No. Jurnal Tujuan</label>
                    <select name="tujuan" id="tujuan" class="form-control select2" required>
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
$(function () {

    // ============================
    // TIPE AWAL
    // ============================
    $('#tipe_awal').select2({
        placeholder: 'Pilih Tipe',
        ajax: {
            url: '/api/jurnal/tipe',
            dataType: 'json',
            delay: 250,
            processResults: function (response) {

                return {
                    results: $.map(response.data, function (item) {
                        return {
                            id: item,
                            text: item
                        }
                    })
                };
            }
        }
    });

    // ============================
    // TIPE TUJUAN
    // ============================
    $('#tipe_tujuan').select2({
        placeholder: 'Pilih Tipe',
        ajax: {
            url: '/api/jurnal/tipe',
            dataType: 'json',
            delay: 250,
            processResults: function (response) {

                return {
                    results: $.map(response.data, function (item) {
                        return {
                            id: item,
                            text: item
                        }
                    })
                };
            }
        }
    });

    // ============================
    // NOMOR AWAL
    // ============================
    $('#awal').select2({
        placeholder: 'Pilih No. Jurnal',

        ajax: {

            url: '/api/jurnal/nomor',

            dataType: 'json',

            delay: 250,

            data: function (params) {

                return {
                    tipe: $('#tipe_awal').val(),
                    search: params.term
                };

            },

            processResults: function (response) {

                return {
                    results: $.map(response.data, function (item) {

                        return {
                            id: item,
                            text: item
                        }

                    })
                };

            }

        }

    });

    // ============================
    // NOMOR TUJUAN
    // ============================
    $('#tujuan').select2({
        placeholder: 'Pilih No. Jurnal',

        ajax: {

            url: '/api/jurnal/nomor',

            dataType: 'json',

            delay: 250,

            data: function (params) {

                return {
                    tipe: $('#tipe_tujuan').val(),
                    search: params.term
                };

            },

            processResults: function (response) {

                return {
                    results: $.map(response.data, function (item) {

                        return {
                            id: item,
                            text: item
                        }

                    })
                };

            }

        }

    });

    // ============================
    // RESET NOMOR JIKA TIPE BERUBAH
    // ============================
    $('#tipe_awal').change(function () {

        $('#awal').val(null).trigger('change');

    });

    $('#tipe_tujuan').change(function () {

        $('#tujuan').val(null).trigger('change');

    });

});

$('#awal').on('select2:opening', function (e) {

    if (!$('#tipe_awal').val()) {

        e.preventDefault();

        alert('Pilih tipe jurnal awal terlebih dahulu.');

    }

});

$('#tujuan').on('select2:opening', function (e) {

    if (!$('#tipe_tujuan').val()) {

        e.preventDefault();

        alert('Pilih tipe jurnal tujuan terlebih dahulu.');

    }

});

    $('#awal').select2({
    placeholder: 'Pilih No. Jurnal Awal',
    ajax: {
        url: '/api/jurnal/nomor',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                tipe: $('#tipe_awal').val(),
                search: params.term,
            };
        },
        processResults: function (response) {
            return {
                results: $.map(response.data, function(item) {
                    return {
                        id: item,
                        text: item
                    }
                })
            };
        },
        cache: true
    }
});

$('#tujuan').select2({
    placeholder: 'Pilih No. Jurnal Tujuan',
    ajax: {
        url: '/api/jurnal/nomor',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                tipe: $('#tipe_tujuan').val(),
                search: params.term,
            };
        },
        processResults: function (response) {
            return {
                results: $.map(response.data, function(item) {
                    return {
                        id: item,
                        text: item
                    }
                })
            };
        },
        cache: true
    }
});
</script>
@endsection
