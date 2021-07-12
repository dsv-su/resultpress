<div class="card my-3">
    <div class="card-header">
        <a href="/project/{{$project->id}}" class="mr-2">{{ $project->name}}</a>
        @if (Auth::user()->hasRole(['Spider', 'Administrator']))
            @if($project->status() == 'planned')
                <span class="badge badge-light font-100">Planned</span>
            @elseif($project->status() == 'inprogress')
                <span class="badge badge-warning font-100">In progress</span>
            @elseif($project->status() == 'delayedhigh')
                <span class="badge badge-danger font-100">Delayed</span>
            @elseif($project->status() == 'delayednormal')
                <span class="badge badge-danger font-100">Delayed</span>
            @elseif($project->status() == 'pendingreview')
                <span class="badge badge-primary font-100">Pending review</span>
            @elseif($project->status() == 'completed')
                <span class="badge badge-success font-100">Completed</span>
            @elseif($project->status() == 'archived')
                <span class="badge badge-secondary font-100">Archived</span>
            @elseif($project->status() == 'onhold')
                <span class="badge badge-secondary font-100">On hold</span>
            @elseif($project->status() == 'terminated')
                <span class="badge badge-secondary font-100">Terminated</span>
            @endif
        @endif
    </div>
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    @if ($project->project_owner->count()>0)
                        <div class="row">
                            @if ($project->project_owner->count()>1) Managers: @else Manager: @endif
                            &nbsp; @foreach($project->project_owner->all() as $k => $project_owner)
                                <span class="font-weight-light">{{$project_owner->user->name}}@if ($k+1<$project->project_owner->count())
                                        ,&nbsp;@endif</span>
                            @endforeach
                        </div>
                    @endif
                    @if (!$project->partners()->isEmpty())
                        <div class="row">
                            @if (count($project->partners())>1) Partners: @else Partner: @endif
                            &nbsp; @foreach($project->partners() as $k => $partner)
                                <span class="font-weight-light">{{$partner->name}}@if ($k+1<count($project->partners()))
                                        ,&nbsp;@endif</span>
                            @endforeach
                        </div>
                    @endif
                    @if (!$project->areas->isEmpty())
                        <div class="row">
                            @if (count($project->areas)>1) Areas: @else Area: @endif &nbsp;@if($project->project_area)
                                @foreach($project->areas as $k => $area)
                                    <span class="font-weight-light">{{$area->name}}@if ($k+1<count($project->areas)),
                                        &nbsp;@endif</span>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="row my-2 my-md-0 trimmed-text">Description:&nbsp;<span
                                class="font-weight-light">{!!$project->description!!}</span></div>
                </div>
            </div>
        </div>
    </div>
    @canany(['project-'.$project->id.'-edit', 'project-'.$project->id.'-update', 'project-'.$project->id.'-delete'])
        <div class="card-footer">
            <div class="col text-right">
                @include('project.action_links')
            </div>
        </div>
    @endcan
</div>
