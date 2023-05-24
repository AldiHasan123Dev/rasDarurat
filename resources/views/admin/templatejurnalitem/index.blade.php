@extends('layouts.admin')
@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header p-2 d-flex justify-content-between" style="gap:10px">
                <button class="py-2 px-3 btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTemplateJurnalItem" aria-controls="offcanvasTemplateJurnalItem">Tambah TemplateJurnalItem</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.7rem">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>Template_jurnal_id</th>
                                <th>Coa_id</th>
                                <th>Tipe</th>
                                <th>No</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
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


    <div class="offcanvas offcanvas-start" tabindex="-2" id="offcanvasTemplateJurnalItem" aria-labelledby="offcanvasTemplateJurnalItemLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTemplateJurnalItemLabel">Form TemplateJurnalItem</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('templatejurnalitem.store') }}" method="post">
                @csrf
                @include('admin.templatejurnalitem.form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let table = $('.table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url: '{{ route('templatejurnalitem.data') }}',
                method:'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            },
            columns: [
                { data: 'id', name: 'id' },
            { data: 'template_jurnal_id', name: 'template_jurnal_id' },
            { data: 'coa_id', name: 'coa_id' },
            { data: 'tipe', name: 'tipe' },
            { data: 'no', name: 'no' },
            { data: 'deskripsi', name: 'deskripsi' },
            { data: 'jumlah', name: 'jumlah' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    </script>
@endsection