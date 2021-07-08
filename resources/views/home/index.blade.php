@extends('layouts.master_new')

@section('content')
    @include('layouts.partials.searchbox')
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
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="col">
        <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="owned-tab" data-toggle="tab" role="tab" href="#owned"
                   aria-controls="owned">Owned Projects <span data-toggle="tooltip"
                                                              title="These are the projects for which you are registered as a project manager"><i
                                class="fas fa-info-circle fa-1x"></i></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="area-tab" data-toggle="tab" role="tab" aria-controls="area" href="#area">Program
                    Area Projects <span data-toggle="tooltip"
                                        title="These are the projects you have chosen to follow sorted by Program area"><i
                                class="fas fa-info-circle fa-1x"></i></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="other-tab" data-toggle="tab" role="tab" aria-controls="other" href="#other">Other
                    Projects <span data-toggle="tooltip"
                                   title="These are the projects you have chosen to follow that are not organized in a Program are"><i
                                class="fas fa-info-circle fa-1x"></i></span></a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane show active" role="tabpanel" aria-labelledby="owned-tab" id="owned">
                @foreach($user->projects as $project)
                    @include('project.project_list', ['$project' => $project])
                @endforeach
            </div>
            <div class="tab-pane" role="tabpanel" aria-labelledby="area-tab" id="area">
                @foreach($areas as $key => $area)
                    @foreach($area->projects as $project)
                        @if(in_array($project->id, json_decode(auth()->user()->follow_projects ?? '[]'), true))
                            @include('project.project_list', ['$project' => $project])
                        @endif
                    @endforeach
                @endforeach
            </div><!--/tab-pane-->
            <div class="tab-pane" role="tabpanel" id="other" aria-labelledby="other-tab">
                @foreach($otherprojects as $project)
                    @if(in_array($project->id, json_decode(auth()->user()->follow_projects ?? '[]'), true))
                        @include('project.project_list', ['$project' => $project])
                    @endif
                @endforeach
            </div><!--/tab-pane-->
        </div><!--/tab-content-->
    </div>

    <script>
        $('#myTab a').on('click', function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
        $('span[data-toggle=tooltip]').mouseover(function () {
            $(this).tooltip('show');
        });
    </script>

@endsection
