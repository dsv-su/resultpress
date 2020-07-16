@extends('layouts.master')

@section('content')
<h4>Project details</h4>
<table class="table" style="width:100%">
    <thead>
        <tr>
            <th>Id</th>
            <th>Project Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $project->id }}</td>
            <td>{{ $project->name}}</td>
            @if($project->status == 1)<td class="status inprogress">In progress</td>
            @elseif($project->status == 2)<td class="status delayed">Delayed</td>
            @elseif($project->status == 3)<td class="status done">Done</td>
            @endif
            <td class="text-center">
                <a href="/project/{{$project->id}}/edit" class="btn btn-outline-primary btn-sm"><i
                        class="far fa-edit"></i></a>
                <a href="/project/{{$project->id}}/summary" class="btn btn-outline-primary btn-sm"><i
                        class="fas fa-calculator"></i></a>
                <a href="/project/{{$project->id}}/update" class="btn btn-outline-primary btn-sm"><i
                        class="fas fa-folder-plus"></i></a>
                <a href="/project/{{$project->id}}/updates" class="btn btn-outline-primary btn-sm"><i
                        class="far fa-list-alt"></i></a>
                <a href="/project/{{$project->id}}/delete" class="btn btn-danger btn-sm" href=""
                    onclick="return confirm('Are you sure you want to delete this item?');"><i
                        class="far fa-trash-alt"></i></a>
            </td>
        </tr>
        <tr>
            <td colspan="4">{{ $project->description }}</td>
        </tr>
    </tbody>
</table>

@if(!$activities->isEmpty())
<h4>Activities</h4>
<table class="table" style="width:100%">
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
            <td class="collapsed link">@if($activity->description)<i class="fas fa-caret-square-right"></i><i class="fas fa-caret-square-down d-none"></i>@endif{{$activity->title}}</td>
            <td>{{$activity->start->format('d/m/Y')}}</td>
            <td>{{$activity->end->format('d/m/Y')}}</td>
            <td>{{$activity->budget}}</td>
        </tr>
        @if ($activity->description)
            <tr id="activity-{{$activity->id}}" class="d-none update"><td colspan="4"><table class="table">
            <tr><td><b>{{$activity->description}}</td></tr>
            </table></td></tr>
        @endif
    @endforeach
    @endif
</table>

@if(!$outputs->isEmpty())
<h4>Outputs</h4>
<table class="table" style="width:100%">
    <thead>
        <tr>
            <th>Indicator</th>
            <th>Target</th>
        </tr>
    </thead>
    <!-- Here comes a foreach to show the outputs -->
    @foreach ($outputs as $output)
    <tr>
        <td>{{$output->indicator}}</td>
        <td>{{$output->target}}</td>
    </tr>
    @endforeach
    @endif
</table>

<script>
    $(document).on('click', '.collapsed', function(){
        name = $(this).parent().attr('id');
        $('tr#'+name).removeClass('d-none');
        console.log('tr#'+name);
        $(this).children('.fas').toggleClass('fa-caret-square-right fa-caret-square-down');
        $(this).toggleClass('collapsed expanded');
    });
    $(document).on('click', '.expanded', function(){
        name = $(this).parent().attr('id');
        $('tr#'+name+'.update').addClass('d-none');
        $(this).children('.fas').toggleClass('fa-caret-square-right fa-caret-square-down');
        $(this).toggleClass('expanded collapsed');
    });
</script>

@endsection