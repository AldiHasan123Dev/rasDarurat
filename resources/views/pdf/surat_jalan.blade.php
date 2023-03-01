<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Document</title>
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ public_path('/') }}assets/css/bs.css">
    <link rel="stylesheet" href="/assets/css/sj.css">
    <style>
        @font-face {
            font-family: 'OpenSans_Condensed-ExtraBold';
            src: url({{ storage_path('/fonts/OpenSans_Condensed-ExtraBold.ttf') }}) format("truetype");
            font-weight: 800;
            font-style: normal;
        }
        body, *{
            font-family: OpenSans_Condensed-ExtraBold;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div id="print">
        <table class="table" style="width:100%">
            <tr>
                <td style="width: 20%">
                    <img src="{{ public_path('/') }}assets/img/ras.png" alt="logo" class="img-fluid" style=" height: 50px; width: 100%;">
                </td>
                <td style="width: 50%">
                    <div style="margin-left:35px">
                        <table style="font-size:.7rem">
                            <tr><td>PT. RAHMAT ALAM SAMUDRA</td></tr>
                            <tr><td>Jl. Kalianak 55G, Surabaya</td></tr>
                            <tr><td>Telp & Fax 031.7495507 / 081.230.162.999</td></tr>
                        </table>
                    </div>
                </td>
                <td style="width: 30%">
                    <p class="mt-3" style="font-size:.7rem">SURAT JALAN / PENGANTAR</p>
                </td>
            </tr>
        </table>
        <hr>
        <div class="mt-3" style="font-size: .7rem;">
            <table>
                <tr>
                    <td style="width:40%">
                        <table style="font-size:.7rem">
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
                                <td>: <span id="d-from">{{ $data['from'] }}</span></td>
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
                    </td>
                    <td style="width: 30%"><p style="color: white">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Nam minima aliquid doloribus, magni ea illum eius totam, quia quod natus a ipsam explicabo repudiandae hic? Aliquam tenetur modi rerum vero.</p></td>
                    <td style="width: 30%">
                        <table class="text-right" style="width:100%; font-size:.7rem; position: relative;">
                            <tr><td>Kepada Yth:</td></tr>
                            <tr><td><span id="d-customer">{{ $data['penerima'] }}</span></td></tr>
                            <tr><td><u id="d-kota">{{ $data['kota'] }}</u></td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <table class="border-dark border-bottom mt-3 w-100 text-center" style="font-size:.7rem">
            <thead>
                <tr class="border-top border-bottom border-dark">
                    <th style="100px !important">JUMLAH</th>
                    <th>JENIS BARANG</th>
                    <th>KETERANGAN</th>
                </tr>
                <tbody id="list" style="height: 90px">
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr style="height: 90px">
                        <td>{{ $data['jumlah'] }}</td>
                        <td>{{ $data['jenis_barang'] }}</td>
                        <td>{{ $data['keterangan'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </thead>
        </table>
        <span style="font-size: .7rem"><b>Barang-barang tersebut diatas harap diterima dengan baik</b></span>
        <table>
            <tr>
                <td style="width: 30%">
                    <div class="text-center" style="font-size: .7rem">
                        <b>Penerima</b>
                        <br><br><br><br><br>
                        <p>(..........................................)</p>
                    </div>
                </td>
                <td style="width: 40%"><p style="color: white">Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas, voluptatem illo, corporis fugiat alias, aliquam blanditiis culpa saepe ratione id iste cupiditate quasi eveniet eligendi! Optio nisi rerum autem nobis.</p></td>
                <td style="width: 30%">
                    <div class="text-center" style="font-size: .7rem">
                        <b>Surabaya, {{ date('d F Y') }}</b>
                        <br><br><br><br><br>
                        <p>( <span id="d-cs">{{ $data['cs'] }}</span> )</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
