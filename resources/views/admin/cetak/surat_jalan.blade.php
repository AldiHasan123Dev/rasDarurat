@extends('layouts.admin')
@section('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@800&display=swap');
        #print *{
            font-family: 'Open Sans', sans-serif;
        }
        @media print {
            @page {
                size: 8.5in 5.5in;
                margin: 0px;
            }
            body * {
                visibility: hidden;
            }
            #print, #print * {
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
    </style>
@endsection
@section('content')
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-4 col-12">
                <div class="card p-3">
                    <span>Form Surat Jalan</span>
                    <hr>
                    <form action="{{ route('cetak.pdf.suratJalan') }}" target="d_blank" method="get">
                        <div class="row">
                            <div class="col-12 mb-2 px-2">
                                <label for="cs">Customer Service</label>
                                <input type="text" name="cs" id="cs" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="no">No. Surat</label>
                                <input type="text" name="no" id="no" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="nopol">Kendaraan No. Pol</label>
                                <input type="text" name="nopol" id="nopol" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="from">Dari</label>
                                <input type="text" name="from" id="from" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="kapal">Kapal</label>
                                <input type="text" name="kapal" id="kapal" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="seal">Cont / Seal</label>
                                <input type="text" name="seal" id="seal" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="jenis_barang">Jenis Barang</label>
                                <input type="text" name="jenis_barang" onkeyup="barang()" class="form-control barang">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="jumlah">Jumlah</label>
                                <input type="text" name="jumlah" onkeyup="barang()" class="form-control barang">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="keterangan">Keterangan</label>
                                <input type="text" name="keterangan" onkeyup="barang()" class="form-control barang">
                            </div>
                            <hr>
                            <div class="col-12 mb-2 px-2">
                                <label for="penerima">Kepada</label>
                                <select id="penerima" class="form-control" name="penerima">
                                    <option value="">none</option>
                                    @foreach ($penerima as $user)
                                        <option value="{{$user->id}}">{{ $user->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <hr>
                        </div>
                        {{-- <button class="btn btn-sm btn-success mt-3" type="submit"><i class="fas fa-print"></i> Print</button> --}}
                        <button class="btn btn-sm btn-success mt-3" type="button" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8 col-12 mt-3 p-2 bg-white">
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
                            <table style="width: 300px">
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
                            <table class="text-right position-relative" style="width:100%">
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
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#penerima').select2();
        $('#no').keyup(function (e) {
            $('#d-no').html($(this).val());
        });
        $('#nopol').keyup(function (e) {
            $('#d-nopol').html($(this).val());
        });
        $('#from').keyup(function (e) {
            $('#d-from').html($(this).val());
        });
        $('#kapal').keyup(function (e) {
            $('#d-kapal').html($(this).val());
        });
        $('#seal').keyup(function (e) {
            $('#d-seal').html($(this).val());
        });
        $('#customer').keyup(function (e) {
            $('#d-customer').html($(this).val());
        });
        $('#kota').keyup(function (e) {
            $('#d-kota').html($(this).val());
        });
        $('#cs').keyup(function (e) {
            $('#d-cs').html($(this).val());
        });

        function barang(){
            var jenis = $("input[name='jenis_barang']").val();
            var jumlah = $("input[name='jumlah']").val();
            var keterangan = $("input[name='keterangan']").val();
            // var jenis = $("input[name='jenis_barang[]']").map(function(){return $(this).val();}).get();
            // var jumlah = $("input[name='jumlah[]']").map(function(){return $(this).val();}).get();
            // var keterangan = $("input[name='keterangan[]']").map(function(){return $(this).val();}).get();
            // let html = '';
            // $.each(jenis, function (idx, item) {
            //     html = html + `
            //                     <tr>
            //                         <td class="fw-bold">${jumlah[idx]}</td>
            //                         <td class="fw-bold">${item}</td>
            //                         <td class="fw-bold">${keterangan[idx]}</td>
            //                     </tr>`
            // });
            let html = `
                <tr>
                    <td class="fw-bold">${jumlah}</td>
                    <td class="fw-bold">${jenis}</td>
                    <td class="fw-bold">${keterangan}</td>
                </tr>`
            $('#list').html(html);
        };

        function addBarang(){
            let html = $('#res').html();
            $('#res').append(html);
        }

        $('#penerima').change(function (e) {
            var val = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('api.customer.getOne') }}",
                data: {customer_id:val},
                success: function (response) {
                    var data = response;
                    $('#d-customer').html(data.nama);
                    $('#d-kota').html(" "+data.kota);
                }
            });
        });
    </script>
@endsection

