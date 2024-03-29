<div class="card my-3">
    <div class="card-header">
        <a href="/project/{{ $project->id }}" class="mr-2">{{ $project->name }}</a>
        @if ($project->status() == 'planned')
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
            <span class="badge badge-secondary font-100">Closed</span>
        @endif
    </div>
    <div class="card-body">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    @if ($project->project_owner->count() > 0)
                        <div class="row"><span>
                                @if ($project->project_owner->count() > 1)
                                    Managers:
                                @else
                                    Manager:
                                @endif
                                <span class="font-weight-light">
                                    {{ implode(', ',array_map(function ($o) {return $o->user->name;}, $project->project_owner->all())) }}
                                </span>
                            </span></div>
                    @endif
                    @if (!$project->partners()->isEmpty())
                        <div class="row">
                            <span>
                                @if (count($project->partners()) > 1)
                                    Partners:
                                @else
                                    Partner:
                                @endif
                                <span class="font-weight-light">{{ implode(', ', array_column($project->partners()->toArray(), 'nameWithOrg')) }}</span>
                            </span>
                        </div>
                    @endif
                    @if (!$project->areas->isEmpty())
                        <div class="row"><span>
                                @if (count($project->areas) > 1)
                                    Areas:
                                @else
                                    Area:
                                @endif
                                <span class="font-weight-light">{{ implode(', ', array_column($project->areas->toArray(), 'name')) }}</span>
                            </span></div>
                    @endif
                </div>
                <div class="col-md-6">
                    @if (!empty($project->summary))
                        <div class="row my-2 my-md-0 trimmed-text">Summary:&nbsp;<span class="font-weight-light">{!! $project->summary !!}</span></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @canany(['project-' . $project->id . '-edit', 'project-' . $project->id . '-update', 'project-' . $project->id . '-delete'])
        <div class="card-footer">
            <div class="col text-right">
                @include('project.action_links')
            </div>
        </div>
    @endcan
</div>
