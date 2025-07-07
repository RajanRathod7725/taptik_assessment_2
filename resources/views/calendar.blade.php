@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
    <h1 class="mt-4">Calendar</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Calendar</li>
    </ol>

    <calendar/>

    </div>

@endsection
