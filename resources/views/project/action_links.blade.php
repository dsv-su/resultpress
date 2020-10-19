@can('project-'.$project->id.'-edit')
<a href="/project/{{$project->id}}/edit" class="btn btn-outline-secondary btn-sm">Edit <i class="far fa-edit"></i></a>
@endcan
@can('project-'.$project->id.'-update')
<a href="/project/{{$project->id}}/update" class="btn btn-outline-secondary @if ($project->hasDraft() && $project->cumulative) disabled @endif btn-sm">Write an update <i class="fas fa-folder-plus"></i></a>
<a href="/project/{{$project->id}}/updates" class="btn btn-outline-secondary btn-sm">All updates <i class="far fa-list-alt"></i></a>
@endcan
@can('project-'.$project->id.'-delete')
<a href="/project/{{$project->id}}/delete" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');">Delete <i class="far fa-trash-alt"></i></a>
@endcan
