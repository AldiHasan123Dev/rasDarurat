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
                    <form action="{{ route('jurnal.update', $data[0]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <span>EDIT JURNAL</span>
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <label for="tipe_jurnal">Nomor Jurnal</label>
                                <input type="text" name="nomor" id="nomor" class="form-control" disabled value="{{ $data[0]->nomor }}">
                            </div>
                            <div class="col-4">
                                <label for="created_at">Tanggal Jurnal</label>
                                <input type="date" name="created_at" id="created_at" value="{{ date('Y-m-d',strtotime($data[0]->created_at)) }}" class="form-control">
                            </div>
                            <div class="col-2">
                                <button class="btn btn-success btn-sm mx-2 mt-3" type="submit" onclick="return confirm('are you sure?')">Simpan Tanggal</button>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-info btn-sm mx-2 mt-3" type="button" onclick="addModal()">Tambah Baris</button>
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
                                    <th>COA</th>
                                    <th>Keterangan</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                            </thead>
                            <tbody id="data-body">

                            </tbody>
                        </table>
                    </div>
                    <table>
                        <tr>
                            <td style="width: 300px"><b>TOTAL DEBET</b></td>
                            <td><b id="total_debit">{{ number_format($data->sum('debit'),2,',','.') }}</b></td>
                        </tr>
                        <tr>
                            <td style="width: 300px"><b>TOTAL CREDIT</b></td>
                            <td><b id="total_credit">{{ number_format($data->sum('credit'),2,',','.') }}</b></td>
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
                        @if ($tipe=='xpdc')
                            <div class="col-12 mb-3">
                                <label for="order_id">JOB</label><br>
                                <select class="form-control" id="order_id" name="order_id" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @foreach ($orders as $item)
                                    <option value="{{ $item->id }}">{{ $item->job }}-{{ sprintf('%02d',$item->no_job) }} / {{ $item->seal }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if ($tipe=='trucking')
                            <div class="col-12 mb-3">
                                <label for="order_id">Trucking</label><br>
                                <select class="form-control" id="order_trucking_id" name="order_trucking_id" style="font-size:.9rem !important">
                                    <option value=""></option>
                                    @foreach ($orders as $item)
                                        <option value="{{ $item->id }}">{{ $item->container }} - {{ $item->seal }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
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
        $('.select2').select2();
        $('#coa_id').select2({
            dropdownParent: $('#modal-add'),
        });
        $('#order_id').select2({
            dropdownParent: $('#modal-add'),
        });
        $('#order_trcuking_id').select2({
            dropdownParent: $('#modal-add'),
        });
        var total_credit = 0;
        var total_debit = 0;
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
                                            @foreach ($orders as $item)
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
                                        @foreach ($orders as $item)
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

        function editModal(url){
            var myModal = new bootstrap.Modal(document.getElementById('modal-edit'));
            $('#iframe-edit').attr('src',url);
            myModal.show();
        }

        var modalBTTB = document.getElementById('modal-edit')
        modalBTTB.addEventListener('hidden.bs.modal', function (event) {
            getData()
        })

        function addModal(url){
            var myModal = new bootstrap.Modal(document.getElementById('modal-add'));
            myModal.show();
        }

        function deleteData(id){
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
            $.ajax({
                type: "POST",
                url: "{{ url('api/get-jurnal') }}",
                data: {nomor:@json($jur->nomor)},
                success: function (response) {
                    let html = '';
                    $.each(response, function (idx, item) {
                        html += `<tr>
                                        <td>
                                            <div class="d-flex">
                                                <button onclick="deleteData(${ item.id })" type="button" style="border:none; background: transparent; color:red"><i class="fas fa-trash"></i></button>
                                                <button onclick="editModal('jurnal-edit-${item.id}')" type="button" style="border:none; background: transparent; color:rgb(41, 51, 226)"><i class="fas fa-pencil"></i></button>
                                            </div>
                                        </td>
                                        <td>${item.id}</td>
                                        <td>${item.job}</td>
                                        <td>${item.no_job}</td>
                                        <td>${item.container}</td>
                                        <td>${item.coa_nama} - ${item.coa_kode}</td>
                                        <td>${item.nama}</td>
                                        <td>${item.debit}</td>
                                        <td>${item.credit}</td>
                                    </tr>`;
                    });

                    $('#data-body').html(html);
                }
            });
        }

        function save(){
            var data = {
                order_id:$('#order_id').val(),
                order_trucking_id:$('#order_trucking_id').val(),
                coa_id:$('#coa_id').val(),
                nama:$('#nama').val(),
                debit:$('#debit').val(),
                credit:$('#credit').val(),
                nomor:@json($jur->nomor),
                created_at:@json(date('Y-m-d',strtotime($jur->created_at))),
                no:@json($jur->no),
                tipe:@json($jur->tipe),
            };
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
