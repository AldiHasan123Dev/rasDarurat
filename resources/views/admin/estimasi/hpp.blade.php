@extends('layouts.admin')
@section('style')
    <style>
        tr, td{
            font-size: .8rem;
            padding: 3px 4px !important;
        }
    </style>
@endsection
@section('content')
<div class="container">
    <div class="card p-3 shadow">
        <h4>Estimasi HPP</h4>
        <hr>
        <div class="row">
            <!-- Form input -->
            <div class="col-md-4">
                <div class="mb-2">
                    <label>Cont</label>
                    <select class="form-control" wire:model="cont" id="cont">
                        <option value="20" selected>20'</option>
                        <option value="40">40'</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Stuffing</label>
                    <select class="form-control" id="stuffing">
                        <option value="dalam" selected>DALAM</option>
                        <option value="luar">LUAR</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Dari</label>
                    <select class="form-control" id="dari">
                        <option value="">Pilih Lokasi Dari</option>
                        @foreach ($lokasi as $item)
                            <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label>Tujuan</label>
                    <select class="form-control" id="tujuan">
                        <option value="">Pilih Tujuan</option>
                        @foreach ($lokasiPelayaran as $item)
                            <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label>Pelayaran</label>
                    <select class="form-control" id="pelayaran">
                        <option value="">Pilih Pelayaran</option>
                        @foreach ($pelayarans as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label>Agen</label>
                    <select class="form-control" id="agen">
                        <option value="">Pilih Agen</option>
                        @foreach ($agens as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label>Pembayar</label>
                    <select class="form-control" id="pembayar_id">
                        <option value="">Pilih Pembayar</option>
                        @foreach ($customers as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="btnHitung" class="btn btn-primary btn-sm w-100">Hitung</button>
            </div>

            <!-- Hasil -->
            <div class="col-md-4" id="col-data"></div>
            <div class="col-md-4" id="col-hpp"></div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script>
        $("#pelayaran").select2();
        $("#stuffing").select2();
        $("#cont").select2();
        $("#dari").select2();
        $("#tujuan").select2();
        $("#agen").select2();
        $("#pembayar_id").select2();

        $('#tujuan').on('change', function() {
    let lokasi = $(this).val();

    $.get("{{ route('get.agens') }}", { lokasi_pelayaran: lokasi }, function(data) {
        let options = '';
        data.forEach(function(agen) {
            options += `<option value="${agen.id}">${agen.nama}</option>`;
        });
        $('#agen').html(options);
    });
});
let hppTableRendered = false; // cek apakah tabel HPP sudah pernah dibuat
let lastR = 0; // simpan nilai R terakhir
let lastMargin = 0; // simpan margin terakhir

$("#btnHitung").on("click", function () {
    $.ajax({
        url: "{{ route('estimasi.hpp.hitung') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            cont: $("#cont").val(),
            stuffing: $("#stuffing").val(),
            dari: $("#dari").val(),
            tujuan: $("#tujuan").val(),
            pelayaran: $("#pelayaran").val(),
            agen: $("#agen").val(),
            pembayar_id: $("#pembayar_id").val()
        },
        success: function (res) {
            if (res.active) {
                // render tabel data kiri
                let tableData = `<table class="table table-sm table-bordered border border-dark">`;
                for (let key in res.data) {
                    tableData += `
                        <tr>
                            <td>${key}</td>
                            <td><input type="number" class="px-3 py-1 text-end" value="${res.data[key]}"></td>
                        </tr>`;
                }
                tableData += `<tr class="text-end"><td><b>Jumlah</b></td><td><b>${res.total.toLocaleString()}</b></td></tr>`;
                tableData += `</table>`;
                $("#col-data").html(tableData);

                // simpan data awal
                window.hppData = res;

                if (!hppTableRendered) {
                    renderTableHppInitial(res);
                    hppTableRendered = true;
                } else {
                    // update nilai R dari server hanya jika user belum mengisi
                    if (!$("#inputR").is(":focus")) {
                        $("#inputR").val(res.r ?? lastR ?? 0);
                    }
                    let currentR = parseFloat($("#inputR").val()) || lastR || 0;
                    lastR = currentR;
                    updateTableHpp(res, currentR);
                }
            }
        }
    });
});

function renderTableHppInitial(res) {
    let tableHpp = `
        <table class="table table-sm table-bordered border border-dark">
            <tr class="text-end bg-light-info">
                <td><b>HPP</b></td><td id="hpp-val"><b>${res.hpp.toLocaleString()}</b></td>
            </tr>
            <tr class="text-end bg-light-info">
                <td><b>Margin</b></td><td id="margin-val"><b>${res.margin.toFixed(2)}</b></td>
            </tr>
            <tr class="text-end bg-light-info">
                <td></td>
                <td>
                    <input type="number" id="inputR" class="py-1 w-100 text-end" value="${res.r ?? 0}">
                </td>
            </tr>
            <tr class="text-end">
                <td><b>TOTAL</b></td><td id="total-val"><b>${res.total.toLocaleString()}</b></td>
            </tr>
            <tr class="text-end bg-light-warning">
                <td><b>PPH (2%)</b></td><td id="pph-val"><b>${res.pph.toLocaleString()}</b></td>
            </tr>
            <tr class="text-end bg-light-warning">
                <td><b>Include PPH</b></td><td id="total-pph-val"><b>${res.total_pph.toLocaleString()}</b></td>
            </tr>
            <tr class="text-end bg-light-danger">
                <td><b>PPN (1%)</b></td><td id="ppn-val"><b>${res.ppn.toLocaleString()}</b></td>
            </tr>
            <tr class="text-end bg-light-danger">
                <td><b>Include PPN</b></td><td id="total-ppn-val"><b>${res.total_ppn.toLocaleString()}</b></td>
            </tr>
        </table>
    `;
    $("#col-hpp").html(tableHpp);

    $("#inputR").off("input").on("input", function () {
        let rVal = parseFloat($(this).val()) || 0;
        lastR = rVal;
        updateTableHpp(window.hppData, rVal);
    });
}

function updateTableHpp(res, r) {
    let margin = res.hpp > 0 ? (r / res.hpp) * 100 : lastMargin;
    if (r > 0) lastMargin = margin;

    let total = r + res.hpp;
    let pph = Math.round(total * 0.02);
    let total_pph = Math.round(total + pph);
    let ppn = Math.round(total_pph * 0.01);
    let total_ppn = Math.round(total_pph + ppn);

    $("#hpp-val").html(`<b>${res.hpp.toLocaleString()}</b>`);
    $("#margin-val").html(`<b>${margin.toFixed(2)}</b>`);
    $("#total-val").html(`<b>${total.toLocaleString()}</b>`);
    $("#pph-val").html(`<b>${pph.toLocaleString()}</b>`);
    $("#total-pph-val").html(`<b>${total_pph.toLocaleString()}</b>`);
    $("#ppn-val").html(`<b>${ppn.toLocaleString()}</b>`);
    $("#total-ppn-val").html(`<b>${total_ppn.toLocaleString()}</b>`);
}

    </script>
@endsection

