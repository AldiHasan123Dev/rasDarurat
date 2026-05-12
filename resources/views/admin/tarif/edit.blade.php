@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-8">
                <div class="card p-4 shadow">
                    <form action="{{ route('tarif.update',$tarif) }}" method="post">
                        @csrf
                        @method('PUT')
                        @include('admin.tarif.form')
                        <div class="mt-2">
                            <button type="submit"  class="btn btn-success btn-sm">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $("select[name=customer_id]").select2(
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

        $("select[name=dari]").select2({
            tags:true
        });
        $("select[name=tujuan]").select2({
            tags:true
        });
        $("select[name=satuan_inv]").select2({
            tags:true
        });
        $('#shipment').change(function (e) {
            var text = $(this).find(":selected").text();
            var val = text.substr(0,3);
            if (val=='FCL'||val=='fcl') {
                $('#satuan').val(1);
            } else {
                $('#satuan').val(2);
            }
        });
    </script>
@endsection
