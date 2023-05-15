@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Editing {{$area->name}}</h2> 
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('programareas') }}"> Back</a>
            </div>
        </div>
    </div>
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
        <div class="row mt-5">
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

        <div class="row px-3">
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
        $('#managers').multiselect();
        var editor = new MediumEditor('.mediumEditor#description', {placeholder: {text: "Area description"}});
    </script>

@endsection