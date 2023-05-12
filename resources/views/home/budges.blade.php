@if ($project->project_updates->where('status', 'submitted')->count())
    <a href="{{ route('projectupdate_index', ['project' => $project->id]) }}">
        <span class="badge badge-primary badge-pill">{{ $project->project_updates->where('status', 'submitted')->count() }} Pending updates</span>
    </a>
@endif
@if ($project->change_request)
    <a href="{{ route('project_show', ['project' => $project->change_request->id]) }}">
        <span class="badge badge-warning badge-pill">Change Request</span>
    </a>
@endif
@if ($project->comments->count())
    <a href="{{ route('project_show', ['project' => $project->id]) }}">
        <span class="badge badge-info badge-pill">{{ $project->comments->count() }} Comments</span>
    </a>
@endif
