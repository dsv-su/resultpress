@can('project-'.$project->id.'-edit')
    <a href="/project/{{$project->id}}/edit" data-toggle="tooltip" title="Edit this project" class="btn btn-outline-secondary btn-sm mb-1"><span
                class="d-none d-sm-inline-block">Edit </span><i class="far fa-edit ml-sm-1"></i></a>
    <a href="/project/{{$project->id}}/history" data-toggle="tooltip" title="Changes history" class="btn btn-outline-secondary btn-sm mb-1"><span
                class="d-none d-sm-inline-block">History </span><i class="fas fa-history ml-sm-1"></i></a>
@endcan
@can('project-'.$project->id.'-update')
    <a href="/project/{{$project->id}}/update" data-toggle="tooltip" title="Write an update" class="btn btn-outline-secondary btn-sm mb-1"><span
                class="d-none d-sm-inline-block">Write an update </span><i class="fas fa-folder-plus ml-sm-1"></i></a>
    <a href="/project/{{$project->id}}/updates" data-toggle="tooltip" title="Show all updates" class="btn btn-outline-secondary btn-sm mb-1"><span
                class="d-none d-sm-inline-block">All updates </span><i class="far fa-list-alt ml-sm-1"></i></a>
@endcan
@can('project-'.$project->id.'-delete')
    <a href="/project/{{$project->id}}/delete" data-toggle="tooltip" title="Delete this project" class="btn btn-outline-danger btn-sm mb-1"
       onclick="return confirm('Are you sure you want to delete this item?');"><span class="d-none d-sm-inline-block">Delete </span><i
                class="far fa-trash-alt ml-sm-1"></i></a>
@endcan
