@extends('layouts.master')

@section('content')
    <h4>{{$project->name}} — project summary</h4>

    <p><a href="{{ url()->previous() }}">Return back</a></p>

    <p>{{$project->description}}</p>
    <table>
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
        <tr>
            <td>Most recent approved update:</td>
            <td class="auto">{{$project->recentupdate}}</td>
        </tr>
    </table>

    <h5 class="my-4">Activities</h5>
    <table class="table">
        @foreach ($activities as $index => $a)
            <tr id="activity-{{$a->id}}">
                <td class="collapsed link">@if($a->comments)<i class="fas fa-caret-square-right mr-2"></i><i
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
                                    <td><b>Update {{$puindex}}</b>: {{$comment}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            @endif
        @endforeach
    </table>

    <h5 class="my-4">Outputs</h5>
    <table class="table table-bordered">
        @foreach ($outputs as $index => $o)
            <tr>
                @if($o->valuestatus == 1)
                    <td class="status inprogress">{{$o->valuesum}}</td>
                @elseif($o->valuestatus == 2)
                    <td class="status delayed">{{$o->valuesum}}</td>
                @elseif($o->valuestatus == 3)
                    <td class="status done">{{$o->valuesum}}</td>
                @endif
                <td>{{$o->indicator}}</td>
            </tr>
        @endforeach
    </table>

    <h5 class="my-4">Budget</h5>
    <table class="table table-bordered">
        <thead>
        <th>Used</th>
        <th>Total</th>
        </thead>
        <tbody>
        <tr>
            <td class="derived">{{$project->moneyspent}}</td>
            <td class="derived">{{$project->budget}}</td>
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
    </script>

@endsection