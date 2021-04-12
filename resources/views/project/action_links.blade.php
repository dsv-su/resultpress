@can('project-'.$project->id.'-edit')
    <a href="/project/{{$project->id}}/edit" class="btn btn-outline-secondary btn-sm mb-1"><span
                class="d-none d-sm-inline-block">Edit </span><i class="far fa-edit ml-sm-1"></i></a>
@endcan
@can('project-'.$project->id.'-update')
    <a href="/project/{{$project->id}}/update" class="btn btn-outline-secondary btn-sm mb-1"><span
                class="d-none d-sm-inline-block">Write an update </span><i class="fas fa-folder-plus ml-sm-1"></i></a>
    <a href="/project/{{$project->id}}/updates" class="btn btn-outline-secondary btn-sm mb-1"><span
                class="d-none d-sm-inline-block">All updates </span><i class="far fa-list-alt ml-sm-1"></i></a>
@endcan
@can('project-'.$project->id.'-delete')
    <a href="/project/{{$project->id}}/delete" class="btn btn-outline-danger btn-sm mb-1"
       onclick="return confirm('Are you sure you want to delete this item?');"><span class="d-none d-sm-inline-block">Delete </span><i
                class="far fa-trash-alt ml-sm-1"></i></a>
@endcan
