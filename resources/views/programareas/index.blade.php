@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Program Areas</h2>
            </div>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <br>
        </div>
    </div>
    @foreach($programareas as $programarea)
    <div class="card">
        <div class="card-header">
            {{$programarea->name}}
        </div>
        <div class="card-body">
            <p class="card-text">{{$programarea->description}}</p>
            <a href="{{route('programarea_edit', $programarea->id)}}" class="btn btn-outline-primary">Edit</a>
        </div>
    </div>
    @endforeach
@endsection
