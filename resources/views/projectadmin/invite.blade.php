@extends('layouts.master')

@section('content')
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Invite a partner to project, <strong>{{$project->name}}</strong>
                    </div>
                    <div class="card-body">                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="post" action="{{route('process_invite')}}">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp" placeholder="Enter email">
                                <input type="number" name="project_id" value="{{$project->id}}" hidden>
                                <small id="emailHelp" class="form-text text-muted">Enter the Email address of the person you would like to invite</small>
                            </div>
                            <button type="submit" class="btn btn-outline-success">Send Invitation</button>
                        </form>                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
