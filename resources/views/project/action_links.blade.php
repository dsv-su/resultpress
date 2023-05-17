@php
    $btnClass = 'btn btn-outline-secondary btn-sm mb-1';
    $spanClass = 'd-none d-sm-inline-block';
@endphp

@if (Auth::user()->hasRole('Partner'))
        @can('project-' . $project->id . '-edit')
            <a href="/project/{{ $project->change_request->id ?? $project->id.'/edit' }}" title="Edit this project" class="{{ $btnClass }}"><span class="{{ $spanClass }}">{{ $project->object_type == 'project' ? $project->change_request ? 'Edit requested change' : 'Suggest a project change' : 'Edit' }}</span><i class="far fa-edit ml-sm-1"></i></a>

            <a href="/project/{{ $project->id }}/history" title="Changes history" class="{{ $btnClass }}"><span class="{{ $spanClass }}">History </span><i class="fas fa-history ml-sm-1"></i></a>
        @endcan

@elseif (Auth::user()->hasRole(['Spider', 'Administrator', 'Program administrator']))
    @can('project-' . $project->id . '-edit')
        @if ($project->change_request && $project->change_request->id)
            <a href="/project/{{ $project->change_request->id }}" title="Edit this project" class="{{ $btnClass }}"><span class="{{ $spanClass }}">Review change request</span><i class="far fa-edit ml-sm-1"></i></a>
        @endif
        <a href="/project/{{ $project->id }}/edit" title="Edit this project" class="{{ $btnClass }}"><span class="{{ $spanClass }}">Edit</span><i class="far fa-edit ml-sm-1"></i></a>

        <a href="/project/{{ $project->id }}/history" title="Changes history" class="{{ $btnClass }}"><span class="{{ $spanClass }}">History </span><i class="fas fa-history ml-sm-1"></i></a>
        
        @if(in_array($project->object_type, ['project_add_request', 'project_change_request']))
            <a href="/project/{{ $project->id }}/reject" title="Reject this project" class="btn btn-outline-danger btn-sm mb-1 ml-2 float-right" onclick="return confirm('Are you sure you want to reject this project?');"><span class="{{ $spanClass }}">Return for revision </span><i class="far fa-window-close ml-sm-1"></i></a>
            <a href="/project/{{ $project->id }}/accept" title="Write an update" class="{{ $btnClass }} ml-4 float-right"><span class="{{ $spanClass }}">Accept</span><i class="fas fa-check-square ml-sm-1"></i></a>
        @endif
    @endcan

@endif

@if (auth()->user()->can('project-' . $project->id . '-update') && $project->object_type == 'project')
    <a href="/project/{{ $project->id }}/update" title="Write an update" class="{{ $btnClass }}"><span class="{{ $spanClass }}">Write an update</span><i class="fas fa-folder-plus ml-sm-1"></i></a>
    <a href="/project/{{ $project->id }}/updates" title="Show all updates" class="{{ $btnClass }}"><span class="{{ $spanClass }}">All updates </span><i class="far fa-list-alt ml-sm-1"></i></a>
@endif

@if (Auth::user()->hasRole(['Spider', 'Administrator', 'Program administrator']))
    @can('project-' . $project->id . '-delete')
        @if ($project->object_type == 'project')
            <a href="/project/{{ $project->id }}/archive" title="Archive this project" class="btn btn-outline-danger btn-sm mb-1" onclick="return confirm('Are you sure you want to archive this item?');"><span class="{{ $spanClass }}">Archive </span><i class="fas fa-archive ml-sm-1"></i></a>
        @elseif ($project->object_type == 'project_archive')
            <a href="/project/{{ $project->id }}/unarchive" title="Unarchive this project" class="btn btn-outline-warning btn-sm mb-1" onclick="return confirm('Are you sure you want to unarchive this item?');"><span class="{{ $spanClass }}">Unarchive </span><i class="fas fa-archive ml-sm-1"></i></a>
        @endif
    @endcan
@endif
