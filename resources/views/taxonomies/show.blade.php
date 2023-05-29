@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> Show Taxonomy</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('taxonomies.index') }}"> Back</a>
            </div>
            <br>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $taxonomy->name }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <h3>Terms</h3>
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <th>Projects</th>
                    <th width="280px">Action</th>
                </tr>
                @foreach ($taxonomy->terms as $term)
                    <tr>
                        <td>{{ $term->name }}</td>
                        <td></td>
                        <td>
                            @can('admin-edit')
                                <a class="btn btn-outline-primary" href="{{ route('taxonomies.edit',$term->id) }}">Edit</a>
                            @endcan
                            @can('admin-delete')
                                <form action="{{ route('taxonomies.destroy', $term->id) }}" method="POST" style="display:inline">
                                    @method('DELETE')
                                    @csrf

                                    <input class="btn btn-outline-danger"  value="Delete" type="submit">
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
            </table>
        </div>
    </div>
@endsection
