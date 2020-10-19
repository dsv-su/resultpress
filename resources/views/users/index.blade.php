@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Users Management</h2>
            </div>
            <br>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('admin') }}"> Back</a>
                @can('admin-create')
                <a class="btn btn-outline-success" href="{{ route('users.create') }}"> Create New User</a>
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
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($data as $key => $user)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                            <label class="badge badge-success">{{ $v }}</label>
                        @endforeach
                    @endif
                </td>
                <td>
                    @can('admin-list')
                        <a class="btn btn-outline-info" href="{{ route('users.show',$user->id) }}">Show</a>
                    @endcan
                    @can('admin-update')
                        <a class="btn btn-outline-primary" href="{{ route('users.edit',$user->id) }}">Edit</a>
                    @endcan
                    @can('admin-delete')
                        @if($user->password !== 'shibboleth')
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline">
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
