@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <form action="{{ route('hutang-agen.store') }}" method="POST" class="card">
            @csrf
            <div class="card-header p-3 d-flex justify-content-between" style="gap:10px">
                <h5>Draf Hutang Agen | {{ $orders->first()->agent->nama }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem; white-space: nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>JOB</th>
                                <th>Pembayar</th>
                                <th>Penerima</th>
                                <th>Tipe</th>
                                <th>Tarif</th>
                                <th>Container / Seal</th>
                                <th>Dari</th>
                                <th>Tujuan</th>
                                <th>Tarif Agen</th>
                                <th>Invoice</th>
                                <th>Tgl Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <input type="hidden" name="order_id[]" value="{{ $order->id }}">
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $order->job }}-{{ sprintf('%02d',$order->no_job) }}</td>
                                    <td>{{ $order->tarif->customer->nama }}</td>
                                    <td>{{ $order->penerima->nama }}</td>
                                    <td>{{ $order->tarif->shipmentInfo->nama }}</td>
                                    <td>{{ number_format($order->tarif->tarif) }}</td>
                                    <td>{{ $order->container }} / {{ $order->seal }}</td>
                                    <td>{{ $order->tarif->dari_lokasi->nama }}</td>
                                    <td>{{ $order->tarif->tujuan_lokasi->nama }}</td>
                                    <td>
                                        <select name="tarif[]" style="width:200px" class="form-select form-select-sm" onchange="hitung({{ $loop->iteration }})" id="tarif-{{ $loop->iteration }}" required>
                                            <option value="0" selected>Rp. 0</option>
                                            @foreach ($tarif->where('pembayar_id',$order->tarif->customer_id) as $item)
                                                <option value="{{ $item->tarif }}"> {{ $item->pembayar->nama }} / {{ $item->penerima->nama }} / {{ number_format($item->tarif) }} / {{ $item->shipment->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="invoice[]" style="width: 200px" placeholder="Invoice" required autocomplete>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control form-control-sm" name="tanggal[]" style="width: 200px" required >
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="10" class="table-secondary"></td>
                                    <td>PPN (1.1%)</td>
                                    <td id="ppn-label-{{ $loop->iteration }}"><input type="number" name="ppn[]" class="form-control form-control-sm" id="ppn-{{ $loop->iteration }}" style="width: 200px" required></td>
                                </tr>
                                <tr>
                                    <td colspan="10" class="table-secondary"></td>
                                    <td>Pot. PPH 23 (2%)</td>
                                    <td id="pph-label-{{ $loop->iteration }}"><input type="number" name="pph[]" class="form-control form-control-sm" id="pph-{{ $loop->iteration }}" style="width: 200px" required></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="11" class="font-bold text-center"><b>TOTAL</b></td>
                                <td><b>Rp. <span id="total">0</span></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <br>
                <div class="d-flex justify-content-between">
                    <h5>Add Cost</h5>
                    <button class="btn btn-sm btn-primary" type="button" onclick="addBaris()">Tambah Baris</button>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem; white-space: nowrap">
                        <thead>
                            <tr>
                                <th>JOB ORDER</th>
                                <th>Keterangan</th>
                                <th>Nominal</th>
                                <th>Beban ditanggung</th>
                            </tr>
                        </thead>
                        <tbody id="tagihan-list">
                            <tr>
                                <td>
                                    <select name="tagihan_order_id[]" class="form-select form-select-sm">
                                        <option value=""></option>
                                        @foreach ($jobs as $job => $item)
                                        <option value="job-{{ $job }}">GROUP JOB {{ $job }}</option>
                                        @endforeach
                                        @foreach ($orders as $item)
                                            <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->container }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="nama[]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="number" name="jumlah[]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <select name="beban[]" class="form-select form-select-sm">
                                        <option value="customer" selected>Customer</option>
                                        <option value="ras">RAS</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select name="tagihan_order_id[]" class="form-select form-select-sm">
                                        <option value=""></option>
                                        @foreach ($jobs as $job => $item)
                                        <option value="job-{{ $job }}">GROUP JOB {{ $job }}</option>
                                        @endforeach
                                        @foreach ($orders as $item)
                                            <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->container }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="nama[]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="number" name="jumlah[]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <select name="beban[]" class="form-select form-select-sm">
                                        <option value="customer" selected>Customer</option>
                                        <option value="ras">RAS</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 row">
                    {{-- <div class="col-4">
                        <label for="tanggal" class="text-label">Tanggal Invoice</label>
                        <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" required>
                    </div> --}}
                    <div class="col">
                        <button class="py-2 px-3 btn btn-success mt-4" type="submit" onclick="return confirm('Are you sure?')">Buat Voucher</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function addBaris(){
            var html = `<tr>
                                <td>
                                    <select name="tagihan_order_id[]" class="form-select form-select-sm">
                                        <option value=""></option>
                                        @foreach ($jobs as $job => $item)
                                            <option value="job-{{ $job }}">GROUP JOB {{ $job }}</option>
                                        @endforeach
                                        @foreach ($orders as $item)
                                            <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->container }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="nama[]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="number" name="jumlah[]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <select name="beban[]" class="form-select form-select-sm">
                                        <option value="customer" selected>Customer</option>
                                        <option value="ras">RAS</option>
                                    </select>
                                </td>
                            </tr>`;
            $('#tagihan-list').append(html);
        }

        var count = parseInt(@json($count));
        function hitung(id){
            var total = 0;
            for (let i = 1; i <= count; i++) {
                var tarif = parseInt($('#tarif-'+i).val());
                var ppn = Math.round(tarif * 0.011);
                var pph = Math.round(tarif * 0.02);
                var jumlah = tarif + ppn - pph;
                total += jumlah
                $('#ppn-'+i).val(ppn);
                $('#pph-'+i).val(pph);
                // $('#ppn-label-'+i).html('Rp. '+ppn.toLocaleString('id-ID'));
                // $('#pph-label-'+i).html('- Rp. '+pph.toLocaleString('id-ID'));
            }
            $('#total').html(total.toLocaleString('id-ID'));
        }
        // $('table').dataTable()
    </script>
@endsection
