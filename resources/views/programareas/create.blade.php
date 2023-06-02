@extends('layouts.master')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('programareas') }}">Areas</a></li>
            <li class="breadcrumb-item active" aria-current="page">New</li>
        </ol>
    </nav>

    <form action="{{ route('areas.store') }}" method="POST">
        @csrf
        <div class="row mt-4">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input class="form-control"  name="name" type="text" placeholder="Name" value="{{ old('name') }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    <textarea name="description" id="description" rows="10"
                              placeholder="Description"
                              class="mediumEditor form-control">
                            {{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="row px-3">
            <label for="external_system_title" class="form-group-header">External System Title</label>
                <span data-toggle="tooltip" title="The name of external system of DSV. Example: Moodle"><i class="fas fa-info-circle fa-1x ml-1"></i></span>
        </div>

        <div class="row px-3 d-flex align-items-center">
            <input class="form-control"  name="external_system_title" type="text" placeholder="External System Title" value="{{ old('external_system_title', empty($area) ? '' : $area->external_system_title) }}">
        </div>

        <div class="row px-3 mt-4">
            <label for="external_system_link" class="form-group-header">External System Link</label>
                <span data-toggle="tooltip" title="Link to external system of DSV, this link will be used to auto-login users to other systems using thier current session."><i class="fas fa-info-circle fa-1x ml-1"></i></span>
        </div>

        <div class="row px-3 d-flex align-items-center">
            <input class="form-control"  name="external_system_link" type="text" placeholder="External System Link" value="">
        </div>

        <div class="row px-3">
            <label for="users" class="form-group-header">Users</label>
                <span data-toggle="tooltip" title="Users and permissions associated with this program area"><i class="fas fa-info-circle fa-1x ml-1"></i></span>
        </div>

        <div class="row px-3 d-flex align-items-center">
            <select name="user_id[]" class="custom-select" id="managers" multiple="multiple" required>
                @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->fullViewName}}</option>
                @endforeach
            </select>
        </div>

        <div class="row mt-4">
            <div class="col-xs-12 col-sm-12 col-md-12 text-left">
                <button type="button submit" class="btn btn-outline-primary">Save</button>
            </div>
        </div>
    </form>

    <script>
        $('#managers').multiselect();
        var editor = new MediumEditor('.mediumEditor#description', {placeholder: {text: "Area description"}});
    </script>

@endsection