@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="bg-white p-1">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                            Draf Resi Pengiriman
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm" id="table-2" style="font-size:.7rem">
                                        <thead>
                                            <tr>
                                                <th>ID.</th>
                                                <th>Tujuan</th>
                                                <th>Kota</th>
                                                <th>JOB/ITEM</th>
                                                <th>Barcode</th>
                                                <th>Tgl Kirim</th>
                                                <th>Tgl Terima</th>
                                                <th>Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $rs)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $rs->lokasi->nama }}</td>
                                                    <td>{{ $rs->agen->lokasi->nama }}</td>
                                                    <td>{{ $rs->order_name() }}</td>
                                                    <td>{{ $rs->barcode }}</td>
                                                    <td>{{ date('d/m/y', strtotime($rs->tgl_kirim)) }}</td>
                                                    <td>{{ date('d/m/y', strtotime($rs->tgl_terima)) }}</td>
                                                    <td>{{ number_format($rs->nominal,2,',','.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('jasakirim.generate.jurnal') }}" method="post" class="container">
                        <div class="card p-3 shadow">
                                @php
                                    $no_1 = App\Models\Jurnal::where('tipe','JNL')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
                                    $no1 = sprintf('%02d',date('m')).'-'.sprintf('%03d',$no_1).'/'.date('y');
                                    $no_2 = App\Models\Jurnal::where('tipe','BBK')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
                                    $no2 = sprintf('%03d',$no_2) . '/' . 'BBK' . '-RAS/' . date('y');
                                    $no_3 = App\Models\Jurnal::where('tipe','BKK')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->max('no') + 1;
                                    $no3 = sprintf('%03d',$no_3) . '/' . 'BKK' . '-RAS/' . date('y');
                                @endphp
                                <div>
                                    @csrf
                                    <input type="hidden" name="no" id="no" value="{{ old('no', '') }}">
                                    <input type="hidden" name="invoice" value="{{ request('invoice') }}">
                                    <div class="d-flex gap-3">
                                        <div class="mb-2">
                                            <label for="nomor">Nomor Jurnal</label>
                                            <select name="nomor" id="nomor" class="form-select" required>
                                                <option value="" selected>Pilih Nomor Jurnal</option>
                                                <option value="{{ $no1 }}">{{ $no1 }}</option>
                                                <option value="{{ $no2 }}">{{ $no2 }}</option>
                                                <option value="{{ $no3 }}">{{ $no3 }}</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="created_at">Tanggal Jurnal</label>
                                            <input type="date" name="created_at" id="created_at" required class="form-control" value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="mb-2 mt-3">
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('are you sure?')">Generate Jurnal</button>
                                            <a href="{{ route('jasakirim.index',['role'=>'jurnal']) }}" class="btn btn-primary btn-sm">Kembali</a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="card p-3 mt-3">
                            <div id="print">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="border border-primary nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Preview Jurnal</button>
                                    </li>
                                </ul>
                                <div class="tab-content border border-primary" id="myTabContent">
                                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        <div class="p-4">
                                            <div class="table-responsive" style="height: 500px">
                                                <table class="table table-sm nowrap" style="font-size: .7rem; white-space:nowrap;">
                                                    <thead>
                                                        <tr>
                                                            {{-- <th>#</th> --}}
                                                            <th>COA</th>
                                                            <th>Akun</th>
                                                            <th>JOB</th>
                                                            <th>Keterangan</th>
                                                            <th>Debit</th>
                                                            <th>Kredit</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data as $idx => $item)
                                                            @foreach ($item->orders as $order)
                                                                <tr>
                                                                    <td>{{ $order->checkOmset() ? '2.1.5.1' : '1.6.1' }}</td>
                                                                    <td>{{ $order->checkOmset() ? 'Hutang Biaya Operasional Ekspedisi' : 'Uang Muka Biaya Oprasional Ekspedisi' }}</td>
                                                                    <td>{{ $order->job }}-{{ sprintf('%02d',$order->no_job) }}</td>
                                                                    <td>Biaya Pengiriman Dokumen {{ $order->agent->nama ?? '-' }} ({{ $order->agent->lokasi->nama ?? '-' }})</td>
                                                                    <td>{{ number_format($item->split_nominal()) }}</td>
                                                                    <td>0</td>
                                                                </tr>
                                                            @endforeach
                                                            @foreach ($item->kirim_dokumen as $kirim)
                                                                <tr>
                                                                    <td>{{ $kirim->order->checkOmset() ? '2.1.5.1' : '1.6.1' }}</td>
                                                                    <td>{{ $kirim->order->checkOmset() ? 'Hutang Biaya Operasional Ekspedisi' : 'Uang Muka Biaya Oprasional Ekspedisi' }}</td>
                                                                    <td>{{ $kirim->order->job }}-{{ sprintf('%02d',$kirim->order->no_job) }}</td>
                                                                    <td>{{ $kirim->nama }}</td>
                                                                    <td>{{ number_format($item->split_nominal()) }}</td>
                                                                    <td>0</td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                        <tr>
                                                            <td>2.1.1.2</td>
                                                            <td>Hutang Agent & Asuransi</td>
                                                            <td>-</td>
                                                            <td>Hutang Agen - {{ $item->invoice }}</td>
                                                            <td>0</td>
                                                            <td>{{ number_format($data->sum('nominal')) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-3">
                                                <div class="d-flex gap-5">
                                                    <div class="text-bold">DEBIT : <span class="text-total">{{ number_format($data->sum('nominal')) }}</span></div>
                                                    <div class="text-bold">CREDIT : <span class="text-total">{{ number_format($data->sum('nominal')) }}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   $(document).ready(function () {
        // Pastikan untuk menangani event 'change' pada dropdown #nomor
        $('#nomor').on('change', function () {
            var selectedNomor = $(this).val();  // Ambil nilai yang dipilih
            console.log('Selected Nomor:', selectedNomor);  // Untuk debugging
            $('#no').val(selectedNomor);  // Update nilai hidden input 'no' sesuai pilihan
        });

        // Untuk memastikan nilai input hidden 'no' tetap sesuai ketika halaman dimuat
        var selectedValue = $('#nomor').val();
        if (selectedValue) {
            $('#no').val(selectedValue);
        }
    });
</script>

@endsection
