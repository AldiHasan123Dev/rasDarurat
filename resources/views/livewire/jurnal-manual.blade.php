<div>
    <div class="col-12">
        <h4>FORM JURNAL MANUAL</h4>
    </div>
    <div class="col-8">
        <div class="card p-3">
            <div class="row">
                <div class="mb-2 col-8">
                    <label>Template Jurnal</label>
                    <select class="form-select" id="template_id" style="font-size:.9rem !important">
                        <option value=""></option>
                        @foreach ($templates as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 col-4">
                    <div class="btn-group">
                        <button class="btn btn-success btn-sm w-100 mt-3" id="apply">Terapkan</button>
                        <button class="btn btn-warning btn-sm w-100 mt-3" id="reset">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card p-2">
            <form action="{{ route('jurnal.manual.store') }}" method="post" class="row" id="form-submit">
                @csrf
                <input type="hidden" name="order_id" id="order_id" value="{{ $order }}">
                <input type="hidden" name="jurnal_id" id="jurnal_id" value="{{ json_encode($jurnal_id) }}">
                <div class="col-12">
                    <div class="row">
                        <div class="col-8">
                            <div class="row">
                                <div class="col-6">
                                    <label for="tipe_jurnal">Tipe Jurnal</label>
                                    <select name="tipe" required id="tipe" class="form-select">
                                        <option value="">-</option>
                                        <option value="JNL">Jurnal Umum - {{ $no_1 }}</option>
                                        <option value="BBK">Bank Keluar - {{ $no_2 }}</option>
                                        <option value="BBM">Bank Masuk - {{ $no_3 }}</option>
                                        <option value="BKK">Kas Keluar - {{ $no_4 }}</option>
                                        <option value="BKM">Kas Masuk - {{ $no_5 }}</option>
                                        <option value="BBKT">Bank Keluar Trucking - {{ $no_6 }}</option>
                                        <option value="BBMT">Bank Masuk Trucking - {{ $no_7 }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="created_at">Tanggal Jurnal</label>
                                    <input type="date" name="created_at" id="created_at" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-4 mt-4 text-end">
                            <button class="btn btn-primary btn-sm mx-2" type="button" id="addBarisTemplate">Tambah Baris Template</button>
                            <button class="btn btn-info btn-sm mx-2" type="button" onclick="addColumnDebit()">Tambah Baris</button>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-sm" id="table-debit">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nopol</th>
                                    <th>Akun Debet</th>
                                    <th>Akun Credit</th>
                                    <th>Keterangan</th>
                                    <th>Nominal</th>
                                    <th style="width: 300px">Pilih Doc</th>
                                    <th hidden id="dynamic-th" style="width: 300px">Nomor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < $debit_idx; $i++)
                                <tr class="init-table">
                                    <td>
                                        <input type="checkbox" name="id[]" onchange="uncheck(this, {{ $i }})" checked id="{{ $i }}" value="{{ $i }}">
                                    </td>
                                    <td>
                                        <select class="form-control select2" id="nopol-{{ $i }}" name="nopol[]" style="font-size: 0.9rem !important; width: 150px;">
                                            <option value=""></option>
                                            @foreach ($kendaraan as $item)
                                                <option value="{{ $item->nopol }}">{{ $item->nopol }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" onchange="total()" id="debit-{{ $i }}" name="debit_coa_id[]" style="font-size: 0.9rem !important; width: 170px;">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" onchange="total()" id="credit-{{ $i }}" name="credit_coa_id[]" style="font-size: 0.9rem !important; width: 170px;">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 250px;">
                                        <input name="name[]" id="keterangan-{{ $i }}" style="width: 300px;" type="text">
                                    </td>
                                    <td>
                                        <input type="number" name="amount[]" onkeyup="total()" id="amount-{{ $i }}">
                                    </td>
                                    <td>
                                        <select class="form-control select2 tipe" id="doc-{{ $i }}" style="font-size: 0.9rem !important; width: 170px;" onchange="updateDynamicColumn(this, {{ $i }})">
                                            <option value=""></option>
                                            <option value="truck">Inv Truck</option>
                                            <option value="vendor_truck">Inv Vendor Truck</option>
                                            <option value="expdc">Inv Xpdc</option>
                                            <option value="agen">Inv Agen</option>
                                            <option value="pelayaran">BG Pelayaran</option>
                                            <option value="lain-lain">Inv Lainnya</option>
                                            <option value="relasi">Relasi</option>
                                        </select>
                                        <input type="text" hidden name="invoice_external[]"
                                        value="" id="invoice_external1-{{ $i }}">
                                    <input type="text" hidden name="invoice[]"
                                        value="" id="invoice1-{{ $i }}">
                                        <input type="text" hidden name="invoice_agen[]"
                                        value="" id="invoice_agen1-{{ $i }}">
                                    <input type="text" hidden name="no_bg[]"
                                        value="" id="no_bg1-{{ $i }}">
                                        <input type="text" hidden name="relasi[]"
                                        value="" id="relasi1-{{ $i }}">
                                        <input type="text" hidden name="invoice_trcuking[]"
                                        value="" id="invoice_trucking1-{{ $i }}">
                                        <input type="text" hidden name="invoice_vendor[]"
                                        value="" id="invoice_vendor1-{{ $i }}">
                                    </td>
                                    <td class="dynamic-column" hidden id="dynamic-column-{{ $i }}"></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>                    
                    <table>
                        <tr>
                            <td style="width: 300px"><b>TOTAL DEBET</b></td>
                            <td><b id="total_debit"></b></td>
                        </tr>
                        <tr>
                            <td style="width: 300px"><b>TOTAL CREDIT</b></td>
                            <td><b id="total_credit"></b></td>
                        </tr>
                        <tr class="border border-top-md">
                            <td class="text-secondary" style="width: 300px"><span>CHECK VOUCHER</span></td>
                            <td class="text-secondary"><span id="total_voucher"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="btn-group mt-3">
                    <input type="hidden" name="simpan" id="simpan">
                    <button type="button" class="btn btn-success btn-sm w-50 mx-2 btn-save" onclick="simpan_jurnal('jurnal')">Simpan Jurnal</button>
                    <button type="button" class="btn btn-warning btn-sm w-50 mx-2 btn-save" onclick="simpan_jurnal('tampungan')">Simpan Tampungan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/js/selectize.js') }}"></script>
<script>
    function updateDynamicColumn(select, rowId) {
            const selectedValue = select.value; 
            const invVendor = document.getElementById(`invoice_vendor1-${rowId}`);
            const invTruck = document.getElementById(`invoice_trucking1-${rowId}`);
            const invx = document.getElementById(`invoice_external1-${rowId}`);
            const inv = document.getElementById(`invoice1-${rowId}`);
            const inv_agen = document.getElementById(`invoice_agen1-${rowId}`);
            const noBg = document.getElementById(`no_bg1-${rowId}`);
            const relasi = document.getElementById(`relasi1-${rowId}`);
            const dynamicColumn = document.getElementById(`dynamic-column-${rowId}`);
            const dynamicTh = document.getElementById("dynamic-th");
            dynamicColumn.innerHTML = ""; // Kosongkan kolom dinamis sebelumnya

            if (selectedValue === "truck") {
                dynamicTh.removeAttribute('hidden'); // Tampilkan <th>
                invTruck.remove();
                dynamicColumn.removeAttribute('hidden'); // Tampilkan kolom dinamis
                dynamicColumn.innerHTML = `
        <td>
            <select class="form-control select3" onchange="total()"
                id="invoice-${rowId}" name="invoice_trucking[]"
                style="font-size:.9rem !important; width:170px">
                <option value="" selected>Pilih Inv Trucking</option>
                @foreach ($order_trucking as $item)
                    <option value="{{ $item->id }}">
    {{ $item->invoice }} | {{ $item->container }}
</option>

                @endforeach
            </select>
        </td>
        `;
                $(dynamicColumn.querySelector('.select3')).select2();



            } else if (selectedValue === "vendor_truck") {
                dynamicTh.removeAttribute('hidden'); // Tampilkan <th>
                invVendor.remove();
                dynamicColumn.removeAttribute('hidden'); // Tampilkan kolom dinamis
                dynamicColumn.innerHTML = `
        <td>
             <select class="form-control select3" onchange="total()"
                id="invoice-${rowId}" name="invoice_vendor[]"
                style="font-size:.9rem !important; width:170px">
                <option value="" selected>Pilih Inv Vendor Trucking</option>
                 @foreach ($inv_vendor as $item)
                    <option value="{{ $item->id }}">
    {{ $item->invoice }} | {{ $item->container }}
</option>

                @endforeach
            </select>
        </td>
        `;
                $(dynamicColumn.querySelector('.select3')).select2();

            } else if (selectedValue === "lain-lain") {
        dynamicTh.removeAttribute('hidden');
        invx.remove();
        dynamicColumn.removeAttribute('hidden');
        dynamicColumn.innerHTML = `
            <td style="width: 170px">
                <select class="form-control select3" id="lain-${rowId}" style="width: 170px" onchange="handleLainLainChange(this, ${rowId})">
                    <option value="">Pilih Opsi</option>
                    <option value="buat-baru">Buat Baru</option>
                    <option value="pilih-invoice-external">Pilih Invoice External</option>
                </select>
            </td>
        `;
        $(dynamicColumn.querySelector('.select3')).select2();

    } else if (selectedValue === "expdc") {
                dynamicTh.removeAttribute('hidden'); // Tampilkan <th>
                inv.remove();
                dynamicColumn.removeAttribute('hidden'); // Tampilkan kolom dinamis
                dynamicColumn.innerHTML = `
        <td>
            <select class="form-control select3" onchange="total()"
                id="invoice-${rowId}" name="invoice[]"
                style="font-size:.9rem !important; width:170px">
                <option value="" selected>Pilih Inv Expdc</option>
                @foreach ($orders as $item)
                    <option value="{{ $item->id }}">
    {{ $item->invoice }} | {{ $item->container }}
</option>

                @endforeach
            </select>
        </td>
        `;
                $(dynamicColumn.querySelector('.select3')).select2();



            } else if (selectedValue === "agen") {
                dynamicTh.removeAttribute('hidden'); // Tampilkan <th>
                inv_agen.remove();
                dynamicColumn.removeAttribute('hidden'); // Tampilkan kolom dinamis
                dynamicColumn.innerHTML = `
        <td>
            <select class="form-control select3" onchange="total()"
                id="invoice-${rowId}" name="invoice_agen[]"
                style="font-size:.9rem !important; width:170px">
                <option value="" selected>Pilih Inv Agen</option>
                @foreach ($agens as $item)
                    <option value="{{ $item->id }}">
    {{ $item->invoice_agen }} | {{ $item->container }}
</option>

                @endforeach
            </select>
        </td>
        `;
                $(dynamicColumn.querySelector('.select3')).select2();

            } else if (selectedValue === "pelayaran") {
                noBg.remove();
                dynamicTh.removeAttribute('hidden'); // Tampilkan <th>
                dynamicColumn.removeAttribute('hidden'); // Tampilkan kolom dinamis
                dynamicColumn.innerHTML = `
        <td>
                                        <select class="form-control select3" id="bg-{{ $i }}" name="no_bg[]"
                                            style="font-size:.9rem !important; width:170px">
                                            <option value="">Pilih No BG</option>
                                            @foreach ($bgs as $bg)
                                                <option value="{{ $bg }}">{{ $bg }}</option>
                                            @endforeach
                                        </select>
        </td>
        `;
                $(dynamicColumn.querySelector('.select3')).select2();

            } else if (selectedValue === "relasi") {
                relasi.remove();
                dynamicTh.removeAttribute('hidden'); // Tampilkan <th>
                dynamicColumn.removeAttribute('hidden'); // Tampilkan kolom dinamis
                dynamicColumn.innerHTML = `
        <td>
            <select class="form-control select3" onchange="total()"
                id="relasi-${rowId}" name="relasi[]"
                style="font-size:.9rem !important; width:170px">
                <option value="" selected>Pilih No Relasi</option>
                @foreach ($relasi as $item)
                    <option value="{{ $item }}">{{ $item }}</option>
                @endforeach
            </select>
        </td>
        `;
                $(dynamicColumn.querySelector('.select3')).select2();
            } else {
                dynamicColumn.setAttribute('hidden', true);
            }
        }

        function handleLainLainChange(select, rowId) {
    const newValue = select.value;
    const dynamicColumn = document.getElementById(`dynamic-column-${rowId}`);
    dynamicColumn.innerHTML = ""; // Kosongkan kolom dinamis sebelumnya

    if (newValue === "buat-baru") {
        dynamicColumn.innerHTML = `
 <td><input name="invoice_external[]"
                                            id="invoice_external-${rowId}" style="width: 170px" type="text">
                                    </td>
        `;
    } else if (newValue === "pilih-invoice-external") {
        dynamicColumn.innerHTML = `
            <td>
                <select class="form-control select3" name="invoice_external[]" id="invoice_external-${rowId}" style="width: 170px">
                    <option value="">Pilih Invoice External</option>
                     @foreach ($invx as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </td>
        `;
        $(dynamicColumn.querySelector('.select3')).select2(); // Inisialisasi Select2
    }
}
    let total_debit = 0;
    let total_credit = 0;
    let credit = 2;
    let debit = 2;
    $('.select2').select2();
    $('#reset').click(function (e) {
        location.reload();
    });

    function simpan_jurnal(name){
        $('#simpan').val(name);
        if(total_debit!=total_credit){
            alert('Jurnal Tidak Balance debit = '+total_debit+' & credit = '+total_credit+' ! Harap check lagi')
        }else{
            if($('#tipe').val()){
                if(confirm('are you sure')){
                    $('#form-submit').submit();
                }
            }else{
                alert('Harap pilih tipe jurnal');
            }
        }
    }

    function addColumnDebit(){
        var amounts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
        debit = amounts.length + 1;
        let html = `<tr>
                        <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                        <td style="width: 150px">
                                <select class="form-control select2" id="nopol-${debit}" name="nopol[]" style="font-size:.9rem !important; width:150px">
                                    <option value=""></option>
                                    @foreach ($kendaraan as $item)
                                        <option value="{{ $item->nopol }}">{{ $item->nopol }}</option>
                                    @endforeach
                                </select>
                            </td>
                        <td>
                            <select class="form-control select2" onchange="total()" id="debit-${debit}" name="debit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control select2" onchange="total()" id="credit-${debit}" name="credit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                <option value=""></option>
                                @foreach ($coa as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 250px"><input name="name[]" id="keterangan-${debit}" style="width: 300px" type="text"></td>
                        <td><input type="number" name="amount[]" onkeyup="total()" id="amount-${debit}"></td>
                         <td>
                                        <select class="form-control select2 tipe" id="doc-${debit}" style="font-size: 0.9rem !important; width: 170px;" onchange="updateDynamicColumn(this, ${debit})">
                                            <option value=""></option>
                                            <option value="truck">Inv Truck</option>
                                            <option value="vendor_truck">Inv Vendor Truck</option>
                                            <option value="expdc">Inv Expdc</option>
                                            <option value="agen">Inv Agen</option>
                                            <option value="pelayaran">BG Pelayaran</option>
                                            <option value="lain-lain">Inv Lainnya</option>
                                            <option value="relasi">Relasi</option>
                                        </select>
                                        <input type="text" hidden name="invoice_external[]"
                                            value="" id="invoice_external1-${debit}">
                            <input type="text" hidden name="invoice[]"
                                            value="" id="invoice1-${debit}">
                                             <input type="text" hidden name="invoice_agen[]"
                                            value="" id="invoice_agen1-${debit}">
                                        <input type="text" hidden name="no_bg[]"
                                            value="" id="no_bg1-${debit}">
                                            <input type="text" hidden name="relasi[]"
                                            value="" id="relasi1-${debit}">
                                             <input type="text" hidden name="invoice_trucking[]"
                                            value="" id="invoice_trucking1-${debit}">
                                             <input type="text" hidden name="invoice_vendor[]"
                                            value="" id="invoice_vendor1-${debit}">
                                    </td>
                                    <td class="dynamic-column" hidden id="dynamic-column-${debit}"></td>
                    </tr>`;
        $('#table-debit').append(html);
        setTimeout(() => {
            $('.select2').select2();
        }, 1000);
        debit++;
    }

    function uncheck (e,id) {
        if($('#' + id).is(":checked")){
            $('#job-'+id).attr('disabled',false);
            $('#debit-'+id).attr('disabled',false);
            $('#credit-'+id).attr('disabled',false);
            $('#keterangan-'+id).attr('disabled',false)
            $('#amount-'+id).attr('disabled',false)
        }else{
            $('#job-'+id).attr('disabled',true);
            $('#debit-'+id).attr('disabled',true);
            $('#credit-'+id).attr('disabled',true);
            $('#keterangan-'+id).attr('disabled',true)
            $('#amount-'+id).attr('disabled',true)
        }
        total();
    }

    function total(){
        var check = $("input[name='id[]']").map(function(){
            if($(this).is(":checked")){
                return $(this).val();
            }
        }).get();
        total_credit = 0;
        total_debit = 0;
        let total_debit_prev = 0;
        let total_credit_prev = 0;
        let c16 = @json($c16);
        let c45 = @json($c45);
        let c175 = @json($c175);
        for (let i = 0; i < check.length; i++) {
            const item = check[i];
            var d = $('#debit-'+item).val();
            var c = $('#credit-'+item).val();
            var a = parseInt($('#amount-'+item).val());
            if(d==c16 || d==c45 || d==c175){
                total_debit_prev+=a;
            }
            if(c==c16 ||c==c45 || c==c175){
                total_credit_prev+=a;
            }
            if(d!=""){
                total_debit+=a;
            }
            if(c!=""){
                total_credit+=a;
            }
        }
        let voucher = total_debit_prev - total_credit_prev;
        if(voucher<0){
            voucher = voucher * -1;
        }
        $('#total_voucher').html('Rp. '+voucher.toLocaleString('en-US'));
        $('#total_debit').html('Rp. '+total_debit.toLocaleString('en-US'));
        $('#total_credit').html('Rp. '+total_credit.toLocaleString('en-US'));
    }

    $('#apply').click(function (e) {
        $('.init-table').hide();
        addTemplate();
    });

    $('#addBarisTemplate').click(function (e) {
        addTemplate();
    });

    function addTemplate(){
        let id = $('#template_id').val();
        $.ajax({
            type: "get",
            url: "{{ url('admin/templatejurnal') }}"+"/"+id,
            success: function (response) {
                $.each(response.items, function (idx, item) {
                    var amounts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
                    debit = amounts.length + 1;
                    let html = '';
                    html += `<tr class="init-table">
                            <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                            <td style="width: 150px">
                                <select class="form-control select2" id="nopol-${debit}" name="nopol[]" style="font-size:.9rem !important; width:150px">
                                    <option value=""></option>
                                    @foreach ($kendaraan as $item)
                                        <option value="{{ $item->nopol }}">{{ $item->nopol }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control select2" onchange="total()" id="debit-${debit}" name="debit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control select2" onchange="total()" id="credit-${debit}" name="credit_coa_id[]" style="font-size:.9rem !important; width:170px">
                                    <option value=""></option>
                                    @foreach ($coa as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 250px"><input name="name[]" value="${item.keterangan}" id="keterangan-${debit}" style="width: 300px" type="text"></td>
                            <td><input type="number" name="amount[]" onkeyup="total()" id="amount-${debit}"></td>
                         <td>
                                        <select class="form-control select2 tipe" id="doc-${debit}" style="font-size: 0.9rem !important; width: 170px;" onchange="updateDynamicColumn(this, ${debit})">
                                            <option value=""></option>
                                            <option value="truck">Inv Truck</option>
                                            <option value="vendor_truck">Inv Vendor Truck</option>
                                            <option value="expdc">Inv Expdc</option>
                                            <option value="agen">Inv Agen</option>
                                            <option value="pelayaran">BG Pelayaran</option>
                                            <option value="lain-lain">Inv Lainnya</option>
                                            <option value="relasi">Relasi</option>
                                        </select>
                                         <input type="text" hidden name="invoice_external[]"
                                            value="" id="invoice_external1-${debit}">
                            <input type="text" hidden name="invoice[]"
                                            value="" id="invoice1-${debit}">
                                             <input type="text" hidden name="invoice_agen[]"
                                            value="" id="invoice_agen1-${debit}">
                                        <input type="text" hidden name="no_bg[]"
                                            value="" id="no_bg1-${debit}">
                                            <input type="text" hidden name="relasi[]"
                                            value="" id="relasi1-${debit}">
                                             <input type="text" hidden name="invoice_trucking[]"
                                            value="" id="invoice_trucking1-${debit}">
                                             <input type="text" hidden name="invoice_vendor[]"
                                            value="" id="invoice_vendor1-${debit}">
                                    </td>
                                    <td class="dynamic-column" hidden id="dynamic-column-${debit}"></td>
                        </tr>`;
                    $('#table-debit').append(html);
                    $('#debit-'+debit).val(item.coa_debit_id);
                    $('#credit-'+debit).val(item.coa_credit_id);
                });
            }
        });
        setTimeout(() => {
            $('.select2').select2();
        }, 2000);
    }
</script>
@endpush
