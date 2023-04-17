@extends('layouts.admin')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">
<style>
    td:hover {
        cursor: pointer;
    }
    table.dataTable tbody th, table.dataTable tbody td{
        padding: 0px 10px !important;
    }
</style>
@endsection
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">Tambah Menu</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="menu" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Title</th>
                                <th>Urutan</th>
                                <th>Icon</th>
                                <th>Name</th>
                                <th>Url</th>
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

    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSubMenu" aria-controls="offcanvasSubMenu">Tambah SubMenu</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="submenu" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Menu</th>
                                <th>Title</th>
                                <th>Urutan</th>
                                <th>Name</th>
                                <th>Url</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasMenuLabel">Form Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('menu.store') }}" method="post">
                @csrf
                @include('admin.menu.form',[])
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasSubMenu" aria-labelledby="offcanvasSubMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSubMenuLabel">Form SubMenu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('submenu.store') }}" method="post">
                @csrf
                @include('admin.submenu.form',[])
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
    <script>
        let id;
        let table = $('#menu').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('menu.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'order', name: 'order' },
                { data: 'icon', name: 'icon' },
                { data: 'name', name: 'name' },
                { data: 'url', name: 'url' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        let table_submenu = $('#submenu').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('submenu.data') }}',
                data:function( d) {
                    d.menu_id = id;
                },
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'menu_id', name: 'menu_id' },
                { data: 'title', name: 'title' },
                { data: 'order', name: 'order' },
                { data: 'name', name: 'name' },
                { data: 'url', name: 'url' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $('#menu tbody').on( 'click', 'tr', function () {
            id =  table.row( this ).data().id;
            table_submenu.ajax.reload()
        });
    </script>
@endsection
