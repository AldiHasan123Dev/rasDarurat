@extends('layouts.admin')
@section('content')
    <form action="{{ route('jurnal.submit_slip_totalan_sopir') }}" method="post" class="container">
        <div class="card p-3 shadow">
                @php
                    $no_1 = App\Models\Jurnal::where('tipe','BBK')->max('no') + 1;
                    $no1 = sprintf('%03d',$no_1).'/BBK-RAS/'.date('y');
                    $no_2 = App\Models\Jurnal::where('tipe','BKK')->max('no') + 1;
                    $no2 = sprintf('%03d',$no_2).'/BKK-RAS/'.date('y');
                @endphp
                <div>
                    @csrf
                    <div class="d-flex gap-3">
                        <div class="mb-2">
                            <label for="nomor">Nomor Jurnal</label>
                            <select name="nomor" id="nomor" class="form-select" required>
                                <option data-tipe="" data-no="0" data-coa="-" data-akun="-" value="">-</option>
                                <option data-tipe="BBK" data-no="{{ $no_1 }}" data-coa="1.1.2.1" data-akun="Bank Mandiri 1400046005006" value="{{ $no1 }}">{{ $no1 }}</option>
                                <option data-tipe="BKK" data-no="{{ $no_2 }}" data-coa="1.1.1" data-akun="Kas" value="{{ $no2 }}">{{ $no2 }}</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="created_at">Tanggal Jurnal</label>
                            <input type="date" name="created_at" id="created_at" required class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-2 mt-3">
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('are you sure?')">Generate Jurnal</button>
                            <a href="{{ route('jurnal.totalan_sopir') }}" class="btn btn-primary btn-sm">Kembali</a>
                        </div>
                    </div>
                </div>
        </div>
        <div class="card p-3 mt-3">
            <div id="print">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="border border-primary nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Detail Informasi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="border border-primary nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Preview Jurnal</button>
                    </li>
                </ul>
                <div class="tab-content border border-primary" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="invoice-box first-page p-4">
                            <div class="table-responsive" style="height:500px;">
                                <table class="mt-2 w-100 table table-sm nowrap" style="font-size: .7rem; white-space:nowrap;">
                                    <thead>
                                        <tr class="heading">
                                            {{-- <td>No</td> --}}
                                            <td>Sopir</td>
                                            <td>TGL Muat</td>
                                            <td>Tipe</td>
                                            <td>No JOB</td>
                                            <td>No Container</td>
                                            <td>Nopol</td>
                                            <td>Customer</td>
                                            <td>Pembayar</td>
                                            <td>Tujuan</td>
                                            <td>Borongan Sopir</td>
                                            <td>Sangu Sopir</td>
                                            <td>Simpanan Sopir</td>
                                            <td>Borongan Kuli</td>
                                            <td>Sangu Kuli</td>
                                            <td>Simpanan Kuli</td>
                                            <td>TB/TL</td>
                                            <td>Stappel</td>
                                            <td>Lain-lain</td>
                                            <td>Sub Total</td>
                                        </tr>
                                    </thead>
                                    @foreach ($orders->groupBy('sopir_id') as $sopir)
                                        @foreach ($sopir as $item)
                                            <tr>
                                                @if ($loop->first)
                                                <td rowspan="{{ $sopir->count() }}">{{ $item->sopir->nama }}</td>
                                                @endif
                                                <td class="text-center">{{ date('d/m/y', strtotime($item->tgl_muat)) }}</td>
                                                <td>{{ $item->tipe }}'</td>
                                                @if ($item->order)
                                                <td>{{ $item->order->job }}-{{ sprintf('%02d',$item->order->no_job) }}</td>
                                                @else
                                                <td>-</td>
                                                @endif
                                                <td>{{ $item->container }} / {{ $item->seal }}</td>
                                                <td>{{ $item->kendaraan->nopol }}</td>
                                                <td>{{ $item->customer->nama }}</td>
                                                <td>{{ $item->order ? ($item->order->tarif->customer->nama??'-') : '-' }}</td>
                                                <td>{{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                                <td class="text-center">{{ number_format($item->borongan) }}</td>
                                                <td class="text-center">{{ number_format($item->sangu) }}</td>
                                                <td class="text-center">{{ number_format($item->simpanan) }}</td>
                                                <td class="text-center">{{ number_format($item->borongan_kuli) }}</td>
                                                <td class="text-center">{{ number_format($item->kuli) }}</td>
                                                <td class="text-center">{{ number_format($item->simpanan_kuli) }}</td>
                                                <td class="text-center">{{ number_format($item->tb_tl) }}</td>
                                                <td class="text-center">{{ number_format($item->stappel) }}</td>
                                                <td class="text-center">{{ number_format($item->lain_lain) }}</td>
                                                <td>
                                                    <div class="price d-flex justify-content-between px-2">
                                                        <span>Rp</span>
                                                        <span>{{ number_format($item->total_sopir) }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    <tr style="height: 20px !important">
                                        <td colspan="19" style="border-bottom: 1px solid black"></td>
                                    </tr>
                                    <tr class="border-bottom border-dark">
                                        <td colspan="16"></td>
                                        <td class="fw-bold text-center" colspan="2">TOTAL</td>
                                        <td class="fw-bold">
                                            <div class="price d-flex justify-content-between px-2">
                                                <span>Rp</span>
                                                <span>{{ number_format($orders->sum('total_sopir')) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="p-4">
                            <div class="table-responsive" style="height: 500px">
                                <table class="table table-sm nowrap" style="font-size: .7rem; white-space:nowrap;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>COA</th>
                                            <th>Akun</th>
                                            <th>Container</th>
                                            <th>Nopol</th>
                                            <th>Keterangan</th>
                                            <th>Debit</th>
                                            <th>Kredit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $inp = 0;
                                        @endphp
                                        @foreach ($orders as $idx => $item)
                                            @if ($item->simpanan > 0)
                                                @php
                                                    $inp++;
                                                @endphp
                                                <tr>
                                                    <td rowspan="2"><input type="checkbox" name="active_{{ $inp }}" id="active_{{ $inp }}" onchange="activeInp({{ $inp }})" value="1"></td>
                                                    <td>{{ $item->customer_id == 2 ? '1.6.2.2' : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?'6.2.1':'2.1.5.2.1') }}</td>
                                                    <td>{{ $item->customer_id == 2 ? 'Uang Muka Biaya Operasional Trucking Ekspedisi' : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?'Biaya Operasional Trucking Eksternal':'Hutang Biaya Oprasional Trucking') }}</td>
                                                    <td>{{ $item->container }}</td>
                                                    <td>{{ $item->kendaraan->nopol }}</td>
                                                    <td>Simpanan Sangu Sopir - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                                    <td>{{ number_format($item->simpanan) }}</td>
                                                    <td>0</td>
                                                </tr>
                                                <tr>
                                                    <td class="coa">-</td>
                                                    <td class="akun">-</td>
                                                    <td>{{ $item->container }}</td>
                                                    <td>{{ $item->kendaraan->nopol }}</td>
                                                    <td>Simpanan Sangu Sopir - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                                    <td>0</td>
                                                    <td>{{ number_format($item->simpanan) }}</td>
                                                </tr>
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][coa_id]" value="{{ $item->customer_id == 2 ? 61 : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?98:80) }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][order_trucking_id]" value="{{ $item->id }}">
                                                <input disabled="disabled" class="inp-{{ $inp }} jurnal_nomor" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][nomor]">
                                                <input disabled="disabled" class="inp-{{ $inp }} no" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][no]">
                                                <input disabled="disabled" class="inp-{{ $inp }} tipe" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][tipe]">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][invoice]" value="{{ $item->invoice }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][nopol]" value="{{ $item->kendaraan->nopol ?? '' }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][container]" value="{{ $item->container }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" id="debit-{{ $inp }}" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][debit]" value="{{ $item->simpanan }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_sopir[{{ $idx }}][nama]" value="Simpanan Sangu Sopir - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}">
                                            @endif
                                            @if ($item->simpanan_kuli > 0)
                                                @php
                                                    $inp++;
                                                @endphp
                                                <tr>
                                                    <td rowspan="2"><input type="checkbox" name="active_{{ $inp }}" id="active_{{ $inp }}" onchange="activeInp({{ $inp }})" value="1"></td>
                                                    <td>{{ $item->customer_id == 2 ? '1.6.2.2' : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?'6.2.1':'2.1.5.2.1') }}</td>
                                                    <td>{{ $item->customer_id == 2 ? 'Uang Muka Biaya Operasional Trucking Ekspedisi' : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?'Biaya Operasional Trucking Eksternal':'Hutang Biaya Oprasional Trucking') }}</td>
                                                    <td>{{ $item->container }}</td>
                                                    <td>{{ $item->kendaraan->nopol }}</td>
                                                    <td>Biaya Kuli - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                                    <td>{{ number_format($item->simpanan_kuli) }}</td>
                                                    <td>0</td>
                                                </tr>
                                                <tr>
                                                    <td class="coa">-</td>
                                                    <td class="akun">-</td>
                                                    <td>{{ $item->container }}</td>
                                                    <td>{{ $item->kendaraan->nopol }}</td>
                                                    <td>Biaya Kuli - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                                    <td>0</td>
                                                    <td>{{ number_format($item->simpanan_kuli) }}</td>
                                                </tr>
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][coa_id]" value="{{ $item->customer_id == 2 ? 61 : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?98:80) }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][order_trucking_id]" value="{{ $item->id }}">
                                                <input disabled="disabled" class="inp-{{ $inp }} jurnal_nomor" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][nomor]">
                                                <input disabled="disabled" class="inp-{{ $inp }} no" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][no]">
                                                <input disabled="disabled" class="inp-{{ $inp }} tipe" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][tipe]">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][invoice]" value="{{ $item->invoice }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][nopol]" value="{{ $item->kendaraan->nopol ?? '' }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][container]" value="{{ $item->container }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" id="debit-{{ $inp }}" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][debit]" value="{{ $item->simpanan_kuli }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_simpanan_kuli[{{ $idx }}][nama]" value="Biaya Kuli - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}">
                                            @endif
                                            @if ($item->tb_tl > 0)
                                                @php
                                                    $inp++;
                                                @endphp
                                                <tr>
                                                    <td rowspan="2"><input type="checkbox" name="active_{{ $inp }}" id="active_{{ $inp }}" onchange="activeInp({{ $inp }})" value="1"></td>
                                                    <td>{{ $item->customer_id == 2 ? '1.6.2.2' : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?'6.2.1':'2.1.5.2.1') }}</td>
                                                    <td>{{ $item->customer_id == 2 ? 'Uang Muka Biaya Operasional Trucking Ekspedisi' : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?'Biaya Operasional Trucking Eksternal':'Hutang Biaya Oprasional Trucking') }}</td>
                                                    <td>{{ $item->container }}</td>
                                                    <td>{{ $item->kendaraan->nopol }}</td>
                                                    <td>TB/TL - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                                    <td>{{ number_format($item->tb_tl) }}</td>
                                                    <td>0</td>
                                                </tr>
                                                <tr>
                                                    <td class="coa">-</td>
                                                    <td class="akun">-</td>
                                                    <td>{{ $item->container }}</td>
                                                    <td>{{ $item->kendaraan->nopol }}</td>
                                                    <td>TB/TL - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                                    <td>0</td>
                                                    <td>{{ number_format($item->tb_tl) }}</td>
                                                </tr>
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_tbtl[{{ $idx }}][coa_id]" value="{{ $item->customer_id == 2 ? 61 : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?98:80) }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_tbtl[{{ $idx }}][order_trucking_id]" value="{{ $item->id }}">
                                                <input disabled="disabled" class="inp-{{ $inp }} jurnal_nomor" type="hidden" name="jurnal_tbtl[{{ $idx }}][nomor]">
                                                <input disabled="disabled" class="inp-{{ $inp }} no" type="hidden" name="jurnal_tbtl[{{ $idx }}][no]">
                                                <input disabled="disabled" class="inp-{{ $inp }} tipe" type="hidden" name="jurnal_tbtl[{{ $idx }}][tipe]">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_tbtl[{{ $idx }}][invoice]" value="{{ $item->invoice }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_tbtl[{{ $idx }}][nopol]" value="{{ $item->kendaraan->nopol ?? '' }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_tbtl[{{ $idx }}][container]" value="{{ $item->container }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" id="debit-{{ $inp }}" type="hidden" name="jurnal_tbtl[{{ $idx }}][debit]" value="{{ $item->tb_tl }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_tbtl[{{ $idx }}][nama]" value="TB/TL - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}">
                                            @endif
                                            @if ($item->stappel > 0)
                                                @php
                                                    $inp++;
                                                @endphp
                                                <tr>
                                                    <td rowspan="2"><input type="checkbox" name="active_{{ $inp }}" id="active_{{ $inp }}" onchange="activeInp({{ $inp }})" value="1"></td>
                                                    <td>{{ $item->customer_id == 2 ? '1.6.2.2' : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?'6.2.1':'2.1.5.2.1') }}</td>
                                                    <td>{{ $item->customer_id == 2 ? 'Uang Muka Biaya Operasional Trucking Ekspedisi' : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?'Biaya Operasional Trucking Eksternal':'Hutang Biaya Oprasional Trucking') }}</td>
                                                    <td>{{ $item->container }}</td>
                                                    <td>{{ $item->kendaraan->nopol }}</td>
                                                    <td>STAPPEL - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                                    <td>{{ number_format($item->stappel) }}</td>
                                                    <td>0</td>
                                                </tr>
                                                <tr>
                                                    <td class="coa">-</td>
                                                    <td class="akun">-</td>
                                                    <td>{{ $item->container }}</td>
                                                    <td>{{ $item->kendaraan->nopol }}</td>
                                                    <td>STAPPEL - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}</td>
                                                    <td>0</td>
                                                    <td>{{ number_format($item->stappel) }}</td>
                                                </tr>
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_stappel[{{ $idx }}][coa_id]" value="{{ $item->customer_id == 2 ? 61 : (date('m-y',strtotime($item->tgl_muat))==date('m-y')?98:80) }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_stappel[{{ $idx }}][order_trucking_id]" value="{{ $item->id }}">
                                                <input disabled="disabled" class="inp-{{ $inp }} jurnal_nomor" type="hidden" name="jurnal_stappel[{{ $idx }}][nomor]">
                                                <input disabled="disabled" class="inp-{{ $inp }} no" type="hidden" name="jurnal_stappel[{{ $idx }}][no]">
                                                <input disabled="disabled" class="inp-{{ $inp }} tipe" type="hidden" name="jurnal_stappel[{{ $idx }}][tipe]">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_stappel[{{ $idx }}][invoice]" value="{{ $item->invoice }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_stappel[{{ $idx }}][nopol]" value="{{ $item->kendaraan->nopol ?? '' }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_stappel[{{ $idx }}][container]" value="{{ $item->container }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" id="debit-{{ $inp }}" type="hidden" name="jurnal_stappel[{{ $idx }}][debit]" value="{{ $item->stappel }}">
                                                <input disabled="disabled" class="inp-{{ $inp }}" type="hidden" name="jurnal_stappel[{{ $idx }}][nama]" value="STAPPEL - {{ $item->customer->nama }} (1x{{ $item->tipe }}) {{ $item->tarif->tujuan->tujuanInfo->nama }}">
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex gap-5">
                                    <div class="text-bold">DEBIT : <span class="text-total">0</span></div>
                                    <div class="text-bold">CREDIT : <span class="text-total">0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
<script>
    $('#nomor').change(function (e) {
        var coa = $(this).find(':selected').attr('data-coa')
        var akun = $(this).find(':selected').attr('data-akun')
        var no = $(this).find(':selected').attr('data-no')
        var tipe = $(this).find(':selected').attr('data-tipe')
        var jurnal_nomor = $(this).val();
        $('.akun').html(akun);
        $('.coa').html(coa);
        $('.jurnal_nomor').val(jurnal_nomor);
        $('.no').val(no);
        $('.tipe').val(tipe);
    });

    let total = 0;
    function activeInp(id){
        if($('#active_' + id).is(":checked")){
            $('.inp-'+id).attr('disabled',false);
            total += parseInt($('#debit-'+id).val());
        }else{
            $('.inp-'+id).attr('disabled',true);
            total -= parseInt($('#debit-'+id).val());
        }
        $('.text-total').html(total.toLocaleString('id-ID'));
    }
</script>
@endsection
