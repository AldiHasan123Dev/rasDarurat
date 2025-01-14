@forelse ($data as $i => $temp)
<tr>
    <td>
        <button class="btn btn-sm btn-danger p-0 text-danger" style="background: transparent" type="button" onclick="deleteData({{ $temp->id }})"><i class="fas fa-trash"></i></button>
    </td>
    <td>
        {{ $temp->coa->kode }} - {{ $temp->coa->nama }}
    </td>
    <td>
        @if ($temp->order)
        {{ $temp->order->job }}-{{ sprintf('%02d',$temp->order->no_job) }}
        @else
        -
        @endif
    </td>
    <td>{{ $temp->invoice ?? '-' }}</td>
    <td>{{ $temp->invoice_agen ?? '-' }}</td>
    <td>{{ $temp->invoice_vendor ?? '-' }}</td>
    <td>{{ $temp->invoice_external ?? '-' }}</td>
    <td>{{ $temp->no_bg ?? '-' }}</td>
    <td>{{ $temp->container ?? '-' }}</td>
    <td>{{ $temp->nopol ?? '-' }}</td>
    <td>{{ $temp->nama ?? '-' }}</td>
    <td class="text-right">{{ $temp->debit !== null ? number_format($temp->debit,2,',','.') : '-' }}</td>
    <td  class="text-right">{{ $temp->credit !== null ? number_format($temp->credit,2,',','.') : '-' }}</td>    
</tr>
@if ($loop->last)
<tr>
    <td class="py-3" colspan="11" style="width: 300px"><b>TOTAL</b></td>
    <td class="py-3 text-right"><b id="total_debit">{{ number_format($data->sum('debit'),2,',','.') }}</b></td>
    <td class="py-3 text-right"><b id="total_credit">{{ number_format($data->sum('credit'),2,',','.') }}</b></td>
</tr>
@endif
@empty
<tr>
    <td colspan="13" class="text-center">Tidak Ada Data!</td>
</tr>
@endforelse
