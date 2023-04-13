@extends('layouts.iframe')
@section('content')
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-8">
            <div class="card p-3 shadow">
                <form action="{{ route('customer.update',$cus) }}" method="post">
                    @csrf
                    @method('PUT')
                    @include('admin.customer.form',['cus'=>$cus])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
