<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Invoice Template</title>

    <!-- Favicon -->
    <link rel="icon" href="./images/favicon.png" type="image/x-icon" />

    <!-- Invoice styling -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@800&display=swap');

        #print * {
            font-family: 'Open Sans', sans-serif;
        }

        @media print {
            @page {
                size: 8.5in 5.5in;
                margin: .6cm .5cm 0cm .5cm;
            }

            body * {
                visibility: hidden;
            }

            #print .header {
                margin-top: 50px;
            }

            #print,
            #print * {
                visibility: visible;
                font-family: 'Open Sans', sans-serif;
                font-size: 1rem !important;
                color: black !important;
            }

            #print {
                height: 100%;
                width: 100%;
                position: absolute;
                left: 0;
                top: -80px;
                font-family: 'Open Sans', sans-serif;
            }
        }

        body {
            /* font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; */
            font-family: 'Open Sans', sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 13px;
            line-height: 24px;
            /* font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; */
            font-family: 'Open Sans', sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        /* .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
            vertical-align: top;
        } */

        .invoice-box table tr.information table td {
            /* padding-bottom: 40px; */
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        .profile {
            font-size: 13px;
            vertical-align: top;
            line-height: 15px;
            font-weight: 600
        }

        .profile-cont {
            display: flex;
            flex-direction: column;
        }

        .t-inv {
            border: 2px solid #000000;
            text-align: center;
            padding: 1px 51px;
            font-size: 20px;
            font-weight: 600;
        }

        .info {
            padding: 0 !important;
        }

        .price {
            display: flex;
            justify-content: space-between;
        }

        .border {
            border: 2px solid #000000;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
    <div style="margin: 20px; justify-content: center;" class="d-flex">
        <button style="font-size: 20px" onclick="window.print()">PRINT</button>
    </div>

    <div class="invoice-box" id="print">
        <table>
            <tr class="top">
                <td colspan="2">
                    <div class="header d-flex mb-3" style="gap:5px; width:100%;">
                        <img src="{{ asset('assets/img/ras.png') }}" alt="logo" style="height: 50px; width: 30%"
                            class="img-fluid">
                        <div style="width: 40%; margin-left:35px">
                            <table style="font-size:.7rem">
                                <tr>
                                    <td style="padding: 0" class="fw-bold">PT. RAHMAT ALAM SAMUDRA</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0">Jl. Kalianak 55G, Surabaya</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0">Telp & Fax 031.7495507 / 081.230.162.999</td>
                                </tr>
                            </table>
                        </div>
                        <p class="fw-bold mt-3" style="width:30%; font-size:.7rem">SURAT JALAN / PENGANTAR</p>
                    </div>
                    {{-- <table>
                        <tr>
                            <td class="title" style="display: flex">
                                <img src="https://seeklogo.com/images/B/business-company-logo-C561B48365-seeklogo.com.png"
                                    alt="Company logo" style="width: 100px; max-width: 300px; max-height: 70px" />
                                <div class="profile-cont">
                                    <span class="profile">RAHMAT ALAM SAMUDERA</span>
                                    <span class="profile">Jl. Kalianak 55G Surabaya</span>
                                    <span class="profile">Telp & Fax 031 7495507 / 031 7495529</span>
                                    <span class="profile">Email : rahmatalamsamudera@gmail.com</span>
                                </div>
                            </td>

                            <td>
                                <div class="t-inv"><u>INVOICE</u></div>
                            </td>
                        </tr>
                    </table> --}}
                </td>
            </tr>

            <tr class="information">
                <td colspan="1">
                    <table>
                        <tr>
                            <td class="info">
                                <div style="display: flex">
                                    <span style="min-width: 170px">No Invoice</span>
                                    <span>: 234234243234</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="info">
                                <div style="display: flex">
                                    <span style="min-width: 170px">Kapal</span>
                                    <span>: MERATUD</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="info">
                                <div style="display: flex">
                                    <span style="min-width: 170px">Pelabuhan Tujuan</span>
                                    <span>: AMBON</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="info">
                                <div style="display: flex">
                                    <span style="min-width: 170px">Buat Pembayaran (Jenis)</span>
                                    <span>: PLASTIK</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td colspan="2" style="min-width: 200px; padding-right: 10px;">
                    <table>
                        <tr>
                            <td class="info">
                                <div>
                                    <span style="min-width: 50px">Customer</span>
                                    <span>: CV. Jaya Abadi</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="info">
                                <div>
                                    <span style="min-width: 50px">Alamat</span>
                                    <span>: Jl. Ahmad Yani No.117, Jemur Wonosari, Kec. Wonocolo, Kota SBY, Jawa Timur
                                        60237</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table>
            <tr class="heading">
                <td>No</td>
                <td>Uraian</td>
                <td>Koli</td>
                <td>Jumlah</td>
                <td>Tipe Tarif</td>
                <td>X</td>
                <td>Tarif</td>
                <td>Sub Total</td>
            </tr>
            @for ($i = 1; $i <= 5; $i++) <tr>
                <td>{{ $i }}</td>
                <td>PRODUK PRODUK</td>
                <td>1Koli</td>
                <td>1.00</td>
                <td>40' DRy</td>
                <td>X</td>
                <td>
                    <div class="price">
                        <span>Rp</span>
                        <span>292,342,234,234</span>
                    </div>
                </td>
                <td>
                    <div class="price">
                        <span>Rp</span>
                        <span>292,342,234,234</span>
                    </div>
                </td>
                </tr>
                @endfor
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="border" style="margin-top: 10px">Subtotal</td>
                    <td class="border">
                        <div class="price">
                            <span>Rp</span>
                            <span>292,342,234,234</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="border" style="margin-top: 10px">PPn 1,1%</td>
                    <td class="border">
                        <div class="price">
                            <span>Rp</span>
                            <span>292,342,234,234</span>
                        </div>
                    </td>
                </tr>

                <tr class="heading">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="border">TOTAL</td>
                    <td class="border">
                        <div class="price ">
                            <span>Rp</span>
                            <span>292,342,234,234</span>
                        </div>
                    </td>
                </tr>
                <tr class="heading">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="border">PPh (dengan kode pajak ....)</td>
                    <td class="border">
                        <div class="price ">
                            <span>Rp</span>
                            <span>292,342</span>
                        </div>
                    </td>
                </tr>
        </table>


        <table style="margin-top: 10px">
            <tr class="">
                <td style="line-height: 10px">Terbilang</td>
                <td style="line-height: 10px"></td>
                <td style="line-height: 10px">Dua Puluh Ribu</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="">
                <td style="line-height: 10px">Container</td>
                <td style="line-height: 10px"></td>
                <td style="line-height: 10px">ASJHAA324</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="">
                <td style="line-height: 10px">No Kode Group</td>
                <td style="line-height: 10px"></td>
                <td style="line-height: 10px">ASJHAA324</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <table>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                <td style="text-align: center;">Surabaya 12 februari 2023</td>
            </tr>
            <tr>
                <td style="line-height: 10px">
                    <div>
                        <span> Pembayaran dapat dilakukan melalui</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="line-height: 10px">
                    <div class="price">
                        <span>Rekening No</span>
                        <div>
                            <span></span>
                            <span style="min-width: 200px">123123123</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="line-height: 10px">
                    <div class="price">
                        <span>Atas Nama</span>
                        <div>
                            <span></span>
                            <span>SUYATNO</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="line-height: 10px">
                    <div class="price">
                        <span>Bank</span>
                        <div>
                            <span></span>
                            <span>Mandiri</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center;">Ulfiah</td>
            </tr>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.3.js"
        integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script>
        function printwes(){
            $('.invoice-box').printElement();
        }
    </script>
</body>

</html>