@extends('layouts.admin')
@section('style')
    <style>
        tr, td{
            font-size: .8rem;
            padding: 3px 4px !important;
        }

        #hpp-container {
    display: flex;
    gap: 20px; /* jarak antar kolom */
    align-items: flex-start; /* biar sejajar di atas */
}
#col-data-left, #col-data-right, #col-hpp {
    flex: 1;
}
.table-sm td, .table-sm th {
    padding: 0.3rem;
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
                    <label>Pelayaran</label>
                    <select class="form-control" id="pelayaran">
                        <option value="">Pilih Pelayaran</option>
                        @foreach ($pelayarans as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
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
                    <label>Pelabuhan Awal</label>
                    <select class="form-control" id="dari">
                        <option value="">Pilih Pelabuhan Awal</option>
                        @foreach ($lokasi as $item)
                            <option value="{{ $item->nama }}" 
                                {{ strtolower($item->nama) == 'surabaya' ? 'selected' : '' }}>
                                {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label>Pelabuhan Tujuan</label>
                    <select class="form-control" id="tujuan">
                        <option value="">Pilih Pelabuhan Tujuan</option>
                        @foreach ($lokasiPelayaran as $item)
                            <option value="{{ $item->nama }}">{{ $item->nama }}</option>
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
              <div class="mb-2">
                    <label>Penerima</label>
                    <select class="form-control" id="penerima_id">
                        <option value="">Pilih Penerima</option>
                        @foreach ($penerima as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="btnHitung" class="btn btn-primary btn-sm w-100">Hitung</button>
                 <button type="button" id="btnExport" class="btn btn-success btn-sm w-100 mt-2">Export Excel</button>
            </div>

    <!-- Kolom kiri (form + tabel kiri) -->
    <div id="col-data-left"></div>
    
    <!-- Kolom kanan untuk sisa biaya -->
    <div id="col-data-right"></div>
    
    <!-- Kolom HPP -->
    <div id="col-hpp"></div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    $("#pelayaran").select2();
    $("#stuffing").select2();
    $("#cont").select2();
    $("#dari").select2();
    $("#tujuan").select2();
    $("#agen").select2();
    $("#pembayar_id").select2();
    $("#penerima_id").select2();

   $("#btnExport").on("click", function () {
    if (typeof XLSX === "undefined") {
        alert("Library XLSX belum ter-load.");
        return;
    }

    // Fungsi untuk ambil isi tabel (dengan input diganti value) → array of arrays
    function tableToArray(selector) {
        let table = document.querySelector(selector);
        if (!table) return [];

        let data = [];
        table.querySelectorAll("tr").forEach(tr => {
            let row = [];
            tr.querySelectorAll("th, td").forEach(td => {
                let input = td.querySelector("input, select, textarea");
                if (input) {
                    if (input.type === "checkbox") {
                        row.push(input.checked ? "✔" : "");
                    } else {
                        row.push(input.value || "");
                    }
                } else {
                    row.push(td.innerText.trim());
                }
            });
            data.push(row);
        });
        return data;
    }

    let allData = [];

    // Ambil data dari tabel kiri
    if ($("#col-data-left").length) {
        allData = allData.concat(tableToArray("#col-data-left"));
        allData.push([]); // baris kosong pemisah
    }

    // Ambil data dari tabel kanan
    if ($("#col-data-right").length) {
        allData = allData.concat(tableToArray("#col-data-right"));
        allData.push([]);
    }

    // Ambil data dari tabel HPP
    if ($("#col-hpp").length) {
        allData = allData.concat(tableToArray("#col-hpp"));
        allData.push([]);
    }

    // Buat workbook & worksheet
    let wb = XLSX.utils.book_new();
    let ws = XLSX.utils.aoa_to_sheet(allData);

    XLSX.utils.book_append_sheet(wb, ws, "Estimasi HPP");

    // Simpan file
    XLSX.writeFile(wb, "Estimasi_HPP.xlsx");
});


    $('#tujuan').on('change', function() {
        let lokasi = $(this).val();
        $.get("{{ route('get.agens') }}", { lokasi_pelayaran: lokasi }, function(data) {
            let options = '<option value="">Pilih Agen</option>';
            data.forEach(function(agen) {
                options += `<option value="${agen.id}">${agen.nama}</option>`;
            });
            $('#agen').html(options);
        });
    });

    $('#agen').on('change', function() {
        let agen = $(this).val();
        $.get("{{ route('get.penerima') }}", { penerima: agen }, function(data) {
            let options = '';
            data.forEach(function(penerima) {
                options += `<option value="${penerima.id}">${penerima.nama}</option>`;
            });
            $('#penerima_id').html(options);
        });
    });

    let hppTableRendered = false;
    let lastR = 0;
    let lastMargin = 0;

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
                pembayar_id: $("#pembayar_id").val(),
                penerima_id: $("#penerima_id").val()
            },
            success: function (res) {
                if (res.active) {
                    window.hppData = res;

                    renderTabelKiriSplit(res);

                    if (!hppTableRendered) {
                        renderTableHppInitial(res);
                        hppTableRendered = true;
                    } else {
                        if (!$("#inputR").is(":focus")) {
                            $("#inputR").val(formatNumber(res.r ?? lastR ?? 0));
                        }
                        let currentR = parseFloat(removeFormat($("#inputR").val())) || lastR || 0;
                        lastR = currentR;
                        updateTableHpp(res, currentR);
                    }
                }
            }
        });
    });

    // --- Utility untuk format angka ---
    function formatNumber(num) {
        return Number(num).toLocaleString();
    }
    function removeFormat(str) {
        return str.toString().replace(/,/g, "");
    }

    function renderTabelKiriSplit(res) {
        let keys = Object.keys(res.data);
        let half = 14; // ambil 14 item di kolom kiri

        // TABEL KIRI
        let tableLeft = `<table class="table table-sm table-bordered border border-dark">`;
        for (let i = 0; i < Math.min(half, keys.length); i++) {
            let key = keys[i];
            let highlight = key.toUpperCase().includes("POD") ? 'style="background-color: yellow;"' : "";
            tableLeft += `
                <tr ${highlight}>
                    <td>${key}</td>
                    <td>
                        <input type="text" class="px-3 py-1 text-end biaya-input" 
                               data-key="${key}" 
                               value="${formatNumber(res.data[key])}">
                    </td>
                </tr>`;
        }
        tableLeft += `</table>`;
        $("#col-data-left").html(tableLeft);

        // TABEL KANAN
        let tableRight = `<table class="table table-sm table-bordered border border-dark">`;
        for (let i = half; i < keys.length; i++) {
            let key = keys[i];
            let highlight = key.toUpperCase().includes("POD") ? 'style="background-color: yellow;"' : "";
            tableRight += `
                <tr ${highlight}>
                    <td>${key}</td>
                    <td>
                        <input type="text" class="px-3 py-1 text-end biaya-input" 
                               data-key="${key}" 
                               value="${formatNumber(res.data[key])}">
                    </td>
                </tr>`;
        }
        tableRight += `<tr class="text-end">
            <td><b>Jumlah</b></td>
            <td id="jumlah-val-right"><b>0</b></td>
        </tr>`;
        tableRight += `</table>`;
        $("#col-data-right").html(tableRight);

        // Listener dinamis format angka
        $(".biaya-input").on("focus", function () {
            $(this).val(removeFormat($(this).val())); // tampilkan angka mentah
        });

        $(".biaya-input").on("blur", function () {
            let val = parseFloat(removeFormat($(this).val())) || 0;
            $(this).val(formatNumber(val));
        });

        $(".biaya-input").on("input", function () {
            let key = $(this).data("key");
            let val = parseFloat(removeFormat($(this).val())) || 0;
            window.hppData.data[key] = val;
            updateJumlah();
            updateTableHpp(window.hppData, lastR);
        });

        updateJumlah();
    }

    function updateJumlah() {
        let keys = Object.keys(window.hppData.data);
        let totalAll = 0;

        for (let i = 0; i < keys.length; i++) {
            totalAll += parseFloat(window.hppData.data[keys[i]]) || 0;
        }

        // Hanya update jumlah di tabel kanan
        $("#jumlah-val-right").html(`<b>${formatNumber(totalAll)}</b>`);

        // Simpan ke HPP supaya perhitungan lanjut tetap benar
        window.hppData.hpp = totalAll;
    }

    function renderTableHppInitial(res) {
    let tableHpp = `
        <table class="table table-sm table-bordered border border-dark">
            <tr class="text-end bg-light-info">
                <td><b>HPP</b></td><td id="hpp-val"><b>${formatNumber(res.hpp)}</b></td>
            </tr>
            <tr class="text-end bg-light-info">
                <td><b>Margin</b></td><td id="margin-val"><b>${res.margin.toFixed(2)}</b></td>
            </tr>
            <tr class="text-end bg-light-info">
                <td></td>
                <td>
                    <input type="text" id="inputR" class="py-1 w-100 text-end" 
                           value="${formatNumber(res.r ?? 0)}">
                </td>
            </tr>
            <tr class="text-end">
                <td><b>TOTAL</b></td><td id="total-val"><b>${formatNumber(res.total)}</b></td>
            </tr>
            <tr class="text-end bg-light-warning">
                <td><b>PPH (2%)</b></td><td id="pph-val"><b>${formatNumber(res.pph)}</b></td>
            </tr>
            <tr class="text-end bg-light-warning">
                <td><b>Include PPH (Tarif Excl. PPN)</b></td><td id="total-pph-val"><b>${formatNumber(res.total_pph)}</b></td>
            </tr>
            <tr class="text-end bg-light-danger">
                <td><b>PPN (1.1%)</b></td><td id="ppn-val"><b>${formatNumber(res.ppn)}</b></td>
            </tr>
            <tr class="text-end bg-light-danger">
                <td><b>Tarif Include PPN</b></td><td id="total-ppn-val"><b>${formatNumber(res.total_ppn)}</b></td>
            </tr>
        </table>
    `;
    $("#col-hpp").html(tableHpp);

    // 🟢 FIX: set nilai awal lastR
    lastR = parseFloat(removeFormat($("#inputR").val())) || 0;

    // Format inputR
    $("#inputR").on("focus", function () {
        $(this).val(removeFormat($(this).val()));
    });

    $("#inputR").on("blur", function () {
        let val = parseFloat(removeFormat($(this).val())) || 0;
        $(this).val(formatNumber(val));
    });

    $("#inputR").off("input").on("input", function () {
        let rVal = parseFloat(removeFormat($(this).val())) || 0;
        lastR = rVal;
        updateTableHpp(window.hppData, rVal);
    });

    // 🟢 FIX: langsung update margin & total pertama kali
    updateTableHpp(res, lastR);
}


    function updateTableHpp(res, r) {
        let margin = res.hpp > 0 ? (r / res.hpp) * 100 : lastMargin;
        if (r > 0) lastMargin = margin;

        let total = r + res.hpp;
        let pph = Math.round(total * 0.02);
        let total_pph = Math.round(total + pph);
        let ppn = Math.round(total_pph * 0.011 );
        let total_ppn = Math.round(total_pph + ppn);

        $("#hpp-val").html(`<b>${formatNumber(res.hpp)}</b>`);
        $("#margin-val").html(`<b>${margin.toFixed(2)}</b>`);
        $("#total-val").html(`<b>${formatNumber(total)}</b>`);
        $("#pph-val").html(`<b>${formatNumber(pph)}</b>`);
        $("#total-pph-val").html(`<b>${formatNumber(total_pph)}</b>`);
        $("#ppn-val").html(`<b>${formatNumber(ppn)}</b>`);
        $("#total-ppn-val").html(`<b>${formatNumber(total_ppn)}</b>`);
    }
</script>
@endsection

