<table>
    <thead>
        <tr>
            <th>To</th>
            <th>: {{ $to }}</th>
        </tr>
        <tr>
            <th>Attn</th>
            <th>: {{ $attn }}</th>
        </tr>
    </thead>
</table>
<table class="table nowrap" style="font-size: .7rem; border: 1px solid black">
    <thead>
        <tr>
            <th class="text-center" style="vertical-align: middle" colspan="2">Pembagian BL</th>
            <th class="text-center" style="vertical-align: middle" rowspan="2">Cont / Seal</th>
            <th class="text-center" style="vertical-align: middle" rowspan="2">Koli</th>
            <th class="text-center" style="vertical-align: middle" rowspan="2">Barang</th>
            <th class="text-center" style="vertical-align: middle" rowspan="2">Stuffing</th>
        </tr>
        <tr>
            <th class="text-center" style="width: 200px">Penerima</th>
            <th class="text-center" style="width: 200px">Pengirim</th>
        </tr>
    </thead>
    <tbody>
        @if ($orders->count()>0)
            @foreach ($orders->where('agen','AGEN')->groupBy('agen_id') as $data)
                @foreach ($data as $item)
                    <tr>
                        @if ($loop->first)
                        <td class="text-center" rowspan="{{ $data->count() }}">{{ $item->agent->nama ?? '-' }}</td>
                        <td class="text-center" rowspan="{{ $data->count() }}">
                            <select>
                                @foreach ($pengirim as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        @endif
                        <td style="padding-left: 10px !important"> <a href="{{ route('bttb.index',['order_id'=>$item->id]) }}"> {{ $item->container }} / {{ $item->seal }}</a></td>
                        <td class="text-center">{{ strtoupper(substr($item->tarif->shipmentInfo->nama,3,7)) }}</td>
                        <td>{{ $item->barang->nama }}</td>
                        <td class="text-center">{{ $item->tarif->stuffing??'-' }}</td>
                    </tr>
                @endforeach
            @endforeach
            @foreach ($orders->where('agen','!=','AGEN')->groupBy('penerima_bl_id') as $data)
                @foreach ($data as $item)
                    <tr>
                        @if ($loop->first)
                        <td class="text-center" rowspan="{{ $data->count() }}">{{ $item->penerima_bl->nama ?? '-' }}</td>
                        <td class="text-center" rowspan="{{ $data->count() }}">
                            <select>
                                @foreach ($pengirim as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        @endif
                        <td style="padding-left: 10px !important"> <a href="{{ route('bttb.index',['order_id'=>$item->id]) }}"> {{ $item->container }} / {{ $item->seal }}</a></td>
                        <td class="text-center">{{ strtoupper(substr($item->tarif->shipmentInfo->nama,3,7)) }}</td>
                        <td>{{ $item->barang->nama }}</td>
                        <td class="text-center">{{ $item->tarif->stuffing??'-' }}</td>
                    </tr>
                @endforeach
            @endforeach
        @endif
    </tbody>
</table>
