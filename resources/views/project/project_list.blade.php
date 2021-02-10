<div class="card my-3">
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="row my-1">
                        <a href="/project/{{$project->id}}" class="mr-2">{{ $project->name}}</a>
                        @if($project->status == 1) <span class="badge badge-warning font-100">In progress</span>
                        @elseif($project->status == 2) <span class="badge badge-danger font-100">Delayed</span>
                        @elseif($project->status == 3) <span class="badge badge-success font-100">Done</span>
                        @endif
                        @if($project->pending_updates()->count())
                            <a href="/project/{{$project->id}}/updates"><span class="badge badge-info font-100">Update
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
                            @foreach($project->areas as $k => $area)
                                {{$area->name}}@if ($k+1<count($project->areas)),@endif
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
