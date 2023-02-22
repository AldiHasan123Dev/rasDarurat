@extends('layouts.admin')
@section('style')
    <style>
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');
            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
            }
            #print, #print * {
                visibility: visible;
            }
            #print {
                width: 100%;
                position: absolute;
                left: 0;
                top: -80px;
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
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-12 mb-2 px-2">
                                <label for="cs">Nama CS</label>
                                <input type="text" name="cs" id="cs" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="pengirim">Pengirim</label>
                                <select id="pengirim" class="form-control">
                                    <option value="">none</option>
                                    @foreach ($pengirim as $user)
                                        <option value="{{$user->id}}">{{ $user->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="penerima">Penerima</label>
                                <select id="penerima" class="form-control">
                                    <option value="">none</option>
                                    @foreach ($penerima as $user)
                                        <option value="{{$user->id}}">{{ $user->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="barang">Nama Barang</label>
                                <input type="text" name="barang" id="barang" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="sail">No. Container</label>
                                <input type="text" name="sail" id="sail" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="kendaraan">Kendaraan</label>
                                <input type="text" name="kendaraan" id="kendaraan" class="form-control">
                            </div>
                            <div class="col-6 mb-2 px-2">
                                <label for="kapal">Dengan Kapal</label>
                                <select name="kapal" id="kapal" class="form-control">
                                    <option value="">none</option>
                                    @foreach ($jadwal_kapal as $item)
                                        <option value="{{ $item->id }}">{{ $item->kapal->nama }} || ETD {{ date('d/m/y',strtotime($item->etd)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-2 px-2">
                                <label for="tujuan">Tujuan</label>
                                <input type="text" name="tujuan" id="tujuan" class="form-control">
                            </div>
                        </div>
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
                        <div style="width:30%; ">
                            <table style="font-size: .7rem; font-weight:bold">
                                <tr><td>PICK UP ORDER</td></tr>
                                <tr class="border-top border-dark"><td>ORDER PENGAMBILAN BARANG</td></tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mt-3" style="font-size: .7rem">
                        <div style="width: 70%">
                            <table>
                                <tr>
                                    <td style="width: 150px!important">Kepada Yth</td>
                                    <td style="">: (Pengirim)</td>
                                </tr>
                                <tr>
                                    <td colspan="2" id="pengirim-nama"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" id="pengirim-phone"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" id="pengirim-alamat"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" id="pengirim-kota"></td>
                                </tr>
                            </table>
                        </div>
                        <div style="width: 30%">
                            <table class="text-right position-relative" style="width:100%">
                                <tr>
                                    <td><span id="penerima-nama"></span>(Penerima)</td>
                                </tr>
                                <tr>
                                    <td id="penerima-kota"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <p style="font-size: .7rem">Dengan Hormat, <br> Bersama ini mohon dapat diserahkan</p>
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 100px;">Barang</td>
                                    <td>: <span id="d-barang"></span></td>
                                </tr>
                                <tr>
                                    <td>No. Cont</td>
                                    <td>: <span id="d-sail"></span></td>
                                </tr>
                                <tr>
                                    <td>Kendaraan</td>
                                    <td>: <span id="d-kendaraan"></span></td>
                                </tr>
                            </table>
                        </div>
                        <div style="margin-right: 100px">
                            <p style="font-size: .7rem">Barang ini rencana akan termuat</p>
                            <table style="font-size: .7rem">
                                <tr>
                                    <td style="width: 100px">Dengan Kapal</td>
                                    <td>: <span id="d-kapal"></span></td>
                                </tr>
                                <tr>
                                    <td>ETD Tanggal</td>
                                    <td>: <span id="d-etd"></span></td>
                                </tr>
                                <tr>
                                    <td>Tujuan</td>
                                    <td>: <span id="d-tujuan"></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <span style="font-size: .7rem">Demikian Penyampaian dari kami, mohon dibantu untuk pemuatanya. Terima kasih atas perhatianya</span>
                    <div class="d-flex mt-3 justify-content-between" style="font-size: .7rem; margin-right: 70px">
                        <div></div>
                        <div class="text-center">
                            <span>Surabaya, {{ date('d F Y') }}</b>
                            <br><br><br><br><br>
                            <p>( <span id="d-cs"></span> )</p>
                        </div>
                    </div>
                </div>
                <button class="btn btn-sm btn-success mt-3" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#pengirim').select2();
        $('#penerima').select2();
        $('#kapal').select2();
        $('#cs').keyup(function (e) {
            $('#d-cs').html($(this).val());
        });
        $('#barang').keyup(function (e) {
            $('#d-barang').html($(this).val());
        });
        $('#sail').keyup(function (e) {
            $('#d-sail').html($(this).val());
        });
        $('#kendaraan').keyup(function (e) {
            $('#d-kendaraan').html($(this).val());
        });
        $('#kapal').keyup(function (e) {
            $('#d-kapal').html($(this).val());
        });
        $('#tujuan').keyup(function (e) {
            $('#d-tujuan').html($(this).val());
        });
        $('#etd').keyup(function (e) {
            $('#d-etd').html($(this).val());
        });

        $('#pengirim').change(function (e) {
            var val = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('api.customer.getOne') }}",
                data: {customer_id:val},
                success: function (response) {
                    var data = response;
                    $('#pengirim-nama').html(data.nama);
                    $('#pengirim-phone').html(data.telp);
                    $('#pengirim-alamat').html(data.alamat);
                    $('#pengirim-kota').html(data.kota);
                }
            });
        });

        $('#penerima').change(function (e) {
            var val = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('api.customer.getOne') }}",
                data: {customer_id:val},
                success: function (response) {
                    var data = response;
                    $('#penerima-nama').html(data.nama);
                    $('#penerima-kota').html(" "+data.kota);
                }
            });
        });
        $('#kapal').change(function (e) {
            var val = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('api.jadwal-kapal.getOne') }}",
                data: {id:val},
                success: function (response) {
                    var data = response;
                    $('#d-kapal').html(data.kapal);
                    $('#d-etd').html(data.etd);
                }
            });
        });
    </script>
@endsection

