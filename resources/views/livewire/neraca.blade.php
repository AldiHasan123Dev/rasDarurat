<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between">
            <div class="d-flex gap-2">
                <b class="mt-2">Bulan: </b>
                @foreach ($months as $idx => $item)
                    <button wire:click="changeMonth({{ $idx+1 }})" class="{{ $idx+1==(int)$month?'bg-light-success':'' }}" style="background: transparent; border: solid 1px gray; width:50px">{{ $item }}</button>
                @endforeach
            </div>
            <div class="d-flex gap-2">
                <b class="mt-2">Tahun: </b>
                <select class="form-control" wire:model="year" wire:change="changeYear" style="width: 70px">
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2028">2028</option>
                    <option value="2029">2029</option>
                    <option value="2030">2030</option>
                </select>
            </div>
        </div>
    </div>
    {{-- <div class="col-12">
        <pre>{{ $start }}</pre>
        <pre>{{ $end }}</pre>
    </div> --}}
    <div class="col-6">
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">AKTIVA LANCAR</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_aktiva_lancar = 0;
                @endphp
                @foreach ($aktiva_lancar as $item)
                @php
                    $total_aktiva_lancar += $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('debit') - $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('credit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ number_format($item->jurnals()->whereBetween('created_at',[$start,$end])->sum('debit') - $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('credit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td><b>{{ number_format($total_aktiva_lancar,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">AKTIVA TAK LANCAR</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_aktiva_tak_lancar = 0;
                @endphp
                @foreach ($aktiva_tak_lancar as $item)
                @php
                    $total_aktiva_tak_lancar += $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('debit') - $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('credit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ number_format($item->jurnals()->whereBetween('created_at',[$start,$end])->sum('debit') - $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('credit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td><b>{{ number_format($total_aktiva_tak_lancar,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
    </div>
    <div class="col-6">
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">KEWAJIBAN</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_kewajiban = 0;
                @endphp
                @foreach ($kewajiban as $item)
                @php
                    $total_kewajiban += $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('credit') - $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('debit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ number_format($item->jurnals()->whereBetween('created_at',[$start,$end])->sum('credit') - $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('debit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td><b>{{ number_format($total_kewajiban,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">MODAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_modal = 0;
                @endphp
                @foreach ($modal as $item)
                @php
                    if ($item->kode=='3.2') {
                        $total_modal+=$lr;
                    } else {
                        $total_modal += $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('credit') - $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('debit');
                    }
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    @if ($item->kode=='3.2')
                    <td>{{ number_format($lr,2,',','.') }}</td>
                    @else
                    <td>{{ number_format($item->jurnals()->whereBetween('created_at',[$start,$end])->sum('credit') - $item->jurnals()->whereBetween('created_at',[$start,$end])->sum('debit'),2,',','.') }}</td>
                    @endif
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td><b>{{ number_format($total_modal,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
    </div>
    <div class="col-12">
        <div class="card shadow p-3">
            <table class="table table-sm" style="font-size: .7rem">
                <thead>
                    <tr>
                        <th colspan="2">DETAIL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>TOTAL AKTIVA</td>
                        <td class="text-end">{{ number_format($total_aktiva_lancar + $total_aktiva_tak_lancar,2,',','.') }}</td>
                    </tr>
                    <tr style="border-bottom: 3px solid black !important;">
                        <td>TOTAL PASIVA</td>
                        <td class="text-end">{{ number_format($total_kewajiban + $total_modal,2,',','.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
