<div class="card m-auto">
    <div class="card-body pb-1">
        @if($invites)
            @foreach($invites as $invite)
            <div class="form-group mb-1 row">
                <label for="invite_email" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">{{$invite->email}}</label>
                <p class="col col-sm-6 pl-1 pr-1 col-form-label-sm text-right"> Sent: {{$invite->created_at->format('d-m-Y')}}</p>
                <a name="remove" class="btn btn-outline-danger btn-sm remove ml-1"><i class="far fa-trash-alt"></i></a>
            </div>
            @endforeach
        @else
            <label for="invite_email" class="col col-sm-3 pl-0 pr-1 col-form-label-sm text-right">No active invites</label>
        @endif
    </div>
</div>
