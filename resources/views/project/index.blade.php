@extends('layouts.master')

@section('content')
    @include('layouts.partials.searchbox')
    <!-- Filter bar -->

    @can('project-create')
        <nav class="navbar navbar-light">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Switch project area
                </button>
                <div class="dropdown-menu">
                    @foreach($program_areas as $program_area)
                        <a class="dropdown-item"
                           href="{{route('programarea_show', $program_area->id)}}">{{$program_area->name}}</a>
                    @endforeach
                </div>
            </div>
        </nav>
        <br>
    @endcan
    <h3 class="mx-3">Projects</h3>
    <div class="container">
        @foreach( $projects as $project )
            @include('project.project_list', ['$project' => $project])
        @endforeach
    </div>
@endsection
