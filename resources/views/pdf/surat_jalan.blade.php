<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Document</title>
    <link rel="stylesheet" href="{{ public_path('assets/css/bs.css') }}">
</head>
<body>
    <div id="print">
        <div class="header d-flex" style="gap:5px; width:100%; display:flex">
            <img src="{{ public_path('assets/img/ras.png') }}" alt="logo" style="height: 50px; width: 30%" class="img-fluid">
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
        <div class="mt-3 d-flex" style="font-size: .7rem; display:flex; justify-content:space-between">
            <div style="width: 70%">
                <table style="width: 300px; font-size:.7rem">
                    <tr>
                        <td style="width: 150px!important">No.</td>
                        <td style="">: <span id="d-no">{{ $data['no'] }}</span></td>
                    </tr>
                    <tr>
                        <td>Kendaraan No. Pol.</td>
                        <td>: <span id="d-nopol">{{ $data['nopol'] }}</span></td>
                    </tr>
                    <tr>
                        <td>Dari</td>
                        <td>: <b id="d-from">{{ $data['from'] }}</b></td>
                    </tr>
                    <tr>
                        <td>Kapal</td>
                        <td>: <span id="d-kapal">{{ $data['kapal'] }}</span></td>
                    </tr>
                    <tr>
                        <td>Container / Seal</td>
                        <td>: <span id="d-seal">{{ $data['seal'] }}</span></td>
                    </tr>
                </table>
            </div>
            <div style="width: 30%">
                <table class="text-right" style="width:100%; font-size:.7rem; position: relative;">
                    <tr><td>Kepada Yth:</td></tr>
                    <tr><td><span class="fw-bold" id="d-customer">{{ $data['penerima'] }}</span></td></tr>
                    <tr><td><u id="d-kota">{{ $data['kota'] }}</u></td></tr>
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
                        <td class="fw-bold">{{ $data['jumlah'] }}</td>
                        <td class="fw-bold">{{ $data['jenis_barang'] }}</td>
                        <td class="fw-bold">{{ $data['keterangan'] }}</td>
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
                <p>( <span id="d-cs">{{ $data['cs'] }}</span> )</p>
            </div>
        </div>
    </div>
</body>
</html>
