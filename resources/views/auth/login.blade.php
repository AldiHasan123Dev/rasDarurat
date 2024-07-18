@extends('layouts.auth')

@section('content')
<form method="post" action="{{ route('login') }}" class="row flex-center h-100 g-0 px-4 px-sm-0">
    @csrf
    <div class="col col-sm-6 col-lg-7 col-xl-6">
        <a class="d-flex flex-center text-decoration-none mb-4" href="#">
        <img src="{{ asset('logo.png') }}" class="img-fluid" alt="logo" style="height: 120px">
        </a>
    <div class="text-center mb-2">
        <h3 class="text-1000">Sign In</h3>
    </div>
    <div class="mb-3">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('danger'))
            <div class="container">
                <div class="my-3">
                    <div class="alert alert-danger alert-dismissible fade show text-white py-2" role="alert">
                        <strong>Warning!</strong> {{ session('danger') }}
                        <button type="button" class="btn-close pt-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="mb-3 text-start">
        <label class="form-label" for="email">Email address</label>
        <div class="form-icon-container">
            <input class="form-control form-icon-input" id="email" type="email" name="email" />
            <span class="fas fa-user text-900 fs--1 form-icon"></span>
        </div>
    </div>
    <div class="mb-3 text-start">
        <label class="form-label" for="password">Password</label>
        <div class="form-icon-container">
            <input class="form-control form-icon-input" id="password" type="password" name="password" />
            <span class="fas fa-key text-900 fs--1 form-icon"></span>
        </div>
    </div>
        <button class="btn btn-primary w-100 mb-3" type="submit">Sign In</button>
    </div>
</form>
@endsection
