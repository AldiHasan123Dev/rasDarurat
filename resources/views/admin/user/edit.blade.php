@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-8">
                <div class="card p-3">
                    <form action="{{ route('uservaleg55.update',Auth::user()) }}" method="post">
                        <input type="hidden" name="id" value="{{ Auth::id() }}">
                        @csrf
                        @method('PUT')
                        @include('admin.user.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
