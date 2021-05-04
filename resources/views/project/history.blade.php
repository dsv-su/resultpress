@extends('layouts.master')

@section('content')
    <div class="row justify-content-between">
        <div class="col-6"><h4>{{ $project->name }}</h4></div>
        <div class="col-sm-auto d-flex align-items-center">
            @if($project->status() == 'planned')
                <span class="badge badge-light font-100">Planned</span>
            @elseif($project->status() == 'inprogress')
                <span class="badge badge-warning font-100">In progress</span>
            @elseif($project->status() == 'delayedhigh')
                <span class="badge badge-danger font-100">Delayed Major</span>
            @elseif($project->status() == 'delayednormal')
                <span class="badge badge-danger font-100">Delayed</span>
            @elseif($project->status() == 'pendingreview')
                <span class="badge badge-primary font-100">Pending review</span>
            @elseif($project->status() == 'completed')
                <span class="badge badge-success font-100">Completed</span>
            @elseif($project->status() == 'archived')
                <span class="badge badge-secondary font-100">Archived</span>
            @elseif($project->status() == 'onhold')
                <span class="badge badge-secondary font-100">On hold</span>
            @elseif($project->status() == 'terminated')
                <span class="badge badge-secondary font-100">Terminated</span>
            @endif
        </div>
    </div>

    <p><a href="{{ url()->previous() }}">Return back</a></p>
    <p>@include('project.action_links')</p>

    @foreach($project->history as $index => $history)
        <div class="card my-2 bg-white">
            <div class="card-header">
                #{{$index+1}} from {{$history->created_at->format('m/d/Y')}} by {{$history->user->name}}
            </div>
            <div class="card-body">
                @if ($index)
                    @if ($history->diff()->getDiffCnt())
                        @if (!empty($history->diff()->getModifiedDiff()))
                            <h4>Modified:</h4>
                            @foreach($history->diff()->getModifiedNew() as $key => $m)
                                @if (is_array($m))
                                    <h5 class="mt-2">{{ucfirst($key)}}:</h5>
                                    @foreach($m as $i => $item)
                                        <p>id #{{$history->diff()->getRearranged()->$key[$i]->id}} new
                                            values: {{json_encode($item)}}</p>

                                    @endforeach
                                @elseif ($key == 'project_updates')
                                    <h5 class="mt-2">Project updates</h5>
                                    @foreach($m as $i => $pu)
                                        <p><a href="/project/update/{{$history->diff()->getRearranged()->$key[$i]->id}}">#{{$history->diff()->getRearranged()->$key[$i]->id}}</a> submitted by {{\App\User::find($history->diff()->getRearranged()->$key[$i]->user_id)->name}} new
                                            status: {{$pu->status == 'draft' ? 'returned for revision' : $pu->status}}</p>
                                    @endforeach
                                @else
                                    <p>{{ucfirst($key)}}
                                        : {{($key=='start' || $key=='end') ? Carbon\Carbon::parse($m)->format('d/m/Y') : $m}}</p>
                                @endif
                            @endforeach
                        @endif
                        @if (!empty($history->diff()->getAdded()))
                            <h4 class="mt-2">Added:</h4>
                            @foreach($history->diff()->getAdded() as $key => $a)
                                <h5>{{ucfirst($key)}}:</h5>
                                @foreach($a as $value)
                                        <p>@if ($key == 'project_updates') <a href="/project/update/{{$value->id}}">#{{$value->id}}</a> submitted by {{\App\User::find($value->user_id)->name}} @endif {{json_encode($value)}}</p>
                                @endforeach
                            @endforeach
                        @endif
                        @if (!empty($history->diff()->getRemoved()))
                            <h4 class="mt-2">Removed:</h4>
                            @foreach($history->diff()->getRemoved() as $key => $r)
                                <h5 class="mt-2">{{ucfirst($key)}}:</h5>
                                @if (is_object($r))
                                    @foreach($r as $value)
                                        <p>{{json_encode($value)}}</p>
                                    @endforeach
                                @elseif (is_array($r))
                                    @foreach($r as $item)
                                        {{json_encode($item)}}
                                    @endforeach
                                @else
                                    <p>{{json_encode($r)}}</p>
                                @endif
                            @endforeach
                        @endif
                    @else
                        No changes
                    @endif
                @else
                    Initial version
                @endif
            </div>
        </div>
    @endforeach

@endsection