@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<style>
    input{
        font-size: .7rem;
    }
    select{
        font-size: .7rem;
        width: 200px;
    }
</style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card p-2">
                    <form action="{{ route('jurnal.balik.create') }}" method="get" class="row" id="form-submit">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                {{-- <input name="nomor" placeholder="Nomor Jurnal" style="width: 300px" type="text"> --}}
                                {{-- <button class="btn btn-info btn-sm" type="button" onclick="addColumnDebit()">Tambah Kolom</button> --}}
                                <span>Form Jurnal Balik</span>
                            </div>
                            <hr>
                            <table class="table table-sm" id="table-debit">
                                <tr>
                                    <td>Tipe</td>
                                    <td id="label">ID JOB</td>
                                    <td>Noted</td>
                                </tr>
                                <tr>
                                    <td>
                                        <select name="tipe" id="tipe" class="form-control">
                                            <option selected value="no_job">TANPA JOB</option>
                                            <option value="job">JOB</option>
                                            <option value="id_job">ID JOB</option>
                                        </select>
                                    </td>
                                    <td id="job">
                                        <select class="form-control select2" name="order_id" style="font-size:.9rem !important; width:300px">
                                            <option value=""></option>
                                            @foreach ($orders as $item)
                                            <option {{  request('order_id')==$item->id?'selected':''  }} value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <ul>
                                            <li><small>*Tipe "TANPA JOB" tidak perlu input job/id job</small></li>
                                            <li><small>*Tipe "JOB" pilih input ID JOB bebas sesuai Group Job nya</small></li>
                                            <li><small>*Tipe "ID JOB" pilih ID JOB harus sesuai dengan ID JOB nya</small></li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                            <table class="table table-sm" id="table-debit">
                                <tr>
                                    <td>Tanggal Awal</td>
                                    <td>Tanggal Akhir</td>
                                    <td colspan="2">Kriteria Kode</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="date" name="start" id="start" class="form-control" value="{{ request('start') }}">
                                    </td>
                                    <td>
                                        <input type="date" name="end" id="end" class="form-control" value="{{ request('end') }}">
                                    </td>
                                    <td colspan="2">
                                        <select name="name" id="name" class="form-control select2">
                                            <option value="">Pilih Kode</option>
                                            @foreach ($kode as $nama)
                                                <option value="{{ $nama['kode'] }}" {{ request('name') == $nama['kode'] ? 'selected' : '' }}>
                                                    {{ $nama['kode'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Akun Debet Awal</td>
                                    <td>Akun Credit Awal</td>
                                    <td>Akun Debet Tujuan</td>
                                    <td>Akun Credit Tujuan</td>
                                </tr>
                                <tr>
                                    <td style="width: 200px">
                                        <select class="form-control select2" name="debit_coa_id_tujuan" id="debit_coa_id_tujuan" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option {{ request('debit_coa_id_tujuan')==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control select2" name="credit_coa_id_tujuan" id="credit_coa_id_tujuan" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option {{ request('credit_coa_id_tujuan')==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control select2" name="debit_coa_id" id="debit_coa_id" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option {{ request('debit_coa_id')==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 200px">
                                        <select class="form-control select2" name="credit_coa_id" id="credit_coa_id" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                            <option {{ request('credit_coa_id')==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100" name="draf" value="1" id="btn-save">Buat Draf</button>
                    </form>
                </div>
            </div>
            @if (request('draf'))
            <div class="col-12 mt-3">
                <div class="card p-2">
                    <span class="border-bottom border-3 border-dark fw-bold" style="font-size: 1rem">Jurnal Awal</span>
                    <div class="table-responsive" style="height:250px">
                        <table class="table table-sm" style="font-size: .7rem; white-space:nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No. Jurnal</th>
                                    <th>ID Job</th>
                                    <th>Inv. External</th>
                                    <th>Account</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($new as $item)
                                    @if ($item['credit'])
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item['credit']->nomor }}</td>
                                        @if ($item['credit']->order)
                                        <td>{{ $item['credit']->order->job }}-{{ sprintf('%02d',$item['credit']->order->no_job) }}</td>
                                        @else
                                        <td>-</td>
                                        @endif
                                        <td>{{ $item['credit']->invoice_external ?? '-' }}</td>
                                        <td>{{ $item['credit']->coa->kode }} - {{ $item['credit']->coa->nama }}</td>
                                        <td>{{  $item['credit']->debit == 0 ? '-' : number_format($item['credit']->debit,2,'.',',') }}</td>
                                        <td>{{  $item['credit']->credit == 0 ? '-' : number_format($item['credit']->credit,2,'.',',') }}</td>
                                        <td>{{ $item['credit']->nama }}</td>
                                    </tr>
                                    @endif
                                    @if ($item['debit'])
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item['debit']->nomor }}</td>
                                        @if ($item['debit']->order)
                                        <td>{{ $item['debit']->order->job }}-{{ sprintf('%02d',$item['debit']->order->no_job) }}</td>
                                        @else
                                        <td>-</td>
                                        @endif
                                        <td>{{ $item['debit']->invoice_external ?? '-' }}</td>
                                        <td>{{ $item['debit']->coa->kode }} - {{ $item['debit']->coa->nama }}</td>
                                        <td>{{  $item['debit']->debit == 0 ? '-' : number_format($item['debit']->debit,2,'.',',') }}</td>
                                        <td>{{  $item['debit']->credit == 0 ? '-' : number_format($item['debit']->credit,2,'.',',') }}</td>
                                        <td>{{ $item['debit']->nama }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card p-2">
                        <span class="border-bottom border-3 border-dark fw-bold" style="font-size: 1rem"> Pilih Jurnal</span>
                        <div class="row my-2">
                            <div class="col">
                                <label for="uncheck">
                                    <input type="checkbox" name="uncheck" id="uncheck" checked> Check / Uncheck All
                                </label>
                                <button type="button" id="hapusTerpilih" class="btn btn-warning btn-sm m-2">Simpan di jurnal valid</button>
                                {{-- <input name="nomor" placeholder="Nomor Jurnal" required style="width: 100%" type="text"> --}}
                            </div>
                            <div class="col">
                                {{-- <input type="date" style="width: 100%" name="created_at" required value="{{ request('created_at') ?? date('Y-m-d') }}"> --}}
                            </div>
                        </div>
                                               <div class="table-responsive" style="height:250px">
                            <table class="table table-sm" style="font-size: .7rem; white-space:nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>No</th>
                                        {{-- <th>ID Job</th> --}}
                                        <th>Inv. Agen / Inv. External</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $k = 1;
                                    @endphp
                                    @foreach ($new as $idx => $item)
                                    @if ($item['debit'])
                                    <tr>
                                            <td><input type="checkbox" name="check[]" value="{{ $k }}" id="check-{{ $k }}" class="checkbox-name" data-amount="{{ $item['debit']->credit + $item['debit']->debit }}" checked></td>
                                            <td style="display: none;">
                                            <input type="hidden" value="{{ $item['debit']->id }}" name="jurnal[{{ $k }}][jurnal_balik]">
                                        </td>
                                            <td>{{ $k }}</td>
                                            @if ($item['debit']->order)
                                                {{-- <td>{{ $item['debit']->order->job }}-{{ sprintf('%02d',$item['debit']->order->no_job) }}</td> --}}
                                            @else
                                                {{-- <td>-</td> --}}
                                            @endif
                                            <td> {{ $item['debit']->invoice_agen ?? '-' }} /  {{ $item['debit']->invoice_external ?? '-' }}</td>
                                            @if ($item['debit']->debit==0)
                                                <input type="hidden" value="{{ $coa_debit->id }}" name="jurnal[{{ $k }}][coa_id]">
                                                {{-- <td>{{ $coa_debit->kode }}</td> --}}
                                            @else
                                                <input type="hidden" value="{{ $coa_credit->id }}" name="jurnal[{{ $k }}][coa_id]">
                                                {{-- <td>{{ $coa_credit->kode }}</td> --}}
                                            @endif
                                            <td>{{  $item['debit']->credit == 0 ? '-' : number_format($item['debit']->credit,2,'.',',') }}</td>
                                            <td>{{  $item['debit']->debit == 0 ? '-' : number_format($item['debit']->debit,2,'.',',') }}</td>
                                            <td><input type="hidden" class="input-name" name="jurnal[{{ $k }}][nama]" value="{{ $item['debit']->nama }}" required id="name-{{ $k }}"  style="width: 100%">{{ $item['debit']->nama }}</td>
                                        </tr>
                                        @php
                                            $k++;
                                        @endphp
                                        @endif

                                        @if ($item['credit'])
                                        <tr>
                                            <td><input type="checkbox" name="check[]" value="{{ $k }}" id="check-{{ $k }}" class="checkbox-name" data-amount="{{ $item['credit']->debit + $item['credit']->credit }}" checked></td>
                                            <td style="display: none;">
                                            <input type="hidden" value="{{ $item['credit']->id }}" name="jurnal[{{ $k }}][jurnal_balik]">
                                            </td>
                                            <td>{{ $k }}</td>
                                            @if ($item['credit']->order)
                                                {{-- <td>{{ $item['credit']->order->job }}-{{ sprintf('%02d',$item['credit']->order->no_job) }}</td> --}}
                                            @else
                                                {{-- <td>-</td> --}}
                                            @endif
                                            <td> {{ $item['credit']->invoice_agen ?? '-' }} /  {{ $item['credit']->invoice_external ?? '-' }}</td>
                                            @if ($item['credit']->debit==0)
                                                <input type="hidden" value="{{ $coa_debit->id }}" name="jurnal[{{ $k }}][coa_id]">
                                                {{-- <td>{{ $coa_debit->kode }}</td> --}}
                                            @else
                                                <input type="hidden" value="{{ $coa_credit->id }}" name="jurnal[{{ $k }}][coa_id]">
                                                {{-- <td>{{ $coa_credit->kode }}</td> --}}
                                            @endif
                                            <td>{{  $item['credit']->credit == 0 ? '-' : number_format($item['credit']->credit,2,'.',',') }}</td>
                                            <td>{{  $item['credit']->debit == 0 ? '-' : number_format($item['credit']->debit,2,'.',',') }}</td>
                                            <td><input type="hidden" name="jurnal[{{ $k }}][nama]" value="{{ $item['credit']->nama }}" required id="name-{{ $k }}" class="input-name" style="width: 100%"> {{ $item['credit']->nama }} </td>
                                        </tr>
                                        @php
                                            $k++;
                                        @endphp
                                        @endif
                                    @endforeach
                                    @if (request('credit_coa_id_tujuan'))
                                    <input type="hidden" value="{{ $item['credit']->id }}" name="jurnal[{{ $k }}][jurnal_balik]">
                                        {{-- <tr>
                                            <td></td>
                                            <td>{{ $k }}</td>
                                            <td></td>
                                            <td>-</td>
                                            <td>{{ $coa_credit->kode }} - {{ $coa_credit->nama }}</td>
                                            <td>-</td>
                                            <td id="value">{{ number_format($data->sum('credit')) }}</td>
                                            <td><input type="text" name="jurnal[{{ $k }}][nama]" id="" style="width: 100%" required></td>
                                        </tr> --}}
                                        <input type="hidden" id="hidden-value" value="{{ $data->sum('credit') }}" name="jurnal[{{ $k }}][credit]">
                                    @else
                                        <input type="hidden" id="hidden-value" value="{{ $data->sum('debit')  }}" name="jurnal[{{ $k }}][debit]">
                                        <input type="hidden" value="0" name="jurnal[{{ $k }}][credit]">
                                        <input type="hidden" value="{{ $coa_debit->id }}" name="jurnal[{{ $k }}][coa_id]">
                                        {{-- <tr>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>{{ $coa_debit->kode }} - {{ $coa_debit->nama }}</td>
                                            <td id="value">{{ number_format($data->sum('debit')) }}</td>
                                            <td>-</td>
                                            <td><input type="text" name="jurnal[{{ $k }}][nama]" id="" style="width: 100%" required></td>
                                        </tr> --}}
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                        <div class="col-12 mt-3">
                            <div class="card p-2">
                                <div class="table-responsive" style="height:250px">
                                    <form action="{{ route('jurnal.balik.store') }}" method="post">
                                        @csrf
                                    <input type="hidden" name="order_id" value="{{ request('order_id') }}">
                                    <input type="hidden" name="debit_coa_id_tujuan" value="{{ request('debit_coa_id_tujuan') }}">
                                    <input type="hidden" name="credit_coa_id_tujuan" value="{{ request('credit_coa_id_tujuan') }}">
                                     <input type="hidden" name="kode" value="{{ request('name') }}">
                                    <input type="hidden" name="no" id="no_jurnal">
                                    <input type="hidden" name="tipe" id="type_jurnal">
                                    <span class="border-bottom border-3 border-dark fw-bold" style="font-size: 1rem">Jurnal Valid</span>
                                    <div class="col mt-3">
                                        <button type="button" id="reset" class="btn btn-danger btn-sm ml-5">Reset</button>
                                    </div>
                                <table class="table table-sm"  id="tabelTerpilih" style="font-size: .7rem; white-space:nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>No</th>
                                    <th>ID Job</th>
                                    <th>Inv. Agen/Inv. External</th>
                                    <th>Account</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (request('credit_coa_id_tujuan'))
                                    <input type="hidden" value="{{ $item['credit']->id }}" name="jurnal[{{ $k }}][jurnal_balik]">
                                <tr id="total-jurnal-row">
                                    <td></td>
                                    <td>{{ $k }}</td>
                                    <td></td>
                                    <td>-</td>
                                    <td>{{ $coa_credit->kode }} - {{ $coa_credit->nama }}</td>
                                    <td>-</td>
                                    <td id="value">{{ number_format($data->sum('credit')) }}</td>
                                    <td><input type="text" name="new_keterangan" style="width: 100%" required></td>
                                </tr>
                                <!-- Data terpilih akan dipindahkan ke sini -->
                                <input type="hidden" id="total-hidden" value="{{ $data->sum('credit') }}" name="jurnal[{{ $k }}][credit]">
                                <input type="hidden" id="coa-hidden" value="{{ $coa_credit->id }}" name="new_coa_id">
                                @else
                                <input type="hidden" value="{{ $item['debit']->id }}" name="jurnal[{{ $k }}][jurnal_balik]">
                                <input type="hidden" id="total-hidden" value="{{ $data->sum('debit')  }}" name="jurnal[{{ $k }}][debit]">
                                <input type="hidden" value="{{ $coa_debit->id }}" name="jurnal[{{ $k }}][coa_id]">
                                <tr id="total-jurnal-row">
                                    <td>-</td>
                                    <td>{{ $k }}</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>{{ $coa_debit->kode }} - {{ $coa_debit->nama }}</td>
                                    <td id="value"><input type="hidden" name="nama" value="{{ $data->sum('debit') }}" id="" style="width: 100%" required>{{ number_format($data->sum('debit')) }}</td>
                                    <td>-</td>
                                    <td><input type="text" name="new_keterangan" style="width: 100%" required></td>
                                <input type="hidden" id="coa-hidden" value="{{ $coa_debit->id }}" name="new_coa_id">
                                </tr>

                            @endif
                            </tbody>
                        </table>
                            </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 d-flex gap-3">
                            <div>
                                <label for="" class="form-label">Nomor Jurnal</label>
                                <select name="nomor" id="nomor_jurnal" class="form-control" required>
                                    {{-- <option data-no="{{ $no_1 }}" selected data-type="JNL" value="{{ $nomor_1 }}">{{ $nomor_1 }}</option>
                                    <option data-no="{{ $no_2 }}" data-type="BBK" value="{{ $nomor_2 }}">{{ $nomor_2 }}</option> --}}
                                    {{-- <option data-no="{{ $no_3 }}" data-type="BBM" value="{{ $nomor_3 }}">{{ $nomor_3 }}</option> --}}
                                    <option data-no="{{ $no_4 }}" data-type="BKK" value="{{ $nomor_4 }}">{{ $nomor_4 }}</option>
                                    {{-- <option data-no="{{ $no_5 }}" data-type="BKM" value="{{ $nomor_5 }}">{{ $nomor_5 }}</option> --}}
                                </select>
                            </div>
                            <button type="submit" id="btn-submit" class="btn btn-success btn-sm mt-3" onclick="return confirm('Are you sure?')">Simpan Jurnal Balik</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.select2').select2();
    </script>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
    $('#name').select2({
        placeholder: "Pilih Nama",
        allowClear: true
    });
});

</script>
<script>
    $(document).ready(function () {
    function cekCheckbox() {
        let totalCheckbox = $('input.checkbox-name').length;
        let checkedCheckbox = $('input.checkbox-name:checked').length;

        if (totalCheckbox > 0 && totalCheckbox === checkedCheckbox) {
            $('#btn-submit').show();
        } else {
            $('#btn-submit').hide();
        }
    }

    // Jalankan saat halaman dimuat
    cekCheckbox();

    // Jalankan saat checkbox diubah
    $('input.checkbox-name').change(function () {
        cekCheckbox();
    });
});

    let credit = 2;
    let debit = 2;
    $('.select2').select2();
    $('#reset').click(function (e) {
        location.reload();
    });
    $('#template_id').click(function (e) {
        setTimeout(() => {
            $('.select2').select2();
        }, 2000);
    });

    setTimeout(() => {
            $('.select2').select2();
        }, 2000);


    // $('#btn-save').click(function (e) {
    //     if(confirm('are you sure')){
    //         $('#form-submit').submit();
    //     }
    // });

    let total_all = parseInt($('#hidden-value').val());

    $('#uncheck').click(function (e) {
    $('input:checkbox').prop('checked', this.checked);
    
    let sum = this.checked ? total_all : 0;
    $('#hidden-value').val(sum);
    $('#value').html(sum.toLocaleString('en-US'));
});
$(document).ready(function () {
    // Sembunyikan tombol submit saat halaman pertama kali dimuat
    $('#btn-submit').hide();
}); 

function updateButtonVisibility() {
    let tabelBaru = $('#tabelTerpilih tbody');
    let tabelBaru1 = $('#tabelTerpilih tbody').length;
    let checkedCount = tabelBaru.find('input.checkbox-name:checked').length;
    let uncheckedCount = tabelBaru.find('input.checkbox-name:not(:checked)').length;

    if ( uncheckedCount === 0 ) {
        $('#btn-submit').show();
    } else {
        $('#btn-submit').hide();
    }
}

$('#hapusTerpilih').click(function () {
    let tabelBaru = $('#tabelTerpilih tbody'); 
    let totalCredit = 0;
    let totalRow = $('#total-jurnal-row'); 

    $('input.checkbox-name:checked').each(function () {
        let row = $(this).closest('tr'); 
        let amount = parseFloat($(this).data('amount')) || 0;

        console.log("Memindahkan row dengan amount:", amount);

        // Pindahkan baris ke tabel baru
        tabelBaru.append(row);
    });

    // Hitung ulang total kredit hanya dari checkbox yang dicentang di tabel tujuan
    tabelBaru.find('input.checkbox-name:checked').each(function () { 
        let amount = parseFloat($(this).data('amount')) || 0;
        totalCredit += amount;
    });

    // Update total kredit setelah jurnal dipindahkan
    $('#total-hidden').val(totalCredit);
    $('#value').html(totalCredit.toLocaleString('en-US'));

    // Pastikan baris total jurnal selalu di paling bawah
    tabelBaru.append(totalRow);

    // Perbarui visibilitas tombol submit
    updateButtonVisibility();
});

// **Tambahkan event listener saat checkbox diubah**
$(document).on('change', 'input.checkbox-name', function () {
    updateButtonVisibility();
});




    $('#reset').click(function () {
        location.reload(); // Reload halaman
    });



    $('#nomor_jurnal').change(function (e) {
        var no = $(this).find(':selected').data('no');
        var type = $(this).find(':selected').data('type');
        $('#no_jurnal').val(no);
        $('#type_jurnal').val(type);
    });

    $('#debit_coa_id_tujuan').change(function (e) {
        var val = $(this).val();
        if(val!=''||val){
            $('#credit_coa_id').val(val).trigger('change');
            $('#credit_coa_id_tujuan').attr('disabled',true);
        }else{
            $('#credit_coa_id_tujuan').attr('disabled',false);
        }
    });

    $('#credit_coa_id_tujuan').change(function (e) {
        var val = $(this).val();
        if(val!=''||val){
            $('#debit_coa_id').val(val).trigger('change');
            $('#debit_coa_id_tujuan').attr('disabled',true);
        }else{
            $('#debit_coa_id_tujuan').attr('disabled',false);
        }
    });

    $(".checkbox-name").change(function() {
    let amount = parseInt($(this).data('amount'));
    console.log("Checkbox changed:", this.checked, "Amount:", amount);

    if (this.checked) {
        let currentValue = parseInt($('#hidden-value').val());
        let sum = currentValue + amount;

        console.log("Checkbox checked. Previous value:", currentValue, "New value:", sum);

        $('#name-' + $(this).val()).attr('disabled', false);
        $('#hidden-value').val(sum);
        $('#value').html(sum.toLocaleString('en-US'));

        console.log("Element enabled: #name-" + $(this).val());
    } else {
        let currentValue = parseInt($('#hidden-value').val());
        let sum = currentValue - amount;

        console.log("Checkbox unchecked. Previous value:", currentValue, "New value:", sum);

        $('#name-' + $(this).val()).attr('disabled', true);
        $('#hidden-value').val(sum);
        $('#value').html(sum.toLocaleString('en-US'));

        console.log("Element disabled: #name-" + $(this).val());
    }

    console.log("Current hidden value:", $('#hidden-value').val());
    console.log("Displayed value:", $('#value').text());
});


</script>
@endpush
