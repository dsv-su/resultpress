@extends('layouts.master')

@section('content')
@include('taxonomies.nav', ['taxonomyType' => $taxonomyType ?? null])

    <form action="{{ route('terms.store') }}" method="POST">
        @csrf
        <input type="hidden" name="type" value="{{ $taxonomyType->slug ?? '' }}">
    <div class="row my-4">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label class="form-label" for="title">Title</label>
                <input class="form-control"  name="title" type="text" placeholder="Title">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control"  name="description" placeholder="Description"></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label class="form-label" for="parent">Parent</label>
                {!! $rootTaxonomies !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
    </form>
@endsection
