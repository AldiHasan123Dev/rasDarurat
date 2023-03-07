@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card p-4 shadow">
                    <form action="{{ route('order.update',$order) }}" method="post">
                        @csrf
                        @method('PUT')
                        @include('admin.order.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('select[name=pengirim_id]').select2(
            {
                ajax: {
                    url: '/api/get-pengirim',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );
    });
    $(document).ready(function() {
        $("select[name=penerima_id]").select2(
            {
                ajax: {
                    url: '/api/get-pengirim',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );
    });
    $(document).ready(function() {
        $("select[name=penerima_bl_id]").select2(
            {
                ajax: {
                    url: '/api/get-pengirim',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );
    });
    $(document).ready(function() {
        $("select[name=barang_id]").select2(
            {
                ajax: {
                    url: '/api/get-barang',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                },
                tags:true
            }
        );
    });
</script>

<script>
    function renderSelect2(id){
        return
        $('#selectPengirim').select2(
            {
                ajax: {
                    url: '/api/get-pengirim',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );

        $("select[name=penerima_id]").select2(
            {
                ajax: {
                    url: '/api/get-pengirim',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                }
            }
        );

        $("select[name=barang_id]").select2(
            {
                ajax: {
                    url: '/api/get-barang',
                    data: function (params) {
                        return {
                            cari: params.term, // text pencarian
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 20) < data.counts
                            }
                        };
                    },
                    minimumInputLength: 2,
                    delay: 400,
                },
                tags:true
            }
        );
    }
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
        $("select[name=satuan]").select2({
            tags:true
        });
        // $("select[name=pengirim_id]").select2({
        //     dropdownParent: $('#offcanvasOrder')
        // });
        // $("select[name=penerima_id]").select2({
        //     dropdownParent: $('#offcanvasOrder')
        // });
        // $("select[name=barang_id]").select2({
        //     dropdownParent: $('#offcanvasOrder'),
        //     tags:true
        // });

        $("select[name=pengirim_id]").select2({
            dropdownParent: $('#offcanvasBTTB')
        });
        $("select[name=satuan_id]").select2({
            dropdownParent: $('#offcanvasBTTB'),
            tags:true
        });
        $("select[name=barang_id]").select2({
            dropdownParent: $('#offcanvasBTTB'),
            tags:true
        });

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
</script>
@endsection
