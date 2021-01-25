@extends('layouts.master_new')

@section('content')
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#partner">Projects&nbsp;&nbsp; <i class="fa fa-dashboard fa-1x" data-toggle="modal" data-target="#Projects"></i></a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="partner">
                <div class="row">
                    <div class="col">
                        <div class="card shadow">
                            <div class="table-responsive">
                                <table id="example" class="table align-items-center table-flush"
                                       data-order='[[ 0, "desc" ]]'
                                       data-page-length='25'>
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Id</th>
                                        <th>Project Name</th>
                                        <th>Program Area</th>
                                        <th>Status</th>
                                        <th>Manager</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach( $projects as $project )
                                        <tr>
                                            <td>{{ $project->id }}</td>
                                            <td><a href="/project/{{$project->id}}">{{ $project->name}}</a></td>
                                            <td>@if($project->project_area)
                                                    @foreach($project->project_area->all() as $project_area)
                                                        {{$project_area->area->name}}
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if($project->status == 1) <span class="badge badge-warning">In progress</span>
                                                @elseif($project->status == 2) <span class="badge badge-danger">Delayed</span>
                                                @elseif($project->status == 3) <span class="badge badge-success">Done</span>
                                                @endif
                                                @if($project->pending_updates()->count())
                                                    <a href="/project/{{$project->id}}/updates"><span class=" badge badge-info">Update
                                        pending</span></a>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach($project->project_owner->all() as $project_owner)
                                                    {{$project_owner->user->name}}
                                                @endforeach
                                            </td>
                                            <td class="text-right">
                                                <div class="dropdown">
                                                    <a class="btn btn-sm btn-icon-only" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                        @include('project.action_links')
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <!-- Modal Projects-->
                <div class="modal" id="Projects">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">Projects</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                                These are the projects in which you have been registered as a project partner
                                <br>

                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>

                        </div>
                    </div>
                </div>
@endsection
