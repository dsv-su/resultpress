@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10">
                <h4>{{ Auth::user()->name ?? 'UserName' }}</h4>
            </div>
            <!--
                @if (Auth::user()->password ?? '' == 'shibboleth')
    <div class="col-sm-2"><a href="/users" class="pull-right"><img title="profile image"
                                                                               class="img-circle img-responsive"
                                                                               src="{{ asset('/images/su_logo_en.gif') }}"></a>
                    </div>
@else
    <div class="col-sm-2"><a href="/users" class="pull-right"><img title="profile image"
                                                                               class="img-circle img-responsive"
                                                                               src="{{ asset('/images/partner_avatar.png') }}"></a>
                    </div>
    @endif
                    -->
        </div>
        <div class="row my-3">
            <form action="{{ route('profile_store', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">User picture</div>
                        <!-- Profile picture -->
                        <div class="text-center col-6 col-md m-auto p-md-0">
                            @if ($user->avatar == null)
                                <img src="{{ asset('images/spider_avatar.png') }}" class="avatar img-circle img-thumbnail" alt="avatar">
                            @else
                                <img src="{{ asset('storage/' . $user->avatar) }}" class="avatar img-circle img-thumbnail" alt="avatar">
                            @endif
                        </div>
                        <div class="text-center mb-3 col-6 col-md m-auto p-md-0">
                            <label for="avatar" class="form-label">Upload a different photo</label>
                            <input id="avatar" name="profile" class="form-control form-control-sm border-0 file-upload" type="file">
                        </div>
                        @can('view-areas')
                        <ul class="list-group my-3">
                            <li class="list-group-item text-muted">Program Areas <span data-toggle="tooltip" title="Summary of projects sorted by Program Areas"><i class="fas fa-info-circle fa-1x"></i></span>
                            </li>
                            @foreach ($programareas as $area)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('programarea_show', ['id' => $area->area_id]) }}">{{ $area->name }}</a>
                                    <span class="badge badge-primary badge-pill">{{ $area->count }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @endcan
                    </div>
                    <div class="col-md-9 my-3 my-md-0">
                        <ul class="nav nav-tabs nav-justified">
                            @can('system-admin')
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#owned">Owned Projects <span data-toggle="tooltip" title="These are the projects for which you are registered as a project manager"><i class="fas fa-info-circle fa-1x"></i></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#programareas">Program Area Projects
                                    <span data-toggle="tooltip" title="Here you can select the projects you want to follow, sorted by Program area"><i class="fas fa-info-circle fa-1x"></i></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#other">Other Projects <span data-toggle="tooltip" title="Here you can select the projects you want to follow that are not organized in a Program area"><i class="fas fa-info-circle fa-1x"></i></span></a>
                            </li>
                            @endcan
                            @if(Auth::user()->hasRole('Partner'))
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#owned">Assigned Projects <span data-toggle="tooltip" title="These are the projects for which you are registered for this partner"><i class="fas fa-info-circle fa-1x"></i></span></a>
                            </li>
                            @endif
                        </ul>
                        <div class="tab-content">
                            @can('system-admin')
                            <div class="tab-pane active" id="owned">
                                <ul class="list-group list-group-flush">
                                    @foreach ($user->projects as $project)
                                        <li class="list-group-item">
                                            <a href="{{ route('project_show', ['project' => $project->id]) }}">{{ $project->name }}</a>
                                            @include('home.budges')
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="tab-pane" id="programareas">
                                <!-- Form -->
                                <ul class="list-group list-group-flush">
                                    @foreach ($areas as $key => $area)
                                        <li class="list-group-item">
                                            <p>{{ $area->name }}</p>
                                            @foreach ($area->projects as $project)
                                                <div class="custom-control custom-checkbox">
                                                    @foreach ($project->project_owner as $project_owner)
                                                        @if (auth()->user()->id == $project_owner->user->id)
                                                            <input class="form-check-input me-1 owner" type="checkbox" value="" aria-label="select" checked disabled>
                                                        @elseif (in_array($project->id, json_decode(auth()->user()->follow_projects), true))
                                                            <input class="form-check-input me-1" name="projects[]" type="checkbox" value="" aria-label="select" checked>
                                                        @else
                                                            <input class="form-check-input me-1" name="projects[]" type="checkbox" value="{{ $project->id }}" aria-label="">
                                                        @endif
                                                    @endforeach
                                                    <a href="{{ route('project_show', ['project' => $project->id]) }}">{{ $project->name }}</a>
                                                    @include('home.budges')
                                                </div>
                                            @endforeach
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="tab-pane" id="other">
                                <ul class="list-group list-group-flush">
                                    @foreach ($otherprojects as $otherproject)
                                        <li class="list-group-item">
                                            <div class="custom-control custom-checkbox">
                                                @if (in_array($otherproject->id, json_decode(auth()->user()->follow_projects), true))
                                                    <input class="form-check-input me-1" name="projects[]" type="checkbox" value="" aria-label="select" checked>
                                                @else
                                                    <input class="form-check-input me-1" name="projects[]" type="checkbox" value="{{ $otherproject->id }}" aria-label="">
                                                @endif
                                                <a href="{{ route('project_show', ['project' => $otherproject->id]) }}">{{ $otherproject->name }}</a>
                                                @include('home.budges', ['project' => $otherproject])
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endcan
                            @if (auth()->user()->hasRole('Partner'))
                            <div class="tab-pane active" id="owned">
                                <ul class="list-group list-group-flush">
                                    @foreach ($user->partner_projects as $project)
                                        <li class="list-group-item">
                                            <a href="{{ route('project_show', ['project' => $project->id]) }}">{{ $project->name }}</a>
                                            @include('home.budges')
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                        </div>
                        <!--/tab-pane-->
                    </div>
                    <!--/tab-content-->
                </div>
                <!--/col-9-->

                <div class="row">
                    <div class="col">
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" name="setting" type="checkbox" value=true aria-label="" checked id="every">
                            <label class="form-check-label" for="every">
                                Show this settings page at every sign in
                            </label>
                        </div>
                    </div>
                    <div class="col-auto ml-auto">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--/row-->

    <script>
        $(document).ready(function() {
            let readURL = function(input) {
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('.avatar').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $(".file-upload").on('change', function() {
                readURL(this);
            });
            $('.owner').on('change', function() {
                if ($(this).prop('checked') == false) {
                    alert('You are registred as an owner to this project, hence not be unselected');
                    $(this).prop("checked", true);
                }
            });
            $('span[data-toggle=tooltip]').mouseover(function() {
                $(this).tooltip('show');
            });
        });
    </script>
@endsection
