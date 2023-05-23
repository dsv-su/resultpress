@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Settings Management</h2>
            </div>
            <div class="pull-right">
                    <a class="btn btn-outline-primary" href="{{ route('admin') }}"> Back</a>
                @can('admin-create')
                    <a class="btn btn-outline-primary" href="{{ route('settings.create') }}"> Create New Setting</a>
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
            <th>Setting key</th>
            <th>Type</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($settings as $key => $setting)
            <tr>
                <td>{{ $setting->name }}</td>
                <td>{{ $setting->type }}</td>
                <td>
                    @can('admin-edit')
                        <a class="btn btn-outline-primary" href="{{ route('settings.edit',$setting->name) }}">Edit</a>
                    @endcan
                    @can('admin-delete')
                        <form action="{{ route('settings.destroy', $setting->id) }}" method="POST" style="display:inline">
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
