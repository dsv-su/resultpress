@extends('layouts.master')

@section('content')
<h4>Update #{{$project_update->index}}</h4>
<p><a href="{{ route('projectupdate_index', $project_update->project_id) }}">Back to project updates list</a></p>

<h5>Update details:</h5>
<p>Date: {{$project_update->created_at->format('d/m/Y')}}</p>

@if(!$activity_updates->isEmpty())
<h5>Covered activities</h5>
<table class="table table-sm table-striped table-bordered" style="width:100%" id="activities_table">
    <thead>
        <th>Activity</th>
        <th>Status</th>
        <th>Comment</th>
        <th>Money spent</th>
        <th>Date(s)</th>
    </thead>
    @foreach($activity_updates as $au)
    <tr>
        <td>{{$au->title}}</td>
        @if($au->status == 1)<td class="status inprogress">Started</td>
        @elseif($au->status == 2)<td class="status delayed">Delayed</td>
        @elseif($au->status == 3)<td class="status done">Done</td>
        @endif
        <td>{{$au->comment}}</td>
        <td>{{$au->money}}</td>
        <td>{{$au->date->format('d/m/Y')}}</td>
    </tr>
    @if($review)
    <tr>
        <td colspan=5 class="derived">{{$au->budgetstring}}<br />{{$au->deadlinestring}}</td>
    </tr>
    @endif
    @endforeach
</table>
@endif

@if(!$output_updates->isEmpty())
<h5>Affected outputs</h5>
<table class="table table-sm table-striped table-bordered" style="width:100%" id="activities_table">
    <thead>
        <th>Output</th>
        <th>Value</th>
    </thead>
    @foreach($output_updates as $ou)
    <tr>
        <td>{{$ou->indicator}}</td>
        <td>{{$ou->value}}</td>
    </tr>
    @if($review)
    <tr>
        <td colspan=2 class="derived">{{$ou->contributionstring}}<br />{{$ou->totalstring}}</td>
    </tr>
    @endif
    @endforeach
</table>
@endif

@if ($project_update->summary)
<h5>Summary</h5>
<p>{{$project_update->summary}}</p>
@endif

@if($review)
<form action="{{ route('projectupdate_update', $project_update) }}" method="POST">
    @method('PUT')
    @csrf
    <div class="form-group">
        <div class="form-row">
            <div class="col">
                <h5>Reviewer comment</h5>
            </div>
            <div class="col text-right">
                <button type="button" class="btn approve editable" name="approved" data-toggle="button" 
                @if ($project_update->approved) aria-pressed="true" @else aria-pressed="false" @endif
                    autocomplete="off">
                    Approve
                </button>
                <input type="hidden" name="approved" @empty($project_update->approved) value=0 @else
                value={{$project_update->approved}} @endempty>
            </div>
        </div>
        <div class="form-row">
            <label for="partner_comment">Partner</label>
            <textarea rows=4 class="form-control form-control-sm @error('partner_comment') is-danger @enderror"
                type="text"
                name="partner_comment">{{ old('partner_comment', empty($project_update) ? '' : $project_update->partner_comment) }}</textarea>
            @error('partner_comment')<div class="text-danger">{{ $errors->first('partner_comment') }}</div>
            @enderror
        </div>
        <div class="form-row">
            <label for="internal_comment">Spider's internal</label>
            <textarea rows=4 class="form-control form-control-sm @error('internal_comment') is-danger @enderror"
                type="text"
                name="internal_comment">{{ old('internal_comment', empty($project_update) ? '' : $project_update->internal_comment) }}</textarea>
            @error('internal_comment')<div class="text-danger">{{ $errors->first('internal_comment') }}</div>
            @enderror
        </div>
        <input class="btn btn-primary btn-lg" value="SAVE" type="submit">
    </div>
</form>
@else
@if ($project_update->partner_comment || $project_update->internal_comment)
<h5>Comments</h5>
@if ($project_update->partner_comment)
Partner:<p>{{$project_update->partner_comment}}</p>
@endif
@if ($project_update->internal_comment)
Spider's internal:<p>{{$project_update->internal_comment}}</p>
@endif
@endif
@endif

<script>
    $(document).on('click', '.approve', function(){
        if ($(this).attr('aria-pressed') == 'true') {
        $('input[name="approved"]').val(1);
        } else {
            $('input[name="approved"]').val(0);
        }
});
</script>

@endsection