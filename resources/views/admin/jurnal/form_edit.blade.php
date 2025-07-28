@extends('layouts.iframe')
@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/selectize.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/selectize.bootstrap5.css') }}">
@endsection
@section('content')
    <form action="{{ route('jurnal.update.one', $jurnal) }}" method="post">
        <input type="text" hidden name="relasi1" id="relasi1" value="{{ $jurnal->relasi }}">
        @csrf
        @method('PUT')
        <div class="row">
            @if ($tipe == 'xpdc')
            <div class="col-12 mb-3">
                <label for="order_id">JOB</label>
                <select class="form-control select2" id="job-{{ $jurnal->id }}" name="job" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders as $item)
                    <option {{ $jurnal->order_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }} / {{ $item->invoice }}</option>
                    @endforeach
                </select>
            </div>         
             <div class="col-12 mb-3">
                <label for="trucking">Trucking</label><br>
                <select class="form-control select2" id="trucking12-{{ $jurnal->id }}" name="trucking" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders_trucking1 as $item)
                    <option {{ $jurnal->order_trucking_id==$item->id?'selected':'' }} value="{{ $item->id }}">
                        {{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }}
                    </option>
                    @endforeach
                </select>
            </div>      
            @elseif ($tipe == 'trucking')
             <div class="col-12 mb-3">
                <label for="job">Job</label><br>
                <select class="form-control select2" id="job-{{ $jurnal->id }}" name="job" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders as $item)
                    <option {{ $jurnal->order_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d', $item->no_job) }} / {{ $item->seal }} </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 mb-3">
                <label for="trucking">Trucking</label><br>
                <select class="form-control select2" id="trucking12-{{ $jurnal->id }}" name="trucking" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders_trucking1 as $item)
                    <option {{ $jurnal->order_trucking_id==$item->id?'selected':'' }} value="{{ $item->id }}">
                                {{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }} </option>
                    @endforeach
                </select>
            </div>
            @else
            <div class="col-12 mb-3">
                <label for="job">Job</label><br>
                <select class="form-control select2" id="job-{{ $jurnal->id }}" name="job" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders as $item)
                    <option {{ $jurnal->order_id==$item->id?'selected':'' }} value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d', $item->no_job) }} / {{ $item->seal }} </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 mb-3">
                <label for="trucking">Trucking</label><br>
                <select class="form-control select2" id="trucking12-{{ $jurnal->id }}" name="trucking" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders_trucking1 as $item)
                    <option {{ $jurnal->order_trucking_id==$item->id?'selected':'' }} value="{{ $item->id }}">
                        {{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            @if ($tipe=='xpdc')
            <div class="col-12 mb-3">
                <label>Pilih Doc</label><br>
                <select class="form-control select2 tipe" id="doc"
                style="font-size:.9rem !important"
                    onchange="updateList()">
                    <option value=""></option>
                    <option value="inv_expdc">Inv Expdc</option>
                    <option value="inv_agen">Inv Agen</option>
                    <option value="pelayaran">BG Pelayaran</option>
                    <option value="lain-lain">Lain-lain</option>
                    <option value="relasi">Relasi</option>
                </select>
            </div>
            @endif
            @if ($tipe=='trucking')
            <div class="col-12 mb-3">
                <label>Pilih Doc</label><br>
                <select class="form-control select2 tipe" id="doc"
                    style="font-size:.9rem !important"
                    onchange="updateList()">
                    <option value=""></option>
                    <option value="inv_trucking">Inv Trucking</option>
                    <option value="inv_vendor">Inv Vendor</option>
                    <option value="lain-lain">Lain-lain</option>
                    <option value="relasi">Relasi</option>
                </select>
            </div>
            @endif
            @if ($tipe=='lain-lain')
            <div class="col-12 mb-3">
                <label>Pilih Doc</label><br>
                <select class="form-control select2 tipe" id="doc"
                    style="font-size:.9rem !important"
                    onchange="updateList()">
                    <option value=""></option>
                    <option value="inv_expdc">Inv Expdc</option>
                    <option value="inv_agen">Inv Agen</option>
                    <option value="pelayaran">BG Pelayaran</option>
                    <option value="inv_trucking">Inv Trucking</option>
                    <option value="inv_vendor">Inv Vendor</option>
                    <option value="lain-lain">Lain-lain</option>
                    <option value="relasi">Relasi</option>
                </select>
            </div>
            @endif
            <div id="dynamic-container"></div>
            <div class="col-12 mb-3">
                <label for="coa_id">COA</label>
                <select class="form-control select2" id="coa_id-{{ $jurnal->id }}" name="coa_id"
                    style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($coa as $item)
                        <option {{ $jurnal->coa_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                            {{ $item->kode }} - {{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 mb-3">
                <label for="nama">Keterangan</label>
                <input class="form-control" onclick="this.select()" name="nama" id="nama-{{ $jurnal->id }}"
                    value="{{ $jurnal->nama }}" type="text">
            </div>
            <div class="col-12 mb-3">
                <label for="debit">Debit</label>
                <input class="form-control" onclick="this.select()" type="text" onkeyup="total()" name="debit"
                    id="debit-{{ $jurnal->id }}" value="{{ $jurnal->debit }}">
            </div>
            <div class="col-12 mb-3">
                <label for="credit">Credit</label>
                <input class="form-control" onclick="this.select()" type="text" onkeyup="total()" name="credit"
                    id="credit-{{ $jurnal->id }}" value="{{ $jurnal->credit }}">
            </div>
            <div class="col-12 mb-3">
                <button type="submit" class="btn btn-success w-100">Simpan</button>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('.select2').select2();
        $('#job').select2();
        $('#trucking12').select2();
        console.log($('#trucking12').html());

        function updateList() {
    const newValue = document.getElementById("doc").value; // Ambil nilai dari select doc
    const container = document.getElementById("dynamic-container"); // Container untuk elemen dinamis

    // Kosongkan elemen sebelumnya
    container.innerHTML = "";

    if (newValue === "inv_expdc") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="order_id">Inv Expdc</label><br>
                <select class="form-control select2" id="invoice_expdc" name="inv_expdc" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders_expdc as $item)
                    <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d', $item->no_job) }} / {{ $item->seal }} / {{ $item->invoice }}</option>
                    @endforeach
                </select>
            </div>
        `;
    } else if (newValue === "inv_agen") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="order_id1">Inv Agen</label><br>
                <select class="form-control select2" id="inv_agen" name="inv_agen" style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($orders_agen as $item)
                    <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d', $item->no_job) }} / {{ $item->seal }} / {{ $item->invoice_agen }}</option>
                    @endforeach
                </select>
            </div>
        `;
    } else if (newValue === "pelayaran") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="order_id">No BG</label>
                <select class="form-control select2" id="bg-{{ $jurnal->id }}" name="no_bg"
                    style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($bgs as $item)
                        <option {{ $jurnal->no_bg == $item ? 'selected' : '' }} value="{{ $item }}">{{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>
        `;
    } else if (newValue === "relasi") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="relasi">Relasi</label>
                <select class="form-control select2" id="relasi-{{ $jurnal->id }}" name="relasi">
                    <option value=""></option>
                    @foreach ($relasi as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        `;
    }  else if (newValue === "inv_trucking") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="relasi">Trucking</label>
                <select class="form-control select2" id="inv_truking-{{ $jurnal->id }}" name="inv_trucking">
                    <option value=""></option>
                    @foreach ($orders_trucking as $item)
                    <option {{ $jurnal->order_trucking_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                                {{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }}</option>
                    @endforeach
                </select>
            </div>
        `;
    } else if (newValue === "inv_vendor") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="relasi">Vendor Trucking</label>
                <select class="form-control select2" id="inv_vendor-{{ $jurnal->id }}" name="inv_vendor">
                    <option value=""></option>
                    @foreach ($orders_vendor as $item)
                    <option {{ $jurnal->order_trucking_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                                {{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }}</option>
                    @endforeach
                </select>
            </div>
        `;
    } else if (newValue === "lain-lain") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="relasi">Invoice Eksternal</label>
                <select class="form-control select2" onchange="handleLainLainChange(this)">
                    <option value="">Pilih Opsi</option>
                    <option value="buat-baru">Buat Baru</option>
                    <option value="kosongkan">Kosongkan</option>
                    <option value="pilih-invoice-external">Pilih Invoice External</option>
                </select>
            </div>
        `;
    } 
    // Inisialisasi ulang Select2 untuk elemen yang baru ditambahkan
    $(container).find(".select2").select2();
}

function handleLainLainChange(select, rowId) {
            const newValue = select.value;
            const dynamicColumn = document.getElementById(`dynamic-container`);
            dynamicColumn.innerHTML = ""; // Kosongkan kolom dinamis sebelumnya

            if (newValue === "buat-baru") {
                dynamicColumn.innerHTML = `
               <div class="col-12 mb-3">
                <label for="invoice_external">Invoice External</label>
                <input class="form-control" onclick="this.select()" name="invoice_external" id="invoice_external-{{ $jurnal->id }}"
                    value="{{ $jurnal->invoice_external }}" type="text">
            </div>
                                
        `;
            } else if (newValue === "pilih-invoice-external") {
                dynamicColumn.innerHTML = `
                <label for="relasi">Invoice Eksternal</label>
                <select class="form-control select3" name="invoice_external" id="invoice_external">
                    <option value="">Pilih Invoice External</option>
                     @foreach ($invx as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
        `;
                $(dynamicColumn.querySelector('.select3')).select2(); // Inisialisasi Select2
            } else if (newValue === "kosongkan") {
                dynamicColumn.innerHTML = `
                    <div class="col-12 mb-3">
                        <label for="invoice_external">Invoice External</label>
                        <input class="form-control" onclick="this.select()" placeholder="Inv lain-lain terhapus" 
                            name="invoice_external" id="invoice_external-{{ $jurnal->id }}" 
                            value="" disabled type="text">
                        <input type="hidden" name="invoice_external" value="">
                    </div>
                `;
            }
        }

    </script>
@endsection
