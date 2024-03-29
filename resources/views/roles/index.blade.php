@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Role Management</h2>
            </div>
            <div class="pull-right">
                    <a class="btn btn-outline-primary" href="{{ route('admin') }}"> Back</a>
                @can('admin-create')
                    <a class="btn btn-outline-primary" href="{{ route('roles.create') }}"> Create New Role</a>
                @endcan
            </div>
            <br>
        </div>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($roles as $key => $role)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    <a class="btn btn-outline-info" href="{{ route('roles.show',$role->id) }}">Show</a>
                    @can('admin-edit')
                        <a class="btn btn-outline-primary" href="{{ route('roles.edit',$role->id) }}">Edit</a>
                    @endcan
                    @can('admin-delete')
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline">
                            @method('DELETE')
                            @csrf

                            <input class="btn btn-outline-danger"  value="Delete" type="submit">
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
@endsection
