@extends('layouts.master')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Pages</a></li>
        <li class="breadcrumb-item">{{ ucfirst(str_replace('-', ' ', $settings->name)) }}</li>
    </ol>
</nav>
{!! $settings->value !!}
@endsection
