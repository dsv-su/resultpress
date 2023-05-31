@extends('layouts.master')
@section('content')
@include('taxonomies.nav', ['taxonomyType' => $taxonomyType ?? null])

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="text-right">
                @can('admin-create')
                    <a class="btn btn-outline-primary" href="{{ route('terms.create', ['type' => $taxonomyType ?? '', 'parent' => $requestParent ?? null]) }}"> Create New Taxonomy</a>
                @endcan
            </div>
        </div>
    </div>
    <table class="table table-bordered my-4">
        <tr>
            <th>Term</th>
            <th>Description</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($terms ?? [] as $key => $term)
            <tr>
                <td>
                    @if ($term->children->count())
                        <a href="{{ route('terms.index', ['type' => $taxonomyType->id ?? '', 'parent' => $term->id]) }}">{{ $term->title }}</a>
                        @else
                        {{ $term->title }}
                    @endif
                </td>
                <td>{{ $term->description }}</td>
                <td>
                    @if ($term->children->count())
                        <a class="btn btn-outline-primary" href="{{ route('terms.index', ['type' => $taxonomyType->id ?? '', 'parent' => $term->id]) }}">Taxonomies</a>
                    @endif
                    @can('admin-edit')
                        <a class="btn btn-outline-primary" href="{{ route('terms.edit',$term->id) }}">Edit</a>
                    @endcan
                    @can('admin-delete')
                        @if (!$term->children->count())
                            <form action="{{ route('terms.destroy', $term->id) }}" method="POST" style="display:inline"
                                    onsubmit="return confirm('Are you sure you want to delete this?');"
                            >
                                @method('DELETE')
                                @csrf

                                <input class="btn btn-outline-danger"  value="Delete" type="submit">
                            </form>
                        @endif
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
@endsection
