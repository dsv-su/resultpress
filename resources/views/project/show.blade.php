@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col"><h4>Project details</h4></div>
        <div class="col text-right">@include('project.action_links')</div>
    </div>

    <table class="table my-4">
        <tr>
            <td>Name</td>
            <td>{{ $project->name}}</td>
        </tr>
        <tr>
            <td>Description</td>
            <td>{{ $project->description }}</td>
        </tr>
        <tr>
            <td>Dates</td>
            <td>{{$project->dates}}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>@if($project->status == 1)
                    <span class="badge inprogress">In progress</span>
                @elseif($project->status == 2)
                    <span class="badge delayed">Delayed</span>
                @elseif($project->status == 3)
                    <span class="badge done">Done</span>
                @endif</td>
        </tr>
    </table>

    @if(!$activities->isEmpty())
        <h4>Activities</h4>
        <table class="table">
            <thead>
            <tr>
                <th>Activity name</th>
                <th>Start</th>
                <th>End</th>
                <th>Budget</th>
            </tr>
            </thead>
            @foreach ($activities as $activity)
                <tr id="activity-{{$activity->id}}">
                    <td class="collapsed link">@if($activity->description)<i class="fas fa-caret-square-right"></i><i
                                class="fas fa-caret-square-down d-none"></i>@endif{{$activity->title}}</td>
                    <td>{{$activity->start->format('d/m/Y')}}</td>
                    <td>{{$activity->end->format('d/m/Y')}}</td>
                    <td>{{$activity->budget}}</td>
                </tr>
                @if ($activity->description)
                    <tr id="activity-{{$activity->id}}" class="d-none update">
                        <td colspan="4">
                            <table class="table">
                                <tr>
                                    <td><b>{{$activity->description}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif
            @endforeach
            @endif
        </table>

        @if(!$outputs->isEmpty())
            <h4>Outputs</h4>
            <table class="table mw-400">
                <thead>
                <tr>
                    <th>Indicator</th>
                    <th>Target</th>
                </tr>
                </thead>
                <!-- Here comes a foreach to show the outputs -->
                @foreach ($outputs as $output)
                    <tr>
                        <td class="w-75">{{$output->indicator}}</td>
                        <td class="w-25">{{$output->target}}</td>
                    </tr>
                @endforeach
                @endif
            </table>

            <script>
                $(document).on('click', '.collapsed', function () {
                    name = $(this).parent().attr('id');
                    $('tr#' + name).removeClass('d-none');
                    console.log('tr#' + name);
                    $(this).children('.fas').toggleClass('fa-caret-square-right fa-caret-square-down');
                    $(this).toggleClass('collapsed expanded');
                });
                $(document).on('click', '.expanded', function () {
                    name = $(this).parent().attr('id');
                    $('tr#' + name + '.update').addClass('d-none');
                    $(this).children('.fas').toggleClass('fa-caret-square-right fa-caret-square-down');
                    $(this).toggleClass('expanded collapsed');
                });
            </script>

@endsection