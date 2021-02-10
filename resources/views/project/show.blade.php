@extends('layouts.master')

@section('content')
    <h4>{{$project->name}} — project summary</h4>

    <p><a href="{{ url()->previous() }}">Return back</a></p>
    <p>@include('project.action_links')</p>

    <p>{!!$project->description!!}</p>

    <div class="my-3 col-md-6 card bg-light p-2">
        <div class="row my-1">
            <div class="col-sm font-weight-bold">Project area(s):</div>
            <div class="col-sm">
                @if (!$project->areas->isEmpty())
                    @foreach($project->areas as $k => $area)
                        {{$area->name}}@if ($k+1<count($project->areas)),@endif
                    @endforeach
                @else Not set
                @endif
            </div>
        </div>
        <div class="row my-1">
            <div class="col-sm font-weight-bold">Project period:</div>
            <div class="col-sm">{{$project->dates}}</div>
        </div>
        <div class="row my-1">
            <div class="col-sm font-weight-bold">Project activities range:</div>
            <div class="col-sm">{{$project->projectstart}} — {{$project->projectend}}</div>
        </div>
        <div class="row my-1">
            <div class="col-sm font-weight-bold">Approved updates:</div>
            <div class="col-sm">{{$project->updatesnumber}}</div>
        </div>
        @if ($project->recentupdate)
            <div class="row my-1">
                <div class="col-sm font-weight-bold">Most recent approved update:</div>
                <div class="col-sm">{{$project->recentupdate}}</div>
            </div>
        @endif
        <div class="row my-1">
            <div class="col-sm font-weight-bold">Budget:</div>
            <div class="col-sm"><span
                        @if ($project->moneyspent > $project->budget) class="badge badge-danger font-100" @endif> {{$project->moneyspent ?? 0}} {{$project->getCurrencySymbol()}}
                    / {{$project->budget ?? 0}} {{$project->getCurrencySymbol()}}</span></div>
        </div>
    </div>

    @if($project->pending_updates()->count())
        <h5 class="my-4">Pending updates</h5>
        <div class="alert alert-info" role="alert">
            @foreach ($project->pending_updates()->all() as $index => $pu)
                <div class="row">
                    <div class="col-auto pl-1 d-flex align-items-center">#{{$index+1}} created
                        on {{$pu->created_at->format('d/m/Y')}}
                        by {{ $pu->user->name }}
                    </div>
                    <div class="col-auto px-1 d-flex align-items-center">
                        <span class="badge badge-warning mx-2 font-100">Pending approval</span>
                    </div>
                    <div class="col-auto px-1">
                        <a href="/project/update/{{$pu->id}}" class="btn btn-outline-secondary btn-sm">Show
                            <i class="fas fa-info-circle"></i></a>
                        <a href="/project/update/{{$pu->id}}/review"
                           class="btn btn-outline-secondary btn-sm">Review
                            <i class="fas fa-highlighter"></i></a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <h5 class="mb-2 mt-4">Activities</h5>
    @if (!$activities->isEmpty())
        <div class="accordion" id="activities">
            @foreach ($activities as $index => $a)
                <div class="card">
                    <div class="card-header bg-white" id="heading-activity-{{$a->id}}">
                        <div class="row">
                            <div class="col-auto pl-1">
                                 <span class="btn cursor-default px-0">
                                    {{$a->title}}
                                 </span>
                            </div>
                            @if ($a->comments)
                                <div class="col-auto d-flex py-2 px-1 align-items-center"><span
                                            data-toggle="collapse"
                                            data-target="#collapse-activity-{{$a->id}}"
                                            aria-expanded="false"
                                            role="button"
                                            aria-controls="collapseactivity-{{$a->id}}"
                                            class="badge badge-light font-100">{{count($a->comments)}} @if (count($a->comments) > 1)
                                            updates @else update @endif</span></div> @endif
                            <div class="col-auto d-flex py-2 px-1 align-items-center">
                                <span class="badge font-100 @if ($a->moneyspent > $a->budget) badge-danger @else badge-info @endif">
                                    {{$a->moneyspent ?? 0}} {{$project->getCurrencySymbol()}} / {{ceil($a->budget) ?? 0}} {{$project->getCurrencySymbol()}}
                                </span>
                            </div>
                            <div class="col-auto d-flex py-2 px-1 align-items-center">
                                @if($a->status == 1)
                                    <span class="badge badge-info font-100">In progress {{$a->statusdate}}</span>
                                @elseif($a->status == 2)
                                    <span class="badge badge-warning font-100">Delayed {{$a->statusdate}}</span>
                                @elseif($a->status == 3)
                                    <span class="badge badge-success font-100">Done {{$a->statusdate}}</span>
                                @elseif($a->status == 0)
                                    <span class="badge badge-light font-100">Not started</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($a->comments)
                        <div id="collapse-activity-{{$a->id}}" class="collapse"
                             aria-labelledby="headin-activity-{{$a->id}}"
                             data-parent="#activities">
                            <div class="card-body">
                                @foreach ($a->comments as $puindex => $comment)
                                    @if (!$project->cumulative) <p>
                                        <b>Update {{$puindex}}</b>: @endif {!! $comment !!}</p>
                                    @endforeach
                            </div>
                        </div>@endif
                </div>
            @endforeach
        </div>
    @else The project has no activities.
    @endif

    <h5 class="mt-4">Outcomes</h5>
    <div class="accordion" id="outcomes">
        @if (!$project->outcomes->isEmpty())
            @include('project.outcomes')
        @else
            The project has no outcomes.
        @endif
    </div>


    <h5 class="mt-4">Outputs</h5>
    @if (!$outputs->isEmpty())
        <div class="col p-2">
            @foreach ($outputs as $o)
                <div class="row my-1">
                    <div class="col">
                    <span>{{$o->indicator}}<span
                                class="badge ml-2 font-100 @if($o->valuestatus == 1) badge-info @elseif($o->valuestatus == 2) badge-warning @elseif($o->valuestatus == 3) badge-success @else badge-light @endif">{{$o->valuesum}} @if ($o->status == 'custom')
                                (unplanned) @else / {{$o->target}} @endif
                        </span></span>
                    </div>
                </div>
            @endforeach
        </div>
    @else The project has no outputs.
    @endif

    <script>
        $('.btn.disabled').on('click', function (e) {
            e.preventDefault();
        });
    </script>

@endsection
