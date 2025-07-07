@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <p class="mb-0 text-center">
                    Welcome Back! {{ Auth::user()->name }}
                </p>
            </div>
        </div>
    </div>

@endsection
