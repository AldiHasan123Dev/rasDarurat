@extends('layouts.iframe')
@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/awesomplete.css') }}">
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-4 shadow">
                    <form action="{{ route('order.update',$order) }}" method="post" id="edit-form">
                        @csrf
                        @method('PUT')
                        @include('admin.order.form')
                        <div class="my-3">
                            <button type="button" class="btn btn-sm btn-success" id="submit-edit" onclick="return confirm('are you sure?')">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ asset('assets/js/awesomplete.js') }}"></script>
<script>
    let customers = @json($customers);
    let barang = @json($barang);
    let agent = @json($agent);
    new Awesomplete(document.getElementById("pengirim_id"), {
        list: customers,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("penerima_id"), {
        list: customers,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("penerima_bl_id"), {
        list: customers,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("barang_id"), {
        list: barang,
        minChars: 3,
        maxItems: 5
    });
    new Awesomplete(document.getElementById("agen_id"), {
        list: agent,
        minChars: 3,
        maxItems: 5
    });
</script>

<script>
    var tarif_id = @json($order->tarif_id);
    var jadwal_kapal_id = @json($order->jadwal_kapal_id);
    $.ajax({
        type: "POST",
        url: "{{ route('api.tarif.getOne') }}",
        data: {id:tarif_id},
        success: function (response) {
            let data = response;
            let tarif = data.tarif;
            $('#tarif').val('Rp. '+tarif.toLocaleString('en-US'));
            $('#dari').val(data.dari);
            $('#tujuan').val(data.tujuan);
            $('#shipment').val(data.shipment);
            $('#kondisi').val(data.kondisi);
            $('#satuan').val(data.satuan);
            $.ajax({
                type: "GET",
                url: "/api/get-jadwal-kapal-pelayaran/"+tarif_id,
                success: function (response) {
                    var data = response;
                    var html = '<option>Pilih Kapal</option>';
                    $.each(data, function (id, name) {
                        if (id==jadwal_kapal_id) {
                            html += '<option value="'+id+'" selected>'+name+'</option>'
                        } else {
                            html += '<option value="'+id+'">'+name+'</option>'
                        }
                    });
                    $('select[name=jadwal_kapal_id]').html(html);
                }
            });
        }
    });
        $("select[name=tarif_id]").select2();

        $("select[name=tarif_id]").change(function (e) {
            var val = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('api.tarif.getOne') }}",
                data: {id:val},
                success: function (response) {
                    let data = response;
                    let tarif = data.tarif;
                    $('#tarif').val('Rp. '+tarif.toLocaleString('en-US'));
                    $('#dari').val(data.dari);
                    $('#tujuan').val(data.tujuan);
                    $('#shipment').val(data.shipment);
                    $('#kondisi').val(data.kondisi);
                    $('#satuan').val(data.satuan);
                }
            });
        });

        $('#tarif_id').change(function (e) {
            var val = $(this).val();
            $.ajax({
                type: "GET",
                url: "/api/get-jadwal-kapal-pelayaran/"+val,
                success: function (response) {
                    var data = response;
                    var html = '<option>Pilih Kapal</option>';
                    $.each(data, function (id, name) {
                        html += '<option value="'+id+'">'+name+'</option>'
                    });
                    $('select[name=jadwal_kapal_id]').html(html);
                }
            });
        });

        if (@json($order->agen=='AGEN')) {
            $('#nag').hide();
        }else{
            $('#ag').hide();
        }
        $('#agen').change(function (e) {
            var val = $(this).val();
            if (val=='AGEN') {
                $('#ag').show();
                $('#nag').hide();
            }else{
                $('#nag').show();
                $('#ag').hide();
            }
        });

        $('#submit-edit').click(function (e) {
            $('#edit-form').submit();
        });
</script>
@endsection
