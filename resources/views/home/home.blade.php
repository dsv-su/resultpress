@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10"><h4>{{Auth::user()->name ?? 'UserName' }}</h4></div>
        <!--
            @if(Auth::user()->password ?? '' == 'shibboleth')
            <div class="col-sm-2"><a href="/users" class="pull-right"><img title="profile image"
                                                                           class="img-circle img-responsive"
                                                                           src="{{asset('/images/su_logo_en.gif')}}"></a>
                </div>
            @else
            <div class="col-sm-2"><a href="/users" class="pull-right"><img title="profile image"
                                                                           class="img-circle img-responsive"
                                                                           src="{{asset('/images/partner_avatar.png')}}"></a>
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
                            @if($user->avatar == null)
                                <img src="{{asset('images/spider_avatar.png')}}" class="avatar img-circle img-thumbnail"
                                     alt="avatar">
                            @else
                                <img src="{{asset($user->avatar)}}" class="avatar img-circle img-thumbnail"
                                     alt="avatar">
                            @endif
                        </div>
                        <div class="text-center mb-3 col-6 col-md m-auto p-md-0">
                            <label for="avatar" class="form-label">Upload a different photo</label>
                            <input id="avatar" name="profile" class="form-control form-control-sm border-0 file-upload"
                                   type="file">
                        </div>
                        <ul class="list-group my-3">
                            <li class="list-group-item text-muted">Program Areas&nbsp;&nbsp; <i
                                        class="fa fa-dashboard fa-1x" data-toggle="modal"
                                        data-target="#programAreas"></i>
                            </li>
                            @foreach($programareas as $area)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{$area->name}}
                                    <span class="badge badge-primary badge-pill">{{$area->count}}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-9 my-3 my-md-0">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#owned">Owned Projects&nbsp;&nbsp; <i
                                            class="fa fa-dashboard fa-1x" data-toggle="modal"
                                            data-target="#ownedProjects"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#programareas">Program Area Projects&nbsp;&nbsp;
                                    <i class="fa fa-dashboard fa-1x" data-toggle="modal"
                                       data-target="#areaProjects"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#other">Other Projects&nbsp;&nbsp; <i
                                            class="fa fa-dashboard fa-1x" data-toggle="modal"
                                            data-target="#otherProjects"></i></a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="owned">
                                <ul class="list-group list-group-flush">
                                    @foreach($user->projects as $project)
                                        <li class="list-group-item">{{$project->name}}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="tab-pane" id="programareas">
                                <!-- Form -->
                                <ul class="list-group list-group-flush">
                                    @foreach($areas as $key => $area)
                                        <li class="list-group-item">
                                            <p>{{$area->name}}</p>
                                            @foreach($area->projects as $project)
                                                <div class="custom-control custom-checkbox">
                                                    @foreach($project->project_owner as $project_owner)
                                                        @if(auth()->user()->id == $project_owner->user->id)
                                                            <input class="form-check-input me-1 owner" type="checkbox"
                                                                   value="" aria-label="select" checked disabled>
                                                        @elseif (in_array($project->id, json_decode(auth()->user()->follow_projects), true))
                                                            <input class="form-check-input me-1" name="projects[]"
                                                                   type="checkbox" value="" aria-label="select" checked>
                                                        @else
                                                            <input class="form-check-input me-1" name="projects[]"
                                                                   type="checkbox" value="{{$project->id}}"
                                                                   aria-label="">
                                                        @endif
                                                    @endforeach
                                                    {{$project->name}}
                                                </div>
                                            @endforeach
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="tab-pane" id="other">
                                <ul class="list-group list-group-flush">
                                    @foreach($otherprojects as $otherproject)
                                        <li class="list-group-item">
                                            <div class="custom-control custom-checkbox">
                                                @if (in_array($otherproject->id, json_decode(auth()->user()->follow_projects), true))
                                                    <input class="form-check-input me-1" name="projects[]"
                                                           type="checkbox"
                                                           value="" aria-label="select" checked>
                                                @else
                                                    <input class="form-check-input me-1" name="projects[]"
                                                           type="checkbox"
                                                           value="{{$otherproject->id}}" aria-label="">
                                                @endif
                                                {{$otherproject->name}}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div><!--/tab-pane-->
                    </div><!--/tab-content-->
                </div><!--/col-9-->

                <div class="row">
                    <div class="col">
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" name="setting" type="checkbox" value=true aria-label=""
                                   checked id="every">
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
    </div><!--/row-->
    <!-- Modal ProgramAreas-->
    <div class="modal" id="programAreas">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Program Areas Summary</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    Summery of projects sorted by Program Areas
                    <br>

                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Owned Projects-->
    <div class="modal" id="ownedProjects">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Owned Projects</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    These are the projects for which you are registered as a project manager.
                    <br>

                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Program Area Projects-->
    <div class="modal" id="areaProjects">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Program Area Projects</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    Here you can select the projects you want to follow, sorted by Program area.
                    <br>

                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Other Projects-->
    <div class="modal" id="otherProjects">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Other Projects</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    Here you can select the projects you want to follow that are not organized in a Program area.
                    <br>

                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    </div>
    <script>
        $(document).ready(function () {
            var readURL = function (input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('.avatar').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $(".file-upload").on('change', function () {
                readURL(this);
            });
            $('.owner').on('change', function () {
                if ($(this).prop('checked') == false) {
                    alert('You are registred as an owner to this project, hence not be unselected');
                    $(this).prop("checked", true);
                }
            });
        });
    </script>
@endsection
