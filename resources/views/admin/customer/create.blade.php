@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-8">
            <div class="card p-3 shadow">
                <form action="{{ route('customer.store') }}" method="post">
                    @csrf
                    @include('admin.customer.form',['cus'=>[]])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
