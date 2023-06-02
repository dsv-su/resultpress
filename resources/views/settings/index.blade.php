@extends('layouts.master')

@section('content')
    <div class="row my-4">
        <div class="col-lg-6 margin-tb">
            <h2>Settings Management</h2>
        </div>
        <div class="col-lg-6 text-right">
            <a class="btn btn-outline-primary" href="{{ route('admin') }}"> Back</a>
        @can('admin-create')
            <a class="btn btn-outline-primary" href="{{ route('settings.create') }}"> Create New Setting</a>
        @endcan
    </div>
    </div>
    <div class="row my-4">
        <div class="col-lg-12">
            <h5>Required settings</h5>
            logo, 
            system-message-login, 
            project-update-help, 
            help
        </div>

    </div>
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
                            <input type="hidden" name="setting" value="{{ $setting->name }}">
                            <input type="hidden" name="id" value="{{ $setting->id }}">
                            <input class="btn btn-outline-danger"  value="Delete" type="submit">
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
@endsection
