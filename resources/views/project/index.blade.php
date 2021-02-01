@extends('layouts.master')

@section('content')
    <!-- Filter bar -->
    @can('project-create')
        <nav class="navbar navbar-light">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Filter by
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
            <div class="card my-3">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row my-1">
                                    <a href="/project/{{$project->id}}" class="mr-2">{{ $project->name}}</a>
                                    @if($project->status == 1) <span class="badge badge-warning">In progress</span>
                                    @elseif($project->status == 2) <span class="badge badge-danger">Delayed</span>
                                    @elseif($project->status == 3) <span class="badge badge-success">Done</span>
                                    @endif
                                    @if($project->pending_updates()->count())
                                        <a href="/project/{{$project->id}}/updates"><span class=" badge badge-info">Update
                        pending</span></a>
                                    @endif
                                </div>
                                <div class="row">
                                    Manager: @foreach($project->project_owner->all() as $project_owner)
                                        {{$project_owner->user->name}}
                                    @endforeach
                                </div>
                                <div class="row">
                                    Area: @if($project->project_area)
                                        @foreach($project->project_area->all() as $project_area)
                                            {{$project_area->area->name}}
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row my-1">{{$project->description}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col text-right">
                        @include('project.action_links')
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
