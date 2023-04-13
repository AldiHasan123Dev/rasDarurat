@extends('layouts.iframe')
@section('style')
    <style>
        .autocomplete {
            position: relative;
            display: inline-block;
        }
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            /*position the autocomplete items to be the same width as the container:*/
            top: 100%;
            left: 0;
            right: 0;
        }
        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }
        .autocomplete-items div:hover {
            /*when hovering an item:*/
            background-color: #e9e9e9;
        }
        .autocomplete-active {
            /*when navigating through the items using the arrow keys:*/
            background-color: DodgerBlue !important;
            color: #ffffff;
        }
    </style>
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
<script src="{{ asset('assets/js/autocomplete.js') }}"></script>
<script>
    $(document).ready(function() {
        var barang = @json($barang);
        var customers = @json($customers);
        autocomplete(document.getElementById("selectBarang"), barang);
        autocomplete(document.getElementById("pengirim_id"), customers);
        autocomplete(document.getElementById("penerima_id"), customers);

        // $("select[name=penerima_bl_id]").select2(
        //     {
        //         ajax: {
        //             url: '/api/get-pengirim',
        //             data: function (params) {
        //                 return {
        //                     cari: params.term, // text pencarian
        //                     page: params.page
        //                 };
        //             },
        //             processResults: function (data, params) {
        //                 params.page = params.page || 1;
        //                 return {
        //                     results: data.items,
        //                     pagination: {
        //                         more: (params.page * 20) < data.counts
        //                     }
        //                 };
        //             },
        //             minimumInputLength: 2,
        //             delay: 400,
        //         }
        //     }
        // );

        $('select[name=penerima_bl_id]').select2();
        var penerima_bl_id = @json($order->penerima_bl_id);
        $('select[name=penerima_bl_id]').val(penerima_bl_id);
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
        $("select[name=agen_id]").select2();

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
                $("select[name=agen_id]").select2({
                    dropdownParent: $('#offcanvasOrder')
                });
            }else{
                $('#nag').show();
                $('#ag').hide();
            }
        });

        $('#submit-edit').click(function (e) {
            $.ajax({
                type: "POST",
                url: "{{ route('api.customer.getCustomer') }}",
                data: {nama:[$('#pengirim_id').val(),$('#penerima_id').val()]},
                success: function (response) {
                    if (response==0) {
                        alert('Pengirim atau Penerima tidak ditemukan di data Customer! silahkan cek data lagi')
                    }else{
                        console.log('resr');
                        $('#edit-form').submit();
                    }
                }
            });
        });
</script>
@endsection
