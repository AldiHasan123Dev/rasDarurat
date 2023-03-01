
<div id="print">
    <div class="header d-flex" style="gap:5px; width:100%">
        <img src="{{ asset('assets/img/ras.png') }}" alt="logo" style="height: 50px; width: 30%" class="img-fluid">
        <div style="width: 40%; margin-left:35px">
            <table style="font-size:.7rem">
                <tr><td class="fw-bold">PT. RAHMAT ALAM SAMUDRA</td></tr>
                <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
            </table>
        </div>
        <p class="fw-bold mt-3" style="width:30%; font-size:.7rem">SURAT JALAN / PENGANTAR</p>
    </div>
    <hr>
    <div class="d-flex justify-content-between mt-3" style="font-size: .7rem">
        <div style="width: 70%">
            <table style="width: 300px; font-size:.7rem">
                <tr>
                    <td style="width: 150px!important">No.</td>
                    <td style="">: <span id="d-no"></span></td>
                </tr>
                <tr>
                    <td>Kendaraan No. Pol.</td>
                    <td>: <span id="d-nopol"></span></td>
                </tr>
                <tr>
                    <td>Dari</td>
                    <td>: <b id="d-from"></b></td>
                </tr>
                <tr>
                    <td>Kapal</td>
                    <td>: <span id="d-kapal"></span></td>
                </tr>
                <tr>
                    <td>Container / Seal</td>
                    <td>: <span id="d-seal"></span></td>
                </tr>
            </table>
        </div>
        <div style="width: 30%">
            <table class="text-right position-relative" style="width:100%; font-size:.7rem">
                <tr><td>Kepada Yth:</td></tr>
                <tr><td><span class="fw-bold" id="d-customer"></span></td></tr>
                <tr><td><u id="d-kota"></u></td></tr>
            </table>
        </div>
    </div>
    <table class="border-dark border-bottom mt-3 w-100 text-center" style="font-size:.7rem">
        <thead>
            <tr class="border-top border-bottom border-dark">
                <th class="fw-bold" style="100px !important">JUMLAH</th>
                <th class="fw-bold">JENIS BARANG</th>
                <th class="fw-bold">KETERANGAN</th>
            </tr>
            <tbody id="list" style="height: 90px">
                <tr>
                    <td class="fw-bold"></td>
                    <td class="fw-bold"></td>
                    <td class="fw-bold"></td>
                </tr>
            </tbody>
        </thead>
    </table>
    <span style="font-size: .7rem"><b>Barang-barang tersebut diatas harap diterima dengan baik</b></span>
    <div class="d-flex mt-3 justify-content-between" style="font-size: .7rem">
        <div class="text-center">
            <b>Penerima</b>
            <br><br><br><br><br>
            <p>(..........................................)</p>
        </div>
        <div class="text-center">
            <b>Surabaya, {{ date('d F Y') }}</b>
            <br><br><br><br><br>
            <p>( <span id="d-cs"></span> )</p>
        </div>
    </div>
</div>
