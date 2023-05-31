@extends('layouts.master')

@section('content')
@include('taxonomies.nav', ['taxonomyType' => $taxonomyType ?? null])
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="text-right">
                @can('admin-create')
                    <a class="btn btn-outline-primary" href="{{ route('types.create') }}"> Create New Type</a>
                @endcan
            </div>
        </div>
    </div>
    <table class="table table-bordered my-4">
        <tr>
            <th>Type</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($types as $key => $type)
            <tr>
                <td><a class="" href="{{ route('types.terms',['type' => $type->id]) }}">{{ $type->name }}</a></td>
                <td>
                    <a class="btn btn-outline-info" href="{{ route('types.terms',['type' => $type->id]) }}">Taxonomies</a>
                    @can('admin-edit')
                        <a class="btn btn-outline-primary" href="{{ route('types.edit',$type->id) }}">Edit</a>
                    @endcan
                    @can('admin-delete')
                        @if (!$type->taxonomies()->count())
                            <form action="{{ route('types.destroy', $type->id) }}" method="POST" style="display:inline"
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
