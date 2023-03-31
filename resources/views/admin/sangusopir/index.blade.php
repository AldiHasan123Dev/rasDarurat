@extends('layouts.admin')
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
        .dataTables_scrollBody > table > thead > tr {
            visibility: collapse;
            height: 0px !important;
        }
    </style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSanguSopir" aria-controls="offcanvasSanguSopir">Tambah Sangu Sopir</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Tanggal</th>
                                <th>Tujuan</th>
                                <th>Sangu Total 20'</th>
                                <th>Sangu 20'</th>
                                <th>Sangu Total 40'</th>
                                <th>Sangu 40'</th>
                                <th>Sangu Total Combo 2x20</th>
                                <th>Sangu Combo 2x20</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasSanguSopir" aria-labelledby="offcanvasSanguSopirLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSanguSopirLabel">Form Sangu Sopir</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('sangusopir.store') }}" method="post">
                @csrf
                @include('admin.sangusopir.form',['sangusopir'=>[]])
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('assets/js/autocomplete.js')}}"></script>
<script>
    $(function() {
        var lokasi = @json($lokasi);
        autocomplete(document.querySelector("[name='tujuan']"), lokasi);
    });
</script>
    <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            scrollY: '50vh',
            ajax:{
                url: '{{ route('sangusopir.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id', visible:false },
                { data: 'created_at', name: 'created_at' },
                { data: 'tujuan', name: 'tujuan' },
                { data: 'ukuran_20', name: 'ukuran_20' },
                { data: 'sangu_20', name: 'sangu_20' },
                { data: 'ukuran_40', name: 'ukuran_40' },
                { data: 'sangu_40', name: 'sangu_40' },
                { data: 'ukuran_combo', name: 'ukuran_combo' },
                { data: 'sangu_combo', name: 'sangu_combo' },
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection
