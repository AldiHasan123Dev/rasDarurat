<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
</div>
<div>
    <div class="col-12">
        <h4>FORM JURNAL TRUCKING</h4>
    </div>
    <div class="col-8">
        <div class="card p-3">
            <div class="row">
                <div class="mb-2 col-8">
                    <label>Template Jurnal</label>
                    <select class="form-select" id="template_id" wire:model="template_id"
                        style="font-size:.9rem !important">
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
    <div class="col-12 mt-2">
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-sm table-bordered" style="font-size:.7rem; white-space:nowrap">
                    <thead>
                        <tr>
                            <th>TGL MUAT</th>
                            <th>[1] ID JOB</th>
                            <th>[2] Cont (XPDC)</th>
                            <th>[3] Seal (XPDC)</th>
                            <th>[4] Kapal (XPDC)</th>
                            <th>[5] Voyage (XPDC)</th>
                            <th>[6] Shipment (XPDC)</th>
                            <th>[7] Pembayar (XPDC)</th>
                            <th>[8] Customer (TRUCKING)</th>
                            <th>[9] Shipment (TRUCKING)</th>
                            <th>[10] Tujuan (TRUCKING)</th>
                        </tr>
                    </thead>
                    <tbody id="table-order">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="card p-2">
            <form action="{{ route('jurnal.trucking.store') }}" method="post" class="row" id="form-submit">
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
                                    <input type="date" name="created_at" id="created_at" value="{{ date('Y-m-d') }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-4 mt-4 text-end">
                            <button class="btn btn-primary btn-sm mx-2" type="button" id="addBarisTemplate">Tambah
                                Baris Template</button>
                            <button class="btn btn-info btn-sm mx-2" type="button" onclick="addColumnDebit()">Tambah
                                Baris</button>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-sm" id="table-debit">
                            <tr>
                                <th>#</th>
                                <th style="width: 300px">CONT/SEAL</th>
                                <th style="width: 300px">Akun Debet</th>
                                <th style="width: 300px">Akun Credit</th>
                                <th>Keterangan</th>
                                <th>Nominal</th>
                                {{-- <th>Invoice External</th> --}}
                                <th style="width: 300px">Pilih Doc</th>
                                <th hidden id="dynamic-th" style="width: 300px">Nomor</th>
                                {{-- <th>Relasi</th> --}}
                            </tr>
                            @for ($i = 0; $i < $debit_idx; $i++)
                                <tr class="init-table">
                                    <td><input type="checkbox" name="id[]"
                                            onchange="uncheck(this,{{ $i }})" checked
                                            id="{{ $i }}" value="{{ $i }}"></td>
                                    <td>
                                        <select class="form-control select2" id="job-{{ $i }}"
                                            onchange="getOrder()" name="order_id[]"
                                            style="font-size:.9rem !important; width:170px">
                                            <option value=""></option>
                                            @foreach ($orders as $item)
                                                <option value="{{ $item->id }}">{{ $item->container }} -
                                                    {{ $item->seal }} - {{ $item->invoice }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" onchange="total()"
                                            id="debit-{{ $i }}" name="debit_coa_id[]"
                                            style="font-size:.9rem !important; width:170px">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                                <option value="{{ $item->id }}">{{ $item->kode }} -
                                                    {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control select2" onchange="total()"
                                            id="credit-{{ $i }}" name="credit_coa_id[]"
                                            style="font-size:.9rem !important; width:170px">
                                            <option value=""></option>
                                            @foreach ($coa as $item)
                                                <option value="{{ $item->id }}">{{ $item->kode }} -
                                                    {{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="width: 250px"><input name="name[]"
                                            id="keterangan-{{ $i }}" style="width: 300px" type="text">
                                    </td>
                                    <td><input type="number" name="amount[]" onkeyup="total()"
                                            id="amount-{{ $i }}"></td>
                                    {{-- <td><input type="text" name="invoice_external[]"
                                            id="invoice_external-{{ $i }}"></td> --}}
                                    <td>
                                        <select class="form-control select2 tipe" id="doc-{{ $i }}"
                                            style="font-size:.9rem !important; width:170px"
                                            onchange="updateDynamicColumn(this, {{ $i }})">
                                            <option value=""></option>
                                            <option value="truck">Inv Truck</option>
                                            <option value="vendor_truck">Inv Vendor Truck</option>
                                            <option value="lain-lain">Inv Lainnya</option>
                                            <option value="relasi">Relasi</option>
                                        </select>
                                        <input type="text" hidden name="invoice_external[]"
                                            value="" id="invoice_external1-{{ $i }}">
                                        <input type="text" hidden name="invoice_trcuking[]"
                                            value="" id="invoice_trucking1-{{ $i }}">
                                            <input type="text" hidden name="invoice_vendor[]"
                                            value="" id="invoice_vendor1-{{ $i }}">
                                            <input type="text" hidden name="relasi[]"
                                            value="" id="relasi1-{{ $i }}">
                                    </td>
                                    <td class="dynamic-column" hidden id="dynamic-column-{{ $i }}"></td>
                                </tr>
                            @endfor
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
                    <button type="button" class="btn btn-success btn-sm w-50 mx-2 btn-save"
                        onclick="simpan_jurnal('jurnal')">Simpan Jurnal</button>
                    <button type="button" class="btn btn-warning btn-sm w-50 mx-2 btn-save"
                        onclick="simpan_jurnal('tampungan')">Simpan Tampungan</button>
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
            const invx = document.getElementById(`invoice_external1-${rowId}`);
            const invTruck = document.getElementById(`invoice_trucking1-${rowId}`);
            const relasi = document.getElementById(`relasi1-${rowId}`);
            const invVendor = document.getElementById(`invoice_vendor1-${rowId}`);
            const dynamicColumn = document.getElementById(`dynamic-column-${rowId}`);
            const dynamicTh = document.getElementById("dynamic-th");
            dynamicColumn.innerHTML = ""; // Kosongkan kolom dinamis sebelumnya

            if (selectedValue === "truck") {
                dynamicTh.removeAttribute('hidden'); 
                invTruck.remove();
                dynamicColumn.removeAttribute('hidden'); // Tampilkan kolom dinamis
                dynamicColumn.innerHTML = `
        <td>
            <select class="form-control select3" onchange="total()"
                id="invoice-${rowId}" name="invoice_trucking[]"
                style="font-size:.9rem !important; width:170px">
                <option value="" selected>Pilih Inv Trucking</option>
                @foreach ($inv_trucking as $item)
                    <option value="{{ $item->id }}">
    {{ $item->invoice }} | {{ $item->container }}
</option>

                @endforeach
            </select>
        </td>
        `;
                $(dynamicColumn.querySelector('.select3')).select2();



            } else if (selectedValue === "vendor_truck") {
                dynamicTh.removeAttribute('hidden'); 
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

            } else if (selectedValue === "relasi") {
                dynamicTh.removeAttribute('hidden'); // Tampilkan <th>
                relasi.remove();
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
        let check_order_id = [];
        let arr_id = [];
        $('.select2').select2();
        $('#reset').click(function(e) {
            location.reload();
        });
        $('#template_id').click(function(e) {
            setTimeout(() => {
                $('.select2').select2();
            }, 2000);
        });

        function simpan_jurnal(name) {
            $('#simpan').val(name);
            if (total_debit != total_credit) {
                alert('Jurnal Tidak Balance debit = ' + total_debit + ' & credit = ' + total_credit + ' ! Harap check lagi')
            } else {
                if ($('#tipe').val()) {
                    if (confirm('are you sure')) {
                        $.ajax({
                            type: "POST",
                            url: "{{ url('api/jurnal/check-omset-trucking') }}",
                            data: {
                                order_id: arr_id
                            },
                            success: function(response) {
                                if (response.status == 1) {
                                    alert(response.message);
                                } else {
                                    $('#form-submit').submit();
                                }
                            }
                        })
                    }
                } else {
                    alert('Harap pilih tipe jurnal');
                }
            }
        }

        function addColumnDebit() {
            var amounts = $("input[name='amount[]']").map(function() {
                return $(this).val();
            }).get();
            debit = amounts.length + 1;
            let html = `<tr>
                        <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                        <td>
                            <select class="form-control select2" id="job-${debit}" onchange="getOrder()" name="order_id[]" style="font-size:.9rem !important; width:170px">
                                <option value=""></option>
                                @foreach ($orders as $item)
                                <option value="{{ $item->id }}">{{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }}</option>
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
                            <select class="form-control select2 tipe"
                            style="font-size:.9rem !important; width:170px"
                            id="doc-${debit}" onchange="updateDynamicColumn(this, ${debit})">
                            <option value=""></option>
                            <option value="truck">Inv Truck</option>
                            <option value="vendor_truck">Inv Vendor Truck</option>
                            <option value="lain-lain">Inv Lainnya</option>
                            <option value="relasi">Relasi</option>
                            </select>
                            <input type="text" hidden name="invoice_external[]"
                                            value="" id="invoice_external1-${debit}">
                            <input type="text" hidden name="invoice_trucking[]"
                                            value="" id="invoice_trucking1-${debit}">
                                             <input type="text" hidden name="invoice_vendor[]"
                                            value="" id="invoice_vendor1-${debit}">
                                            <input type="text" hidden name="relasi[]"
                                            value="" id="relasi1-${debit}">
                        </td>
                        <td class="dynamic-column" hidden id="dynamic-column-${debit}"></td>
                    </tr>`;
            $('#table-debit').append(html);
            setTimeout(() => {
                $('.select2').select2();
            }, 1000);
            debit++;
        }

        function getOrder() {
            var order_id = $("select[name='order_id[]']").map(function() {
                let val = $(this).val();
                if (val == "") {
                    val = '0';
                }
                return val;
            }).get();
            console.log(order_id);

            $.ajax({
                type: "POST",
                url: "{{ url('api/get-array-id-trucking') }}",
                data: {
                    id: order_id
                },
                success: function(response) {
                    let html = '';
                    $.each(response, function(idx, item) {
                        html +=
                            `
                    <tr>
                        <td>${item.tgl_muat}</td>
                        <td>${item.job}</td>
                        <td>${item.container}</td>
                        <td>${item.seal}</td>
                        <td>${item.kapal}</td>
                        <td>${item.voyage}</td>
                        <td>${item.shipment}</td>
                        <td>${item.pembayar}</td>
                        <td>${item.customer}</td>
                        <td>${item.tipe}</td>
                        <td>${item.tujuan}</td>
                    </tr>
                    `
                    });

                    $('#table-order').html(html);
                    $('#select')
                }
            });
        }

        function uncheck(e, id) {
            if ($('#' + id).is(":checked")) {
                $('#job-' + id).attr('disabled', false);
                $('#debit-' + id).attr('disabled', false);
                $('#credit-' + id).attr('disabled', false);
                $('#keterangan-' + id).attr('disabled', false)
                $('#amount-' + id).attr('disabled', false)
                $('#doc-' + id).attr('disabled', false)
                $('#dynamic-column' + id).attr('disabled', false)
                $('#invoice_external1-' + id).attr('disabled', false)
                $('#invoice_trucking1-' + id).attr('disabled', false)
                $('#invoice_vendor1-' + id).attr('disabled', false)
                $('#relasi1-' + id).attr('disabled', false)
            } else {
                $('#job-' + id).attr('disabled', true);
                $('#debit-' + id).attr('disabled', true);
                $('#credit-' + id).attr('disabled', true);
                $('#keterangan-' + id).attr('disabled', true)
                $('#amount-' + id).attr('disabled', true)
                $('#doc-' + id).attr('disabled', true)
                $('#dynamic-column-' + id).attr('disabled', true)
                $('#invoice_external1-' + id).attr('disabled', true)
                $('#invoice_trucking1-' + id).attr('disabled', true)
                $('#invoice_vendor1-' + id).attr('disabled', true)
                $('#relasi1-' + id).attr('disabled', true)
            }
            total();
        }

        function total() {
            var check = $("input[name='id[]']").map(function() {
                if ($(this).is(":checked")) {
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
            let c31 = @json($c31);
            arr_id = [];
            for (let i = 0; i < check.length; i++) {
                const item = check[i];
                var d = $('#debit-' + item).val();
                var c = $('#credit-' + item).val();
                var a = parseInt($('#amount-' + item).val());
                var or_id = $('#job-' + item).val();
                if (or_id == "") {
                    or_id = 0;
                } else if (d == c31) {
                    arr_id.push(or_id);
                } else {
                    arr_id.push(0);
                }
                if (d == c16 || d == c45 || d == c175) {
                    total_debit_prev += a;
                }
                if (c == c16 || c == c45 || c == c175) {
                    total_credit_prev += a;
                }
                if (d != "") {
                    total_debit += a;
                }
                if (c != "") {
                    total_credit += a;
                }
            }

            let voucher = total_debit_prev - total_credit_prev;
            if (voucher < 0) {
                voucher = voucher * -1;
            }
            $('#total_voucher').html('Rp. ' + voucher.toLocaleString('en-US'));
            $('#total_debit').html('Rp. ' + total_debit.toLocaleString('en-US'));
            $('#total_credit').html('Rp. ' + total_credit.toLocaleString('en-US'));
        }

        $('#apply').click(function(e) {
            $('.init-table').hide();
            addTemplate();
        });
        $('#addBarisTemplate').click(function(e) {
            addTemplate();
        });

        function addTemplate() {
            let id = $('#template_id').val();
            $.ajax({
                type: "get",
                url: "{{ url('admin/templatejurnal') }}" + "/" + id,
                success: function(response) {
                    $.each(response.items, function(idx, item) {
                        console.log(item);
                        var amounts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
                        debit = amounts.length + 1;
                        let html = '';
                        html += `<tr>
                       <td><input type="checkbox" name="id[]" onchange="uncheck(this,${debit})" checked id="${debit}" value="${debit}"></td>
                        <td>
                            <select class="form-control select2" id="job-${debit}" onchange="getOrder()" name="order_id[]" style="font-size:.9rem !important; width:170px">
                                <option value=""></option>
                                @foreach ($orders as $item)
                                <option value="{{ $item->id }}">{{ $item->container }} - {{ $item->seal }} - {{ $item->invoice }}</option>
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
                <select class="form-control select2 tipe"
                    style="font-size:.9rem !important; width:170px"
                    id="doc-${debit}" onchange="updateDynamicColumn(this, ${debit})">
                    <option value=""></option>
                    <option value="truck">Inv Truck</option>
                    <option value="vendor_truck">Inv Vendor Truck</option>
                    <option value="lain-lain">Inv Lainnya</option>
                    <option value="relasi">Relasi</option>
                </select>
                                            <input type="text" hidden name="invoice_external[]"
                                            value="" id="invoice_external1-${debit}">
                            <input type="text" hidden name="invoice_trucking[]"
                                            value="" id="invoice_trucking1-${debit}">
                                             <input type="text" hidden name="invoice_vendor[]"
                                            value="" id="invoice_vendor1-${debit}">
                                            <input type="text" hidden name="relasi[]"
                                            value="" id="relasi1-${debit}">
            </td>
                        <td class="dynamic-column" hidden id="dynamic-column-${debit}"></td>
                    </tr>`;
                        $('#table-debit').append(html);
                        $('#debit-' + debit).val(item.coa_debit_id);
                        $('#credit-' + debit).val(item.coa_credit_id);
                    });
                }
            });
            setTimeout(() => {
                $('.select2').select2();
            }, 2000);
        }
    </script>
@endpush
