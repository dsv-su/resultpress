@extends('layouts.master')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('project_show', $project->id) }}">{{ $project->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Updates</li>
        </ol>
    </nav>
    <div class="container">
        @foreach ($project->project_updates as $index => $pu)
            @if (($pu->status == 'approved') || (Auth::user()->id == $pu->user_id && $pu->status != 'approved') || Auth::user()->hasRole(['Spider', 'Administrator']))
            <div class="card my-3">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row my-1">
                                    <a href="/project/update/{{$pu->id}}" class="mr-2">#{{$index+1}}</a>
                                    @if($pu->status == 'draft') <span class="badge badge-danger font-100">Draft</span>
                                    @elseif($pu->status == 'submitted') <span
                                            class="badge badge-warning font-100">Pending approval</span>
                                    @elseif($pu->status == 'approved') <span class="badge badge-success font-100">Approved</span>
                                    @endif
                                </div>
                                <div class="row">
                                    Created by {{$pu->user->name}} on {{$pu->created_at->format('d/m/Y')}}
                                </div>
                                <div class="row">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row my-1 trimmed-text">@if($pu->summary){!!$pu->summary!!} @else No summary provided @endif</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col text-right">
                        <a href="/project/update/{{$pu->id}}" class="btn btn-outline-secondary btn-sm">Show <i
                                    class="fas fa-info-circle"></i></a>
                        @if ($pu->status == 'draft' && Auth::user()->id == $pu->user_id)
                            <a href="/project/update/{{$pu->id}}/edit" class="btn btn-outline-secondary btn-sm">Edit <i
                                        class="fas fa-info-circle"></i></a>
                            <a href="/project/update/{{$pu->id}}/delete" class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this update?');">Delete <i
                                        class="fas fa-trash-alt"></i></a>
                        @elseif ($pu->status == 'submitted')
                            <a href="/project/update/{{$pu->id}}/review" class="btn btn-outline-secondary btn-sm">@if (Auth::user()->hasRole(['Spider', 'Administrator'])) Review @elseif (Auth::user()->hasRole(['Partner'])) Additional comments @endif
                                <i class="fas fa-highlighter"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        @endforeach
        <a class="btn btn-primary" href="/project/{{$project->id}}/update" role="button">Write a new update</a>
    </div>
@endsection