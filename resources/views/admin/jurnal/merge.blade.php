@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="card p-3 shadow">
            <span>Merge No. Jurnal</span>
            <hr>
            <form action="{{ route('jurnal.merge.store') }}" method="POST" class="row">
                @csrf
                <div class="col-4 mb-2">
                    <label for="awal">No. Jurnal Awal</label>
                    <select name="awal" id="awal" class="form-control select2" required>
                        <option value=""></option>
                        @foreach ($data as $item)
                            <option value="{{$item}}">{{$item}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 mb-2">
                    <label for="tujuan">No. Jurnal Tujuan</label>
                    <select name="tujuan" id="tujuan" class="form-control select2" required>
                        <option value=""></option>
                        @foreach ($data as $item)
                            <option value="{{$item}}">{{$item}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 mb-2">
                    <button type="submit" class="btn btn-sm btn-success mt-3" onclick="return confirm('are you sure?')">Merge</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $('.select2').select2();
</script>
@endsection