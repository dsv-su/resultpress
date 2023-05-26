@extends('layouts.master')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('programareas') }}">Areas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('programarea_show', ['id' => $area->id]) }}">{{ $area->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Projects</li>
        </ol>
    </nav>

    @include('layouts.partials.searchbox')
    <!-- Filter bar -->

    @can('project-create')
        <nav class="navbar navbar-light">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Select project area
                </button>
                <div class="dropdown-menu">
                    @foreach($program_areas as $program_area)
                        <a class="dropdown-item"
                           href="{{route('programarea_show', $program_area->id)}}">{{$program_area->name}}</a>
                    @endforeach
                </div>
            </div>
            @if ($area && $token && !empty($area->external_system_link) && !empty($area->external_system_title))
                <a href="{{ $area->external_system_link }}?token={{ $token }}" class="btn btn-primary" target="_blank">{{ $area->external_system_title }}</a>
            @endif
            
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
