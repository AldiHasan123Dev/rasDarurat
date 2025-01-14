@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
@endsection
@section('content')
    <div class="container">
        <form action="" method="POST" class="row">
            @csrf
            <input type="hidden" name="nomor" id="nomor">
            <input type="hidden" name="no" id="no">
            <div class="col-12">
                <div class="card p-3 shadow">
                    <div class="row">
                        <div class="col-6">
                            <label for="tipe_jurnal">Tipe Jurnal</label>
                            <select name="tipe" required id="tipe" class="form-control">
                                <option value="">-</option>
                                <option value="JNL" data-no="{{ $no_1 }}" data-nomor="{{ $jno_1 }}">Jurnal Umum - {{ $jno_1 }}</option>
                                <option value="BBK" data-no="{{ $no_2 }}" data-nomor="{{ $jno_2 }}">Bank Keluar - {{ $jno_2 }}</option>
                                <option value="BBM" data-no="{{ $no_3 }}" data-nomor="{{ $jno_3 }}">Bank Masuk - {{ $jno_3 }}</option>
                                <option value="BKK" data-no="{{ $no_4 }}" data-nomor="{{ $jno_4 }}">Kas Keluar - {{ $jno_4 }}</option>
                                <option value="BKM" data-no="{{ $no_5 }}" data-nomor="{{ $jno_5 }}">Kas Masuk - {{ $jno_5 }}</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="created_at">Tanggal Jurnal</label>
                            <input type="date" name="created_at" id="created_at" value="{{ date('Y-m-d') }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card p-3 shadow">
                    <div class="table-responsive">
                        <table class="table table-sm mt-3" style="white-space: nowrap; font-size:.7rem">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>COA</th>
                                    <th>ID Job</th>
                                    <th>Invoice</th>
                                    <th>Invoice Agen</th>
                                    <th>Invoice Vendor</th>
                                    <th>Invoice External</th>
                                    <th>No BG</th>
                                    <th>Cont</th>
                                    <th>Nopol</th>
                                    <th>Keterangan</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                            </thead>
                            <tbody id="tampungan-data">
                                @include('data.jurnal')
                            </tbody>
                            <tr>
                                <td colspan="9">
                                    <button type="button" onclick="addData()" class="btn btn-sm btn-success mt-3" id="btn-save">Terbitkan Jurnal</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
<script>
    let page = 1;
    function deleteData(id){
        if (confirm('are you sure?')) {
            $.ajax({
                type: "delete",
                url: "{{ route('jurnal.tampungan.destroy') }}",
                data: {
                    id:id,
                    _token:"{{ csrf_token() }}"
                },
                success: function (response) {
                    loadData();
                }
            });
        }
    }

    function addData(){
        if (confirm('are you sure?')) {
            $.ajax({
                type: "post",
                url: "{{ route('jurnal.tampungan.store') }}",
                data: {
                    nomor:$('#nomor').val(),
                    tipe:$('#tipe').val(),
                    no:$('#no').val(),
                    created_at:$('#created_at').val(),
                    _token:"{{ csrf_token() }}"
                },
                success: function (response) {
                    loadData();
                    alert(response.message);
                }
            });
        }
    }

    $('#tipe').change(function () {
        let nomor = $(this).find(':selected').data('nomor');
        let no = $(this).find(':selected').data('no');
        $('#nomor').val(nomor);
        $('#no').val(no);
    });

    function loadData(){
        page++;
        var url =  '?page=' + page;
        $.ajax({
            url: url,
            type: "get",
        })
        .done(function(data){
            if(data.html == " "){
                $('#load-more').hide();
                return;
            }
            $("#tampungan-data").html(data.html);
        })
        .fail(function(jqXHR, ajaxOptions, thrownError){
            alert('server not responding...');
        });
    }

    loadData();
</script>
@endsection
