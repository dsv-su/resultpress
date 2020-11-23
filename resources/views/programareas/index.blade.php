@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Program Areas</h2><i class="fas fa-info-circle" data-toggle="modal" data-target="#programAreas"></i>
            </div>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <br>
        </div>
    </div>
    @foreach($programareas as $programarea)
    <div class="card">
        <div class="card-header">
            {{$programarea->name}}
        </div>
        <div class="card-body">
            <p class="card-text">{{$programarea->description}}</p>
            <a href="{{route('programarea_edit', $programarea->id)}}" class="btn btn-outline-primary">Edit</a>
        </div>
    </div>
    @endforeach
    <!-- The Modal -->
    <div class="modal" id="programAreas">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Program Areas</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    Each project should be associated to at least one program area. Program areas need to be able to have their own outputs and outcomes, which should be aggregated from related projects.
                    <br>
                    A project can connect to one or several program areas (not mandatory). Spider/administrator users have a filter in the project list page.
                    Program area names and descriptions can be edited. Each program area can have their own outputs and outcomes - this is not yet implemented.
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@endsection
