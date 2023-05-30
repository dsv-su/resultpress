@extends('layouts.master')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
        </ol>
    </nav>
    <div class="row justify-content-between mb-3">
        <div class="col-6"></div>
        <div class="col-sm-auto d-flex align-items-center">
            @if ($project->status() == 'planned')
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
                <span class="badge badge-secondary font-100">Closed</span>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="form-row">
            <div class="col">
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="form-row">
            <div class="col">
                <div class="alert alert-success" role="alert">
                    {{ session()->get('success') }}
                </div>
            </div>
        </div>
    @endif

    <p>@include('project.action_links')</p>
    
    <h5 class="my-4">Project summary</h5>
    <p>{!! $project->summary !!}</p>

    <h5 class="my-4">Project description</h5>
    <p>{!! $project->description !!}</p>

    <div class="my-3 col card bg-light p-2">
        <div class="row my-1">
            <div class="col-sm col-md-3 font-weight-bold">Project area(s):</div>
            <div class="col-sm">
                @if (!$project->areas->isEmpty())
                    @foreach ($project->areas as $k => $area)
                        {{ $area->name }}@if ($k + 1 < count($project->areas))
                            ,
                        @endif
                    @endforeach
                @else
                    Not set
                @endif
            </div>
        </div>
        <div class="row my-1">
            <div class="col-sm col-md-3 font-weight-bold">Project period:</div>
            <div class="col-sm">{{ $project->dates }}</div>
        </div>
        <div class="row my-1">
            <div class="col-sm col-md-3 font-weight-bold">Project activities range:</div>
            <div class="col-sm">{{ $project->projectstart }} — {{ $project->projectend }}</div>
        </div>
        <div class="row my-1">
            <div class="col-sm col-md-3 font-weight-bold">Approved updates:</div>
            <div class="col-sm">{{ $project->updatesnumber }}</div>
        </div>
        @if ($project->recentupdate)
            <div class="row my-1">
                <div class="col-sm col-md-3 font-weight-bold">Most recent approved update:</div>
                <div class="col-sm">{{ $project->recentupdate }}</div>
            </div>
        @endif
        <div class="row my-1">
            <div class="col-sm col-md-3 font-weight-bold">Budget:</div>
            <div class="col-sm">
                @if (Auth::user()->hasRole(['Spider', 'Administrator']))
                    <span @if ($project->moneyspent > $project->budget) class="badge badge-danger font-100" @endif> {{ $project->moneyspent ?? 0 }} {{ $project->getCurrencySymbol() }}
                        / {{ $project->budget ?? 0 }} {{ $project->getCurrencySymbol() }}</span>
                @else
                    {{ $project->budget ?? 0 }} {{ $project->getCurrencySymbol() }}
                @endif
            </div>
        </div>
        <div class="row my-1">
            <div class="col-sm col-md-3 font-weight-bold">Deadlines:</div>
            @if (!$deadlines->isEmpty())
                <div class="col-sm">
                    @foreach ($deadlines as $deadline)
                        <p>{!! strip_tags($deadline->name) !!}: <span class="badge badge-secondary">{{ $deadline->set->format('m/d/Y') }}
                                @if ($deadline->reminder == true)
                                    <i class="far fa-bell ml-1"></i>
                                @endif
                            </span>
                        </p>
                    @endforeach
                </div>
            @else
                <div class="col-sm">Not set</div>
            @endif
        </div>
    </div>

    @if ($project->pending_updates()->count() && Auth::user()->hasRole(['Spider', 'Administrator']))
        <h5 class="my-4">Pending updates</h5>
        <div class="alert alert-info" role="alert">
            @foreach ($project->pending_updates()->all() as $index => $pu)
                <div class="row my-2">
                    <div class="col-auto pl-1 d-flex align-items-center">#{{ $pu->getIndex() }} created
                        on {{ $pu->created_at->format('d/m/Y') }}
                        by {{ $pu->user->name }}
                    </div>
                    <!--
                                <div class="col-auto px-1 d-flex align-items-center">
                                    <span class="badge badge-warning mx-2 font-100">Pending approval</span>
                                </div>
                                -->
                    <div class="col-auto px-1">
                        <a href="/project/update/{{ $pu->id }}" class="btn btn-outline-secondary btn-sm">Show
                            <i class="fas fa-info-circle"></i></a>
                        <a href="/project/update/{{ $pu->id }}/review" class="btn btn-outline-secondary btn-sm">Review
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
                    <div class="card-header bg-white" id="heading-activity-{{ $a->id }}">
                        <div class="row">
                            <div class="col-auto pl-1">
                                <span class="btn cursor-default px-0 text-left">
                                    {{ $a->title }} @if ($a->priority == 'high')
                                        <span data-toggle="tooltip" title="High priority"><i class="fas fa-arrow-alt-circle-up text-danger"></i></span>
                                    @endif
                                </span>
                            </div>
                            @if ($a->comments)
                                <div class="col-auto d-flex py-2 px-1 align-items-center"><span data-toggle="collapse" data-target="#collapse-activity-{{ $a->id }}" aria-expanded="false" role="button" aria-controls="collapseactivity-{{ $a->id }}" class="badge badge-light font-100">{{ count($a->comments) }} @if (count($a->comments) > 1)
                                            updates
                                        @else
                                            update
                                        @endif
                                    </span>
                                </div>
                            @endif
                            <div class="col-auto d-flex py-2 px-1 align-items-center">
                                <span class="badge font-100 @if ($a->moneyspent > $a->budget) badge-danger @else badge-info @endif">
                                    {{ $a->moneyspent ?? 0 }} {{ $project->getCurrencySymbol() }} / {{ ceil($a->budget) ?? 0 }} {{ $project->getCurrencySymbol() }}
                                </span>
                            </div>
                            <div class="col-auto d-flex py-2 px-1 align-items-center">
                                @if ($a->status() == 'planned')
                                    <span class="badge badge-light font-100">Planned</span>
                                @elseif($a->status() == 'inprogress')
                                    <span class="badge badge-warning font-100">In progress</span>
                                @elseif($a->status() == 'delayednormal')
                                    <span class="badge badge-danger font-100">Delayed</span>
                                @elseif($a->status() == 'delayedhigh')
                                    <span class="badge badge-danger font-100">Delayed Major</span>
                                @elseif($a->status() == 'pendingreview')
                                    <span class="badge badge-info font-100">Pending review</span>
                                @elseif($a->status() == 'completed')
                                    <span class="badge badge-success font-100">Completed</span>
                                @elseif($a->status() == 'cancelled')
                                    <span class="badge badge-dark font-100">Cancelled</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($a->comments)
                        <div id="collapse-activity-{{ $a->id }}" class="collapse" aria-labelledby="headin-activity-{{ $a->id }}" data-parent="#activities">
                            <div class="card-body">
                                @foreach ($a->comments as $puindex => $arr)
                                    @if (!$project->cumulative)
                                        <p>
                                            <b><a href="/project/update/{{ $arr['pu'] }}">Update {{ $puindex }}</a></b>:
                                    @endif {!! $arr['comments'] !!}
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        The project has no activities.
    @endif

    <h5 class="mb-2 mt-4">Outcomes</h5>
    <div id="outcomes">
        @if (!$project->outcomes->isEmpty())
            @foreach ($project->outcomes as $outcome)
                <div class="card mb-3">
                    @include('project.outcome_update', ['outcome_update' => $outcome->latest_approved_update(), 'outcome' => $outcome, 'show' => true])
                </div>
            @endforeach
        @else
            The project has no outcomes.
        @endif
    </div>

    <h5 class="mt-4">Outputs</h5>
    @if (!$outputs->isEmpty())
        <div class="accordion" id="outputs">
            @foreach ($outputs as $index => $output)
                <div class="card">
                    <div class="card-header bg-white" id="heading-output-{{ $output->id }}">
                        <div class="row">
                            <div class="col-auto pl-1">
                                <span class="btn cursor-default px-0 text-left">
                                    {!! strip_tags($output->indicator) !!}
                                </span>
                            </div>
                            @if ($output->publishedUpdates()->count() > 0)
                                <div class="col-auto d-flex py-2 px-1 align-items-center"><span data-toggle="collapse" data-target="#collapse-output-{{ $output->id }}" aria-expanded="false" role="button" aria-controls="collapseoutput-{{ $output->id }}" class="badge badge-light font-100">{{ $output->publishedUpdates()->count() }} @if ($output->publishedUpdates()->count() > 1)
                                            updates
                                        @else
                                            update
                                        @endif
                                    </span>
                                </div>
                            @endif
                            <div class="col-auto d-flex py-2 px-1 align-items-center">
                                @if (Auth::user()->hasRole(['Spider', 'Administrator']))
                                    <span class="badge ml-2 font-100 @if ($output->valuestatus == 1) badge-info @elseif($output->valuestatus == 2) badge-warning @elseif($output->valuestatus == 3) badge-success @else badge-light @endif">
                                        @if ($output->valuesum == 1 && $output->valuesum == $output->target)
                                            Completed
                                        @else
                                            {{ $output->valuesum }} 
                                            @if ($output->status == 'custom')
                                                (unplanned)
                                            @else
                                                / {{ $output->target }}
                                            @endif 
                                        @endif
                                    </span>
                                @else
                                    <span class="badge ml-2 font-100 badge-info">{{ $output->valuesum }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($output->publishedUpdates()->count() > 0)
                        <div id="collapse-output-{{ $output->id }}" class="collapse" aria-labelledby="headin-output-{{ $output->id }}" data-parent="#outputs">
                            <div class="card-body">
                                @foreach ($output->publishedUpdates() as $puindex => $arr)
                                    <p>
                                        <b><a href="/project/update/{{ $arr['project_update_id'] }}">Update {{ $puindex + 1 }}</a></b>:
                                        {!! $arr['progress'] !!}
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($output->latest_update)
                        <div class="card-body">
                            <p>
                                <b><span>Latest update</span></b>:
                                {!! $arr['progress'] !!}
                            </p>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    @else
        The project has no outputs.
    @endif

    @livewire('comments', ['project' => $project, 'comments' => $project->comments, 'commentable_type' => 'App\Project', 'commentable_id' => $project->id])

    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
        $('.btn.disabled').on('click', function(e) {
            e.preventDefault();
        });
    </script>

@endsection
