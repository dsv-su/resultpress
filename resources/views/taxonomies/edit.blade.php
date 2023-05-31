@extends('layouts.master')

@section('content')
@include('taxonomies.nav', ['taxonomyType' => $taxonomyType ?? null])

    <form action="{{ route('terms.update', $taxonomy->id) }}" method="POST">
        @method('PATCH')
        @csrf
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control" name="title" type="text" placeholder="Title" value="{{ old('title', empty($taxonomy) ? '' : $taxonomy->title) }}">
                </div>
                <div class="form-group hidden">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" name="description" placeholder="Description">{{ old('description', empty($taxonomy) ? '' : $taxonomy->description) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="type">Parent</label>
                    {!! $rootTaxonomies !!}
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection

