@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Add taxonomy</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('taxonomies.index') }}"> Back</a>
            </div>
        </div>
    </div>
    @if (count($errors) > 0)
        <br>
        <div class="alert alert-danger">
            There are some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('taxonomies.store') }}" method="POST">
        @method('POST')
        @csrf
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input class="form-control" name="name" type="text" placeholder="Name" value="{{ old('name') }}">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection

