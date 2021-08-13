@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit: {{$area->name}}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('programareas') }}"> Back</a>
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
    <form action="{{ route('programarea_update', $area->id) }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input class="form-control"  name="name" type="text" placeholder="Name" value="{{ old('name', empty($area) ? '' : $area->name) }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    <textarea name="description" id="description"
                              placeholder="Description"
                              class="mediumEditor form-control">
                            {{ old('description', empty($area) ? '' : $area->description) }}</textarea>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="button submit" class="btn btn-outline-primary">Submit</button>
            </div>
        </div>
    </form>

    <script>
        var editor = new MediumEditor('.mediumEditor#description', {placeholder: {text: "Area description"}});
    </script>

@endsection