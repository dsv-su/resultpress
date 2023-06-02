@extends('layouts.master')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('programareas') }}">Areas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('programarea_show', ['id' => $area->id]) }}">{{ $area->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    @if (count($errors) > 0)
        <br>
        <div class="alert alert-danger">
            There are some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('programarea_update', $area->id) }}" method="POST">
        @csrf
        <div class="row mt-4">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input class="form-control"  name="name" type="text" placeholder="Name" value="{{ old('name', empty($area) ? '' : $area->name) }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    <textarea name="description" id="description" rows="10"
                              placeholder="Description"
                              class="mediumEditor form-control">
                            {{ old('description', empty($area) ? '' : $area->description) }}</textarea>
                </div>
            </div>
        </div>
        @if (auth()->user()->hasRole('Administrator'))
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
                <input class="form-control"  name="external_system_link" type="text" placeholder="External System Link" value="{{ old('external_system_link', empty($area) ? '' : $area->external_system_link) }}">
            </div>
        @endif
        @if ($taxonomyTypes->count() && auth()->user()->hasRole('Administrator'))
            @foreach ($taxonomyTypes as $taxonomyType)
                <div class="row px-3 mt-4">
                    <label for="users" class="form-group-header">{{ $taxonomyType->name }}</label>
                        <span data-toggle="tooltip" title="Users and permissions associated with this program area"><i class="fas fa-info-circle fa-1x ml-1"></i></span>
                </div>
        
                <div class="row px-3 d-flex align-items-center">
                    <select name="{{ $taxonomyType->slug }}[]" class="custom-select" id="{{ $taxonomyType->slug }}" multiple="multiple" required>
                        {!! $taxonomyType->buildOptionsTree($taxonomyType->taxonomiesTree(), null, $area->taxonomies($taxonomyType->slug)->pluck('id')->toArray()) !!}
                    </select>
                </div>
            @endforeach
        @endif
        <div class="row px-3 mt-4">
            <label for="users" class="form-group-header">Users</label>
                <span data-toggle="tooltip" title="Users and permissions associated with this program area"><i class="fas fa-info-circle fa-1x ml-1"></i></span>
        </div>

        <div class="row px-3 d-flex align-items-center">
            <select name="user_id[]" class="custom-select" id="managers" multiple="multiple" required>
                @foreach($users as $user)
                    <option value="{{$user->id}}" {{ in_array($user->id, $areaUsers) ? 'selected':''}}>{{$user->fullViewName}}</option>
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
        @if ($taxonomyTypes->count())
            @foreach ($taxonomyTypes as $taxonomyType)
                $('#{{ $taxonomyType->slug }}').multiselect();
            @endforeach
        @endif
        $('#managers').multiselect();
        var editor = new MediumEditor('.mediumEditor#description', {placeholder: {text: "Area description"}});
    </script>

@endsection