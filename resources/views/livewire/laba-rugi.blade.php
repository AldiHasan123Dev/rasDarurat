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
                <select class="form-control" wire:model="year" style="width: 70px">
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
    <div class="col-6">
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">A. PENJUALAN USAHA</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_penjualan_usaha = 0;
                @endphp
                @foreach ($penjualan_usaha as $item)
                @php
                    $total_penjualan_usaha += $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-end">{{ number_format($item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td class="text-end"><b>{{ number_format($total_penjualan_usaha,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">B. HARGA POKOK PENJUALAN</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_hpp = 0;
                @endphp
                @foreach ($hpp as $item)
                @php
                    $total_hpp += $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-end">{{ number_format($item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td class="text-end"><b>{{ number_format($total_hpp,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">C. BIAYA USAHA</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_biaya_usaha = 0;
                @endphp
                @foreach ($biaya_usaha as $item)
                @php
                    $total_biaya_usaha += $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-end">{{ number_format($item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td class="text-end"><b>{{ number_format($total_biaya_usaha,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">D. BIAYA DEPRESIASI</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_biaya_depresiasi = 0;
                @endphp
                @foreach ($biaya_depresiasi as $item)
                @php
                    $total_biaya_depresiasi += $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-end">{{ number_format($item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td class="text-end"><b>{{ number_format($total_biaya_depresiasi,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">E. PENDAPATAN DAN BIAYA LAIN-LAIN</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_biaya_lain = 0;
                @endphp
                @foreach ($biaya_lain as $item)
                @php
                    $total_biaya_lain += $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-end">{{ number_format($item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td class="text-end"><b>{{ number_format($total_biaya_lain,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
        <table class="table table-sm" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">F. BIAYA KEUANGAN I</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_biaya_keuangan1 = 0;
                @endphp
                @foreach ($biaya_keuangan1 as $item)
                @php
                    $total_biaya_keuangan1 += $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-end">{{ number_format($item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td class="text-end"><b>{{ number_format($total_biaya_keuangan1,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
        <table class="table table-sm mt-3" style="font-size: .7rem">
            <thead>
                <tr>
                    <th colspan="3">G. BIAYA KEUANGAN II</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_biaya_keuangan2 = 0;
                @endphp
                @foreach ($biaya_keuangan2 as $item)
                @php
                    $total_biaya_keuangan2 += $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit');
                @endphp
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-end">{{ number_format($item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('debit') - $item->jurnals()->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('credit'),2,',','.') }}</td>
                </tr>
                @endforeach
                <tfoot>
                    <tr>
                        <td colspan="2"><b>TOTAL</b></td>
                        <td class="text-end"><b>{{ number_format($total_biaya_keuangan2,2,',','.') }}</b></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
    </div>
    <div class="col-6">
        <div class="card shadow p-3">
            <table class="table table-sm" style="font-size: .7rem">
                <thead>
                    <tr>
                        <th colspan="2">SUMMARY</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>TOTAL PENJUALAN USAHA</td>
                        <td class="text-end">{{ number_format($total_penjualan_usaha,2,',','.') }}</td>
                    </tr>
                    <tr style="border-bottom: 3px solid black !important;">
                        <td>TOTAL HARGA POKOK PENJUALAN</td>
                        <td class="text-end">{{ number_format($total_hpp,2,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>LABA/RUGI KOTOR</td>
                        <td class="text-end">{{ number_format($total_penjualan_usaha - $total_hpp,2,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>TOTAL BIAYA USAHA</td>
                        <td class="text-end">{{ number_format($total_biaya_usaha,2,',','.') }}</td>
                    </tr>
                    <tr style="border-bottom: 3px solid black !important;">
                        <td>TOTAL BIAYA PENYUSUTAN</td>
                        <td class="text-end">{{ number_format($total_biaya_depresiasi,2,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>LABA/RUGI USAHA</td>
                        <td class="text-end">{{ number_format($total_penjualan_usaha - $total_hpp - $total_biaya_usaha - $total_biaya_depresiasi,2,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>TOTAL PENDAPATAN DAN BIAYA LAIN-LAIN</td>
                        <td class="text-end">{{ number_format($total_biaya_lain,2,',','.') }}</td>
                    </tr>
                    <tr style="border-bottom: 3px solid black !important;">
                        <td>TOTAL BIAYA KEUANGAN I</td>
                        <td class="text-end">{{ number_format($total_biaya_keuangan1,2,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>LABA/RUGI BERSIH SEBELUM PAJAK</td>
                        <td class="text-end">{{ number_format($total_penjualan_usaha - $total_hpp - $total_biaya_usaha - $total_biaya_depresiasi - $total_biaya_lain - $total_biaya_keuangan1,2,',','.') }}</td>
                    </tr>
                    <tr style="border-bottom: 3px solid black !important;">
                        <td>TOTAL BIAYA KEUANGAN II</td>
                        <td class="text-end">{{ number_format($total_biaya_keuangan2,2,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>LABA/RUGI BERSIH SESUDAH PAJAK</td>
                        <td class="text-end">{{ number_format($total_penjualan_usaha - $total_hpp - $total_biaya_usaha - $total_biaya_depresiasi - $total_biaya_lain - $total_biaya_keuangan1 - $total_biaya_keuangan2,2,',','.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
