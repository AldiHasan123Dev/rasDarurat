@extends('layouts.admin')
@section('style')
    <style>
        .select2.select2-container.select2-container--default{
            width: 100% !important;
        }
        @media print {
            @import url('https://fonts.cdnfonts.com/css/dot-matrix');
            @page {
                size: 210mm 297mm;
                margin: 0cm .5cm 0cm .5cm;
            }
            body * {
                visibility: hidden;
                font-family: 'Dot Matrix', sans-serif;
                color: #000;
            }
            #print, #print * {
                visibility: visible;
                font-size: 1rem !important;
            }
            #print {
                width: 210mm;
                position: absolute;
                left: 0;
                top: -140px !important;
            }
            .table tr td{
                border: #000 1px solid;
                padding: 0px 3px !important;
                vertical-align: middle;
            }
        }

        #print {
            color: #000;
            }
        .table tr td{
            border: #000 1px solid;
            padding: 0;
            vertical-align: middle;
        }
        .table tr th{
            border: #000 1px solid;
        }
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            border: none;
            text-indent: 1px;
            text-overflow: '';
            font-size: .7rem;
            padding: 5px 10px;
            background: none;
        }
        p{
            font-size: .9rem;
        }
    </style>
@endsection
@section('content')
@php
        function terbilang($angka) {
        $angka = (float)$angka;
        $bilangan = array(
                '',
                'satu',
                'dua',
                'tiga',
                'empat',
                'lima',
                'enam',
                'tujuh',
                'delapan',
                'sembilan',
                'sepuluh',
                'sebelas'
            );
            if ($angka < 12) {
                return $bilangan[$angka];
            } else if ($angka < 20) {
                return $bilangan[$angka - 10] . ' belas';
            } else if ($angka < 100) {
                $hasil_bagi = (int)($angka / 10);
                $hasil_mod = $angka % 10;
                return trim(sprintf('%s puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
            } else if ($angka < 200) {
                return 'seratus ' . terbilang($angka - 100);
            } else if ($angka < 1000) {
                $hasil_bagi = (int)($angka / 100);
                $hasil_mod = $angka % 100;
                return trim(sprintf('%s ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
            } else if ($angka < 2000) {
                return 'seribu ' . terbilang($angka - 1000);
            } else if ($angka < 1000000) {
                $hasil_bagi = (int)($angka / 1000);
                $hasil_mod = $angka % 1000;
                return trim(sprintf('%s ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
            } else if ($angka < 1000000000) {
                $hasil_bagi = (int)($angka / 1000000);
                $hasil_mod = $angka % 1000000;
                return trim(sprintf('%s juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
            } else if ($angka < 1000000000000) {
                $hasil_bagi = (int)($angka / 1000000000);
                $hasil_mod = fmod($angka, 1000000000);
                return trim(sprintf('%s miliar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
            } else {
                return 'Angka terlalu besar';
            }
        }
@endphp
    <div class="container">
        <div class="card p-3">
            <div class="d-flex justify-content-between" style="gap:5px">
                @if ($orders->count()>0)
                <div class="d-flex" style="gap: 10px">
                    <div class="d-flex gap-3">
                        <button onclick="printSubmit()" class="btn btn-sm btn-success ml-2 mb-3">Submit Jasa Kirim & Print Surat</button>
                    </div>
                </div>
                @endif
                <button data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-sm btn-info">Cari JOB</button>
            </div>
        </div>
        <div class="card p-3 mt-3">
            <div id="print" style="width: 100%">
                <x-header-cop></x-header-cop>
                <hr>
                <div class="row">
                    @if ($orders->count()>0)
                    <div class="col-12">
                        <table style="font-size: .9rem" class="w-100">
                            <tr>
                                <td >No. {{ $no_dooring }}</td>
                            </tr>
                            <tr>
                                <td>Surabaya, {{ date('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Kepada</td>
                            </tr>
                        </table>
                        <table style="font-size: .9rem" class="w-100 mt-3">
                            <tr>
                                <td>{{ $order->agent->nama }}</td>
                            </tr>
                            <tr>
                                <td>{{ $order->agent->pic }}</td>
                            </tr>
                            <tr>
                                <td>{{ $order->agent->alamat }}</td>
                            </tr>
                            <tr>
                                <td>{{ $order->agent->kota }}</td>
                            </tr>
                        </table>
                        <p>Dengan Hormat</p>
                        <p>Bersama ini kami sampaikan bahwa {{ $order->jadwal_kapal->kapal->nama }} VOY.{{ $order->jadwal_kapal->voyage }} TD {{ date('d F Y', strtotime($order->jadwal_kapal->td)) }} <br> Termuat {{ $orders->count() }} ({{ terbilang($orders->count()) }}) cont dengan <b><u>PELAYARAN "{{ $order->jadwal_kapal->pelayaran->nama }}" berikut data :</u></b></p>
                        <table class="w-100 mt-3 border border-dark my-2" style="font-size: .7rem">
                            <tr>
                                <td class="border border-dark text-center fw-bold">No</td>
                                <td class="border border-dark text-center fw-bold">JOB</td>
                                <td class="border border-dark text-center fw-bold">No. Cont/Seal</td>
                                <td class="border border-dark text-center fw-bold">Penerima</td>
                                <td class="border border-dark text-center fw-bold">Jmlh</td>
                                <td class="border border-dark text-center fw-bold">Nama Barang</td>
                            </tr>
                            @foreach ($orders as $item)
                            <tr>
                                <td class="border border-dark text-center fw-bold">{{ $loop->iteration }}</td>
                                <td class="border border-dark text-center fw-bold">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }}</td>
                                <td class="border border-dark text-center fw-bold">{{ $item->container }}/{{ $item->seal }}</td>
                                <td class="border border-dark text-center fw-bold">{{ $item->penerima->nama }}</td>
                                <td class="border border-dark text-center fw-bold">{{ $item->bttb->sum('qty') }}</td>
                                <td class="border border-dark text-center fw-bold">{{ $item->barang->nama }}</td>
                            </tr>
                            @endforeach
                        </table>
                        <p>Container tersebut diatasi kondisi <b>DOOR</b> sampai ke alamat penerima. Konosemen untuk pengeluaran container akan kami fax setelah pihak pelayaran menerbitkan.</p>
                        <p>Demikian penyampaian dari kami, atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
                        <div style="position: relative; right:200px; font-size: .9rem">
                            <div class="text-center">
                                <p>Hormat Kami,</p>
                                <div style="height: 100px"></div>
                                <p> ({{ Auth::user()->name }})</p>
                            </div>
                        </div>
                        <b>REMINDER :</b>
                        <ol style="font-size: .8rem">
                            <li>Barang wajib di antar / di dooring kan ke penerima maksimal 3 hari setelah kapal bongkar</li>
                            <li>Kami tidak bertanggung jawab jika terjadi klaim, jika di dooring kan lebih dari 3 hari</li>
                            <li>Bila pihak penerima belum bisa menerima barang, segera konfirmasi ke kami dan minta surat keterangan, dari pihak penerima</li>
                        </ol>
                        <b>NOTE :</b>
                        <ul style="font-size: .8rem">
                            <li>BTTB putih & merah Kembali ke RAS SBY</li>
                            <li>BTTB kuning untuk penerima</li>
                        </ul>
                    </div>
                    @else
                    <div class="alert alert-danger text-center">
                        <strong>JOB tidak ditemukan</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('cetak.dooring') }}" method="GET" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Dooring</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="jadwal_kapal_id-si">Kapal</label><br>
                        <select name="jadwal_kapal_id" id="jadwal_kapal_id-si" class="form-control w-100">
                            @foreach ($jadwal_kapals as $kapal)
                                <option value="{{ $kapal->id }}">{{ $kapal->kapal->nama ?? '-' }} || Voy.{{ $kapal->voyage }} || {{ $kapal->pelayaran->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="tujuan-si">Tujuan</label><br>
                        <select name="tujuan" id="tujuan-si" class="form-control w-100">
                            @foreach ($data_lokasi as $lokasi)
                                <option value="{{ $lokasi->id }}">{{ $lokasi->nama ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="agent">Agent</label><br>
                        <select name="agent" id="agent" class="form-control w-100">
                            <option value=""></option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->nama ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Buat Surat</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('#attn').keyup(function (e) {
            var val = $(this).val();
            $('#d-attn').html(val);
            $('#attn-input').val(val);
        });
        $("#jadwal_kapal_id-si").select2({
            dropdownParent: $('#exampleModal'),
        });
        $("#tujuan-si").select2({
            dropdownParent: $('#exampleModal'),
        });
        $("#agent").select2({
            dropdownParent: $('#exampleModal'),
        });

        function printSubmit(){
            if(confirm('Apa anda yakin ? proses ini sekaligus akan membuat jasa pengiriman surat')){
                $.ajax({
                    type: "POST",
                    url: "{{ route('api-jasakirim.store') }}",
                    data: {no_dooring:@json($no_dooring),lokasi_id:@json(request('tujuan')),agen_id:@json(request('agent')),order_id:@json($order_id),jadwal_kapal_id:@json(request('jadwal_kapal_id'))},
                    success: function (response) {
                        let url = @json($url);
                        let params_ = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=800,height=500,left=100,top=100`;
                        open(url, 'Cetak Doring', params_);
                    }
                });
            }
        }

        $(document).on('keydown', function(e) {
            if((e.ctrlKey || e.metaKey) && (e.key == "p" || e.charCode == 16 || e.charCode == 112 || e.keyCode == 80) ){
                alert("Harap klik tombol cetak");
                e.cancelBubble = true;
                e.preventDefault();

                e.stopImmediatePropagation();
            }
        });
    </script>
@endsection
