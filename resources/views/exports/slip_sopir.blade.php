<table>
    <thead>
        <tr>
            <th>Totalan</th>
            <th>: {{ $order->sopir->nama }}</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>: {{ $order->tgl_total ? date('d/m/Y',strtotime($order->tgl_total)):'-' }}</th>
        </tr>
    </thead>
</table>
<table class="mt-2 w-100 table table-sm nowrap" style="font-size: .7rem; white-space:nowrap">
    <thead>
        <tr class="heading">
            {{-- <td>No</td> --}}
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
    @foreach ($orders as $item)
        <tr>
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
    <tr style="height: 20px !important">
        <td colspan="18" style="border-bottom: 1px solid black"></td>
    </tr>
    <tr class="border-bottom border-dark">
        <td colspan="15"></td>
        <td class="fw-bold text-center" colspan="2">TOTAL</td>
        <td class="fw-bold">
            <div class="price d-flex justify-content-between px-2">
                <span>Rp</span>
                <span>{{ number_format($orders->sum('total_sopir')) }}</span>
            </div>
        </td>
    </tr>
</table>
