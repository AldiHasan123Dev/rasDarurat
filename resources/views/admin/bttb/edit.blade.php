@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-8">
            <div class="card p-3 shadow">
                <form action="{{ route('bttb.update',$bttb) }}" method="post">
                    @csrf
                    @method('PUT')
                    @include('admin.bttb.form')
                    <div class="my-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        $("select[name=satuan_id]").select2({
            tags:true
        });
        $("select[name=barang_id]").select2({
            tags:true
        });
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
        $("select[name=pengirim_id]").val(@json($bttb->pengirim_id)).trigger('change');
        function hitungVol(){
            var p = $('#p').val();
            var l = $('#l').val();
            var t = $('#t').val();
            var vol = $('#vol').val();
            if(p>0&&l>0&&t>0){
                vol = (p*l*t)/1000000
            }
            $('#vol').val(vol);
        }

        $('#p').keyup(function (e) {
            hitungVol()
        });
        $('#l').keyup(function (e) {
            hitungVol()
        });
        $('#t').keyup(function (e) {
            hitungVol()
        });
    </script>
@endsection
