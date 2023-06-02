@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Create New Setting</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('settings.index') }}"> Back</a>
            </div>
        </div>
    </div>
    <form action="{{ route('settings.store') }}" method="POST">
        @csrf
    <div class="row mt-5">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                <input class="form-control"  name="name" type="text" placeholder="Name">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>
                    Setting type:
                </strong>
                <select name="type" class="form-control">
                    @foreach ( $fieldTypes as $fieldTypeKey => $fieldType )
                        <option value="{{ $fieldTypeKey }}">{{ $fieldType }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
    </form>
@endsection
