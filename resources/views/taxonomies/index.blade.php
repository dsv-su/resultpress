@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Taxonomies Management</h2>
            </div>
            <div class="pull-right">
                    <a class="btn btn-outline-primary" href="{{ route('admin') }}"> Back</a>
                @can('admin-create')
                    <a class="btn btn-outline-primary" href="{{ route('taxonomies.create') }}"> Create New Taxonomy</a>
                @endcan
            </div>
            <br>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <th>Terms</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($taxonomies as $taxonomy)
            <tr>
                <td>{{ $taxonomy->name }}</td>
                <td>{{ $taxonomy->terms->count() }}</td>
                <td>
                    @can('admin-edit')
                        <a class="btn btn-outline-primary" href="{{ route('taxonomies.edit',$taxonomy->id) }}">Edit</a>
                    @endcan
                    @can('admin-delete')
                        @if ($taxonomy->terms->count() == 0)
                            <form action="{{ route('taxonomies.destroy', $taxonomy->id) }}" method="POST" style="display:inline">
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
