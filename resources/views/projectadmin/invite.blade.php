@extends('layouts.master')

@section('content')
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Invite a partner to project, <strong>{{$project->name}}</strong>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{route('process_invite')}}">
                            @csrf
                            <label for="org">Select Organisation</label>
                            <div class="col-md-8">
                                <select name="org" class="form-control">
                                    @foreach($organisations as $org)
                                    <option value="{{$org->id}}">{{$org->org}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Partners Email address</label>
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
