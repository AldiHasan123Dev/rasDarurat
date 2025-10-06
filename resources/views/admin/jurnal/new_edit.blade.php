@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/selectize.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/selectize.bootstrap5.css') }}">
<style>
    input{
        font-size: .7rem;
    }
    select{
        font-size: .7rem;
        width: 200px;
    }
    span.select2.select2-container{
        width: 100% !important;
    }
</style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <span>PARAM</span>
                    <div class="d-flex flex-wrap gap-2" style="white-space: nowrap">
                        <span class="bg-light-primary px-2 py-1">[1] ID JOB</span>
                        <span class="bg-light-primary px-2 py-1">[2] Cont (XPDC)</span>
                        <span class="bg-light-primary px-2 py-1">[3] Seal (XPDC)</span>
                        <span class="bg-light-primary px-2 py-1">[4] Kapal (XPDC)</span>
                        <span class="bg-light-primary px-2 py-1">[5] Voyage (XPDC)</span>
                        <span class="bg-light-primary px-2 py-1">[6] Shipment (XPDC)</span>
                        <span class="bg-light-primary px-2 py-1">[7] Pembayar (XPDC)</span>
                        <span class="bg-light-primary px-2 py-1">[8] Customer (TRUCKING)</span>
                        <span class="bg-light-primary px-2 py-1">[9] Shipment (TRUCKING)</span>
                        <span class="bg-light-primary px-2 py-1">[10] Tujuan (TRUCKING)</span>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-2">
                <div class="card p-3" id="form-jurnal">
                    <form action="{{ route('jurnal.update', $data) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <span>EDIT JURNAL</span>
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <label for="tipe_jurnal">Nomor Jurnal</label>
                                <input type="text" name="nomor" id="nomor" class="form-control" disabled value="{{ $data->nomor }}">
                            </div>
                            <div class="col-4">
                                <label for="created_at">Tanggal Jurnal</label>
                                <input type="date" name="created_at" id="created_at" value="{{ date('Y-m-d',strtotime($data->created_at)) }}" class="form-control">
                            </div>
                            <div class="col-2">
                                <button class="btn btn-success btn-sm mx-2 mt-3" type="submit" onclick="return confirm('are you sure?')">Simpan Tanggal</button>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-info btn-sm mx-2 mt-3" type="button" onclick="addModal({{ $data->kunci }})">Tambah Baris</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm mt-3" style="font-size: .7rem; white-space:nowrap">
                            <thead>
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>ID</th>
                                    <th>Job</th>
                                    <th>ID JOB</th>
                                    <th>Cont</th>
                                    <th>No BG</th>
                                    <th>Inv</th>
                                    <th>Inv Ext</th>
                                    <th>Inv Vendor</th>
                                    <th>Inv Trucking</th>
                                    <th>Inv Agen</th>
                                    <th>COA</th>
                                    <th>Keterangan</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Relasi</th>
                                </tr>
                            </thead>
                            <tbody id="data-body">
                                <tr>
                                    <td colspan="15" class="text-center">Loading</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex my-3 justify-content-between">
                        <div style="width: 200px">
                            Total Baris : {{ $count }}
                        </div>
                        <div class="d-flex flex-wrap">
                            {{-- @for ($i = 0; $i <= ((int)$count/10) + 1; $i++)
                                <button type="button" class="btn btn-sm mt-2 mx-1 btn-{{$i==1?'primary':'secondary'}}" id="page-{{ $i }}" onclick="changePage({{ $i }})">{{$i==0?'All':$i}}</button>
                            @endfor --}}
                        </div>
                    </div>
                    <table>
                        <tr>
                            <td style="width: 300px"><b>TOTAL DEBET</b></td>
                            <td><b id="total_debit">{{ number_format($deb,2,',','.') }}</b></td>
                        </tr>
                        <tr>
                            <td style="width: 300px"><b>TOTAL CREDIT</b></td>
                            <td><b id="total_credit">{{ number_format($cre,2,',','.') }}</b></td>
                        </tr>
                        <tr class="border border-top-md">
                            <td class="text-secondary" style="width: 300px"><span>CHECK VOUCHER</span></td>
                            <td class="text-secondary">{{ number_format($voucher,2,',','.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit" tabindex="-1"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">EDIT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="iframe-edit" style="width: 100%; height:440px"></iframe>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-add" tabindex="-1"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Jurnal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @if ($tipe == 'xpdc')
                        <div class="col-12 mb-3">
                            <label for="order_id1">Job</label><br>
                            <select class="form-control" id="job" name="job" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($orders as $item)
                                <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d', $item->no_job) }} / {{ $item->seal }} </option>
                                @endforeach
                            </select>
                        </div>
                        @elseif ($tipe == 'trucking')
                        <div class="col-12 mb-3">
                            <label for="order_id1">Trucking</label><br>
                            <select class="form-control" id="trucking12" name="trucking" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($orders_trucking1 as $item)
                                <option value="{{ $item->id }}">
                                            {{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }} </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="col-12 mb-3">
                            <label for="order_id1">Job</label><br>
                            <select class="form-control" id="job" name="job" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($orders as $item)
                                <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d', $item->no_job) }} / {{ $item->seal }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="order_id1">Trucking</label><br>
                            <select class="form-control" id="trucking12" name="trucking" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($orders_trucking1 as $item)
                                <option value="{{ $item->id }}">
                                            {{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }} </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                            <div class="col-12 mb-3">
                                <label>Pilih Doc</label><br>
                                <select class="form-control" id="doc" onchange="updateList()" name="order_id" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @if ($tipe == 'xpdc')
                                        <option value="inv_expdc">Inv Expdc</option>
                                        <option value="inv_agen">Inv Agen</option>
                                        <option value="pelayaran">BG Pelayaran</option>
                                    @elseif ($tipe == 'trucking')
                                        <option value="inv_trucking">Inv Trucking</option>
                                        <option value="inv_vendor">Inv Vendor</option>
                                     @else    
                                     <option value="inv_expdc">Inv Expdc</option>
                                     <option value="inv_agen">Inv Agen</option>
                                     <option value="inv_trucking">Inv Trucking</option>
                                    <option value="inv_vendor">Inv Vendor</option>
                                    @endif
                                    <option value="lain-lain">Lain-lain</option>
                                    <option value="relasi">Relasi</option>
                                </select>
                            </div>
                            <div id="dynamic-container"></div>
                        <div class="col-12 mb-3">
                            <label for="coa_id">COA</label><br>
                            <select class="form-control" id="coa_id" name="coa_id" style="font-size:.9rem !important">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="nama">Keterangan</label>
                            <input class="form-control" onclick="this.select()" name="nama" id="nama" type="text">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="debit">Debit</label>
                            <input class="form-control" onclick="this.select()" type="text" onkeyup="total()" value="0" name="debit" id="debit">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="credit">Credit</label>
                            <input class="form-control" onclick="this.select()" type="text" onkeyup="total()" value="0" name="credit" id="credit">
                        </div>
                        <div class="col-12 mb-3">
                            <button type="button" onclick="save()" class="btn btn-success w-100">Simpan</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function updateList() {
    const newValue = document.getElementById("doc").value; // Ambil nilai dari select doc
    const container = document.getElementById("dynamic-container"); // Container untuk elemen dinamis

    // Kosongkan elemen sebelumnya
    container.innerHTML = "";

    if (newValue === "inv_expdc") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="order_id">Job Expdc</label><br>
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
                <label for="order_id1">JOB Agen</label><br>
                <select class="form-control select2" id="invoice_agen" name="invoice_agen" style="font-size:.9rem !important">
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
                <select class="form-control select2" id="bg" name="no_bg"
                    style="font-size:.9rem !important">
                    <option value=""></option>
                    @foreach ($bgs as $item)
                        <option value="{{ $item }}">{{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>
        `;
    } else if (newValue === "relasi") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="relasi">Relasi</label>
                <select class="form-control select2" id="relasi" name="relasi">
                    <option value=""></option>
                    @foreach ($relasi as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        `;
    } else if (newValue === "inv_trucking") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="relasi">Inv Trucking</label>
                <select class="form-control select2" id="invoice_trucking" name="inv_trucking">
                    <option value=""></option>
                    @foreach ($orders_trucking as $item)
                    <option value="{{ $item->id }}">
                                {{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }}</option>
                    @endforeach
                </select>
            </div>
        `;
    } else if (newValue === "inv_vendor") {
        container.innerHTML = `
            <div class="col-12 mb-3">
                <label for="relasi">Vendor Trucking</label>
                <select class="form-control select2" id="invoice_vendor" name="inv_vendor">
                    <option value=""></option>
                    @foreach ($orders_vendor as $item)
                    <option value="{{ $item->id }}">
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
                    <option value="pilih-invoice-external">Pilih Invoice External</option>
                </select>
            </div>
        `;
    } 
    // Inisialisasi ulang Select2 untuk elemen yang baru ditambahkan
    $(container).find(".select2").select2({
        dropdownParent: $('#modal-add'),
    });
}

function handleLainLainChange(select, rowId) {
            const newValue = select.value;
            const dynamicColumn = document.getElementById(`dynamic-container`);
            dynamicColumn.innerHTML = ""; // Kosongkan kolom dinamis sebelumnya

            if (newValue === "buat-baru") {
                dynamicColumn.innerHTML = `
               <div class="col-12 mb-3">
                <label for="invoice_external">Invoice External</label>
                <input class="form-control" onclick="this.select()" name="invoice_external" id="invoice_external"
                    value="" type="text">
            </div>
                                
        `;
            } else if (newValue === "pilih-invoice-external") {
                dynamicColumn.innerHTML = `
                  <div class="col-12 mb-3">
                <label for="relasi">Invoice Eksternal</label>
                <select class="form-control select3" name="invoice_external" id="invoice_external">
                    <option value="">Pilih Invoice External</option>
                     @foreach ($invx as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
                 </div>
        `;
                $(dynamicColumn.querySelector('.select3')).select2({
                    dropdownParent: $('#modal-add'),
                }); // Inisialisasi Select2
            }
        }
        $('.select2').select2();
        $('#job').select2({
            dropdownParent: $('#modal-add'),
        });
        $('#trucking12').select2({
            dropdownParent: $('#modal-add'),
        });
        $('#coa_id').select2({
            dropdownParent: $('#modal-add'),
        });
        $('#order_id').select2({
            dropdownParent: $('#modal-add'),
        });
        $('#doc').select2({
            dropdownParent: $('#modal-add'),
        });
        $('#order_trcuking_id').select2({
            dropdownParent: $('#modal-add'),
        });
        var total_credit = 0;
        var total_debit = 0;
        var page = 1;
        var xhr;
        function uncheck (e,id) {
            if($('#' + id).is(":checked")){
                $('#job-'+id).attr('disabled',false);
                $('#coa_id-'+id).attr('disabled',false);
                $('#nama-'+id).attr('disabled',false)
                $('#amount-'+id).attr('disabled',false)
                $('#debit-'+id).attr('disabled',false)
                $('#credit-'+id).attr('disabled',false)
            }else{
                $('#job-'+id).attr('disabled',true);
                $('#coa_id-'+id).attr('disabled',true);
                $('#nama-'+id).attr('disabled',true)
                $('#amount-'+id).attr('disabled',true)
                $('#debit-'+id).attr('disabled',true)
                $('#credit-'+id).attr('disabled',true)
            }
            total();
        }

        function changePage(no){
            $('#page-'+page).removeClass('btn-primary').addClass('btn-secondary');
            $('#page-'+no).removeClass('btn-secondary').addClass('btn-primary');
            page = no;
            $('#data-body').html(`<tr>
                                    <td colspan="11" class="text-center">Loading</td>
                                </tr>`);
            xhr.abort()
            getData();
        }

        function addColumnDebit(){
            let tipe = @json($tipe);
            var amounts = $("input[name='id[]']").map(function(){return $(this).val();}).get();
            debit = amounts.length + 1;
            let html;
            if(tipe=='trucking'){
                html = `<tr>
                                <td style="width: 50px"><input id="add-${debit}" type="checkbox" onchange="uncheck(this,${debit})" name="id[]" value="${debit}" checked></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="add-job-id-${debit}" name="jurnal_create[${debit}][order_trucking_id]" style="font-size:.9rem !important">
                                            <option value=""></option>
                                            @foreach ($orders_trucking as $item)
                                                <option value="{{ $item->id }}">{{ $item->container }} - {{ $item->seal }}</option>
                                            @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="add-coa_id-${debit}" name="jurnal_create[${debit}][coa_id]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="jurnal_create[${debit}][nama]" id="add-nama-${debit}" style="width: 300px" type="text"></td>
                                <td><input type="text" onkeyup="total()" value="0" name="jurnal_create[${debit}][debit]" id="debit-${debit}"></td>
                                <td><input type="text" onkeyup="total()" value="0" name="jurnal_create[${debit}][credit]" id="credit-${debit}"></td>
                            </tr>`;
            }else{
                html = `<tr>
                                <td style="width: 50px"><input id="add-${debit}" type="checkbox" onchange="uncheck(this,${debit})" name="id[]" value="${debit}" checked></td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="add-job-${debit}" name="jurnal_create[${debit}][order_id]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($orders_expdc as $item)
                                        <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 200px">
                                    <select class="form-control select2" id="add-coa_id-${debit}" name="jurnal_create[${debit}][coa_id]" style="font-size:.9rem !important">
                                        <option value=""></option>
                                        @foreach ($coa as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="width: 300px"><input name="jurnal_create[${debit}][nama]" id="add-nama-${debit}" style="width: 300px" type="text"></td>
                                <td><input type="text" onkeyup="total()" value="0" name="jurnal_create[${debit}][debit]" id="debit-${debit}"></td>
                                <td><input type="text" onkeyup="total()" value="0" name="jurnal_create[${debit}][credit]" id="credit-${debit}"></td>
                            </tr>`;

            }
            $('#table-debit').append(html);
            setTimeout(() => {
                $('.select2').select2();
            }, 1000);
            debit++;
        }

        function total(){
            var check = $("input[name='id[]']").map(function(){
                if($(this).is(":checked")){
                    return $(this).val();
                }
            }).get();
            total_credit = 0;
            total_debit = 0;
            for (let i = 0; i < check.length; i++) {
                const item = check[i];
                var d = parseInt($('#debit-'+item).val());
                var c = parseInt($('#credit-'+item).val());
                if(d!=""){
                    total_debit+=d;
                }
                if(c!=""){
                    total_credit+=c;
                }
            }
            $('#total_debit').html('Rp. '+total_debit.toLocaleString('en-US'));
            $('#total_credit').html('Rp. '+total_credit.toLocaleString('en-US'));
        }

        $('#btn-save').click(function (e) {
            if(total_debit!=total_credit){
                alert('Jurnal Tidak Balance debit = '+total_debit+' & credit = '+total_credit+' ! Harap check lagi')
            }else{
                if(confirm('are you sure')){
                    $('#form-jurnal').submit();
                }
            }
        });

        function editModal(url, kunci) {
    kunci = parseInt(kunci); // pastikan jadi angka

    if (isNaN(kunci)) {
        console.error('Parameter kunci tidak valid:', kunci);
        return;
    }

    if (kunci === 1) {
        alert('Data ini sudah terkunci dan tidak bisa diedit.');
        return;
    }

    // Jika tidak terkunci, tampilkan modal
    var myModal = new bootstrap.Modal(document.getElementById('modal-edit'));
    $('#iframe-edit').attr('src', url);
    myModal.show();
}



        var modalBTTB = document.getElementById('modal-edit')
        modalBTTB.addEventListener('hidden.bs.modal', function (event) {
            getData()
        })

        function addModal(kunci){
             kunci = parseInt(kunci); // pastikan jadi angka

    if (isNaN(kunci)) {
        console.error('Parameter kunci tidak valid:', kunci);
        return;
    }

    if (kunci === 1) {
        alert('Data ini sudah terkunci dan tidak bisa ditambah baris.');
        return;
    }
            var myModal = new bootstrap.Modal(document.getElementById('modal-add'));
            myModal.show();
        }

        function deleteData(id, kunci){
             if (kunci === 1) {
        alert('Data ini sudah terkunci dan tidak bisa dihapus.');
        return;
    }
            if (confirm('Are you sure?')) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('api/jurnal/delete') }}",
                    data: {id:id},
                    success: function (response) {
                        alert('Data berhasil dihapus!');
                        getData();
                    }
                });
            }
        }

        function getData(){
            xhr = $.ajax({
                type: "POST",
                url: "{{ url('api/get-jurnal') }}",
                data: {nomor:@json($jur->nomor)},
                success: function (response) {
                    let html = '';
                    $.each(response, function (idx, item) {
                        html += `<tr>
                                        <td>
                                            <div class="d-flex">
                                                <button onclick="deleteData(${ item.id }, ${item.kunci})" type="button" style="border:none; background: transparent; color:red"><i class="fas fa-trash"></i></button>
                                                <button 
                                                    onclick="editModal('jurnal-edit-${item.id}', ${item.kunci})" 
                                                    type="button" 
                                                    style="border:none; background: transparent; color:rgb(41, 51, 226)">
                                                    <i class="fas fa-pencil"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>${item.id}</td>
                                        <td>${item.job}</td>
                                        <td>${item.no_job}</td>
                                        <td>${item.container}</td>
                                        <td>${item.no_bg}</td>
                                        <td>${item.invoice}</td>
                                        <td>${item.invoice_external}</td>
                                        <td>${item.invoice_vendor}</td>
                                        <td>${item.invoice_trucking}</td>
                                        <td>${item.invoice_agen}</td>
                                        <td>${item.coa_nama} - ${item.coa_kode}</td>
                                        <td>${item.nama}</td>
                                        <td>${item.debit}</td>
                                        <td>${item.credit}</td>
                                        <td>${item.relasi}</td>
                                    </tr>`;
                    });

                    $('#data-body').html(html);
                }
            });
        }

        function save(){
            var data = {
                invoice_expdc:$('#invoice_expdc').val() || $('#job').val() || null,
                invoice_agen:$('#invoice_agen').val() || $('#job').val() || null,
                invoice_vendor:$('#invoice_vendor').val() || $('#trucking').val() || null,
                invoice_trucking:$('#invoice_trucking').val() ||$('#trucking').val() || null,
                coa_id:$('#coa_id').val() || null,
                nama:$('#nama').val() || null,
                debit:$('#debit').val() || null,
                credit:$('#credit').val() || null,
                nomor:@json($jur->nomor),
                created_at:@json(date('Y-m-d',strtotime($jur->created_at))),
                no:@json($jur->no),
                tipe:@json($jur->tipe),
                invoice_external:$('#invoice_external').val() || null,
                relasi: $('#relasi').val() || @json($jur->nomor),
                no_bg:$('#bg').val() || null,
            };
            console.log(data);
            $.ajax({
                type: "POST",
                url: "{{ url('api/jurnal/add') }}",
                data: data,
                success: function (response) {
                    var myModal = new bootstrap.Modal(document.getElementById('modal-add'));
                    myModal.hide();
                    $('#order_id').val('');
                    $('#order_trucking_id').val('');
                    $('#coa_id').val('');
                    $('#nama').val('');
                    $('#debit').val(0);
                    $('#credit').val(0);
                    alert('Data berhasil ditambahkan!');
                    getData();
                }
            });
        }

        getData();
    </script>
@endsection
