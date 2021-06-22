<div class="card m-auto">
    <div class="card-body pb-1">
        @if(count($invites)>0)
            @foreach($invites as $invite)
            <div class="form-group mb-1 row">
                <label for="invite_email" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">{{$invite->email}}</label>
                <p class="col col-sm-6 pl-1 pr-1 col-form-label-sm text-right"> Sent: {{$invite->created_at->format('d-m-Y')}}</p>
                <a href="{{route('invite_remove', $invite)}}" class="btn btn-outline-danger btn-sm invite-remove ml-1"><i class="far fa-trash-alt"></i></a>
            </div>
            @endforeach
            @empty(!$project->id)
                    <a href="{{route('invite_view', $project)}}" role="button" class="btn btn-outline-primary btn-sm ml-1"><i class="far fa-envelope"></i> Add Invite</a>
            @endempty
        @else
            <label for="invite_email" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">No active invites</label>
            @empty(!$project->id)
                <a href="{{route('invite_view', $project)}}" role="button" class="btn btn-outline-primary btn-sm ml-1"><i class="far fa-envelope"></i> Invite</a>
            @endempty
        @endif
    </div>
</div>
