@foreach ($data as $item)
@php
    if ($tipe=='D') {
        if ($item->debit>0) {
            $saldo_awal += $item->debit;
        } else {
            $saldo_awal -= $item->credit;
        }
    } else {
        if ($item->debit>0) {
            $saldo_awal -= $item->debit;
        } else {
            $saldo_awal += $item->credit;
        }
    }
@endphp
<tr>
    <td>{{ date('d/m/y', strtotime($item->created_at)) }}</td>
    <td>{{ $item->nomor }}</td>
    <td>{{ $item->coa->kode }}</td>
    <td>{{ $item->coa->nama }}</td>
    @if ($coa->is_cont)
    <td>{{ $item->container ?? '-' }}</td>
    @endif
    @if ($coa->is_nopol)
    <td>{{ $item->nopol ?? '-' }}</td>
    @endif
    @if ($coa->is_nojob)
    <td>{{ $item->order ? $item->order->job.'-'.sprintf('%02d',$item->order->no_job) : '-' }}</td>
    @endif
    @if ($coa->is_invoice)
    <td>{{ $item->invoice ?? '-' }}</td>
    @endif
    @if ($coa->is_invoice_trucking)
    <td>{{ $item->invoice ?? '-' }}</td>
    @endif
    <td>{{ $item->nama }}</td>
    <td>{{ number_format($item->debit,2,',','.') }}</td>
    <td>{{ number_format($item->credit,2,',','.') }}</td>
    @if ($coa->is_nobg)
    <td>-</td>
    @endif
    @if ($coa->is_nobupot)
    <td>{{ $item->order ? ($item->order->transaksi ? $item->order->transaksi->no_bupot :'') : '-' }}</td>
    @endif
    @if ($coa->is_tglbupot)
    <td>{{ $item->order ? ($item->order->transaksi ? $item->order->transaksi->tgl_bupot :'') : '-' }}</td>
    @endif
    <td>{{ number_format($saldo_awal,2,',','.') }}</td>
</tr>

@endforeach
