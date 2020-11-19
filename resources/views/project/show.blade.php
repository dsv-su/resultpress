@extends('layouts.master')

@section('content')
    <h4>{{$project->name}} — project summary</h4>

    <p><a href="{{ url()->previous() }}">Return back</a></p>
    <p>@include('project.action_links')</p>

    <p>{!!$project->description!!}</p>
    <table class="my-3">
        <tr>
            <td>Project area:</td>
            <td class="auto">@if (!empty($project->project_area_id)){{$project->project_area->name}}@else Not
                set @endif</td>
        </tr>
        <tr>
            <td>Project period:</td>
            <td class="auto">{{$project->dates}}</td>
        </tr>
        <tr>
            <td>Project activities range:</td>
            <td class="auto">{{$project->projectstart}} — {{$project->projectend}}</td>
        </tr>
        <tr>
            <td>Number of approved updates:</td>
            <td class="derived">{{$project->updatesnumber}}</td>
        </tr>
        @if ($project->recentupdate)
            <tr>
                <td>Most recent approved update:</td>
                <td class="auto">{{$project->recentupdate}}</td>
            </tr>
        @endif
    </table>

    <div class="my-3">
        <p><a class="btn btn-light" data-toggle="collapse" href="#project_updates" role="button" aria-expanded="false"
              aria-controls="project_updates">Project updates @if ($project->pending_updates()->count()) <span
                        class="badge badge-info">{{$project->pending_updates()->count()}}</span><span class="sr-only">pending updates</span> @endif
            </a></p>
        @if($project->pending_updates()->count())
            <div class="collapse" id="project_updates">
                <div class="card card-body">
                    @foreach ($project->pending_updates()->all() as $index => $pu)
                        <p>#{{$index+1}} created on {{$pu->created_at->format('d/m/Y')}} by {{ Auth::user()->name }}
                            @if($pu->status == 'draft') <span class="badge badge-danger">Draft</span>
                            @elseif($pu->status == 'submitted') <span
                                    class="badge badge-warning">Pending approval</span>
                            @elseif($pu->status == 'approved') <span class="badge badge-success">Approved</span>
                            @endif<br/>
                            @if($pu->summary){{$pu->summary}} @else No summary provided @endif
                            <br/>
                            @if ($pu->status == 'draft')
                                <a href="/project/update/{{$pu->id}}/edit"
                                   class="btn btn-outline-secondary btn-sm">Edit <i class="fas fa-info-circle"></i></a>
                                <a href="/project/update/{{$pu->id}}/delete"
                                   class="btn btn-outline-secondary btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this update?');">Delete
                                    <i class="fas fa-trash-alt"></i></a>
                            @endif
                            <a href="/project/update/{{$pu->id}}" class="btn btn-outline-secondary btn-sm">Show
                                <i class="fas fa-info-circle"></i></a>
                            @if ($pu->status != 'draft')
                                <a href="/project/update/{{$pu->id}}/review" class="btn btn-outline-secondary btn-sm">Review
                                    <i class="fas fa-highlighter"></i></a>
                        @endif
                        <p/>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="my-3">
        <p><a class="btn btn-light" data-toggle="collapse" href="#project_activities" role="button"
              aria-expanded="false"
              aria-controls="project_activities">Project activities</a></p>

        <div class="collapse" id="project_activities">
            <div class="card card-body">
                @if (!$activities->isEmpty())
                    <table class="table">
                        @foreach ($activities as $index => $a)
                            <tr id="activity-{{$a->id}}">
                                <td class="collapsed link">@if($a->comments)<i
                                            class="fas fa-caret-square-right mr-2"></i><i
                                            class="fas fa-caret-square-down d-none"></i>@endif{{$a->title}}</td>
                                @if($a->status == 1)
                                    <td class="status inprogress">In progress {{$a->statusdate}}</td>
                                @elseif($a->status == 2)
                                    <td class="status delayed">Delayed {{$a->statusdate}}</td>
                                @elseif($a->status == 3)
                                    <td class="status done">Done {{$a->statusdate}}</td>
                                @elseif($a->status == 0)
                                    <td class="status">Not started</td>
                                @endif
                            </tr>
                            @if ($a->comments)
                                <tr id="activity-{{$a->id}}" class="d-none update">
                                    <td colspan="2">
                                        <table class="table">
                                            @foreach ($a->comments as $puindex => $comment)
                                                <tr>
                                                    <td>@if (!$project->cumulative)<b>Update {{$puindex}}</b>
                                                        : @endif {!! $comment !!}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="my-3">
        <p><a class="btn btn-light" data-toggle="collapse" href="#project_outcomes" role="button" aria-expanded="false"
              aria-controls="project_outcomes">Project outcomes</a></p>
        <div class="collapse" id="project_outcomes">
            <div class="card card-body">
                <ul class="list-group">
                    @foreach($project->outcomes as $outcome)
                        @if ($outcome->completed)
                            <li class="list-group-item">{{$outcome->name}} <a href="#" class="badge badge-success"
                                                                              data-toggle="modal"
                                                                              data-target="#outcome_completed"
                                                                              data-outcome="{{$outcome->name}}"
                                                                              data-id="{{ $outcome->id }}">Completed
                                    on {{$outcome->completed_on}}</a></li>

                            <div class="modal fade" id="outcome_completed" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Outcome
                                                completion: {{$outcome->name}}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <p>Completed on {{$outcome->completed_on}}</p>
                                            </div>
                                            <div class="form-group">
                                                <label for="summary" class="col-form-label">Completion
                                                    description:</label>
                                                <p class="form-control" id="summary"
                                                   name="summary">{{$outcome->summary}}</p>
                                            </div>
                                            <div class="form-group">
                                                <label for="summary" class="col-form-label">Outputs status:</label>
                                                @foreach($outcome->outputs as $output_id => $output)
                                                    <p>Output: {{$output['indicator']}} Value: {{$output['value']}}</p>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <li class="list-group-item">{{$outcome->name}} <a href="#" class="badge badge-primary"
                                                                              data-toggle="modal"
                                                                              data-target="#outcome_completion"
                                                                              data-outcome="{{$outcome->name}}"
                                                                              data-id="{{ $outcome->id }}">Mark as
                                    complete</a>
                            </li>
                            <div class="modal fade" id="outcome_completion" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('outcome_update', $outcome) }}" method="POST">
                                        @method('PUT')
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Outcome
                                                    completion: {{$outcome->name}}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form>
                                                    <!--
                                                    <div class="form-group">
                                                        <label for="completion_date"
                                                               class="col-form-label">Completed on:</label>
                                                        <input type="date" class="form-control" id="completion-date">
                                                    </div>
                                                    -->
                                                    <input name="project" value="{{$project->id}}" hidden>
                                                    <div class="form-group col-md-3 px-0">
                                                        <label for="project_area">Outputs covered</label><br>
                                                        <div class="col-md-4 py-2">
                                                            <select name="outcome_outputs[]" id="outcome_outputs"
                                                                    class="custom-select"
                                                                    multiple="multiple" required>
                                                                @foreach($outputs as $output)
                                                                    <option value="{{$output->id}}" {{ old('output_id') == $output->id || in_array($output->id, $outcome->outputs) ? 'selected':''}}>{{$output->indicator}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="summary" class="col-form-label">Completion
                                                            description:</label>
                                                        <textarea class="form-control" id="summary" name="summary"
                                                                  placeholder="Describe the outcome completion summary"></textarea>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                                <input class="btn btn-primary" value="Save" type="submit">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    @if (!$outputs->isEmpty())
        <h5 class="my-4">Outputs</h5>
        <table class="table table-bordered">
            @foreach ($outputs as $o)
                <tr>
                    <td>{{$o->indicator}}</td>
                    <td class="status @if($o->valuestatus == 1) inprogress @elseif($o->valuestatus == 2) delayed @elseif($o->valuestatus == 3) done @endif">
                        {{$o->valuesum}} @if ($o->status == 'custom') (unplanned) @else / {{$o->target}} @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    <h5 class="my-4">Budget</h5>
    <table class="table table-bordered">
        <thead>
        <th>Used</th>
        <th>Total</th>
        </thead>
        <tbody>
        <tr>
            <td class="derived">{{$project->moneyspent}} {{$project->getCurrencySymbol()}}</td>
            <td class="derived">{{$project->budget}} {{$project->getCurrencySymbol()}}</td>
        </tr>
        </tbody>
    </table>

    <script>
        $(document).on('click', '.collapsed', function () {
            name = $(this).parent().attr('id');
            $('tr#' + name).removeClass('d-none');
            $(this).children('.fas').toggleClass('fa-caret-square-right fa-caret-square-down');
            $(this).toggleClass('collapsed expanded');
        });
        $(document).on('click', '.expanded', function () {
            name = $(this).parent().attr('id');
            $('tr#' + name + '.update').addClass('d-none');
            $(this).children('.fas').toggleClass('fa-caret-square-right fa-caret-square-down');
            $(this).toggleClass('expanded collapsed');
        });
        $(document).ready(function () {
            $('#outcome_outputs').multiselect({
                templates: {
                    li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>'
                }
            });
        });
    </script>

@endsection