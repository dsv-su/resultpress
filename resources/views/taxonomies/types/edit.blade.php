@extends('layouts.master')

@section('content')
@include('taxonomies.nav', ['taxonomyType' => $taxonomyType ?? null])
    <form action="{{ route('types.update', $taxonomyType->id) }}" method="POST">
        @method('PATCH')
        @csrf
        <div class="row my-4">
            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-control" name="name" type="text" placeholder="Name" value="{{ old('name', empty($taxonomyType) ? '' : $taxonomyType->name) }}">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection

