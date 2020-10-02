@extends('layouts.master')

@section('content')
    <div class="row justify-content-between">
        <div class="col-6"><h4>{{ $project->name }}: Update #{{$project_update->index}}</h4></div>
        <div class="col-sm-auto field auto d-flex align-items-center">
            <span>{{$project_update->created_at->format('d/m/Y')}}</span>
        </div>
    </div>
    <p><a href="{{ route('projectupdate_index', $project_update->project_id) }}">Back to project updates list</a></p>

    @if(!$activity_updates->isEmpty())
        <h4>Covered activities</h4>
        <table class="table" style="width:100%" id="activities_table">
            <thead class="text-nowrap">
            <th>Activity</th>
            <th>Status</th>
            <th>Money spent</th>
            <th>Date(s)</th>
            </thead>
            @foreach($activity_updates as $au)
                <tr>
                    <td>{{$au->title}}</td>
                    @if($au->status == 1)
                        <td class="status inprogress text-nowrap">In progress</td>
                    @elseif($au->status == 2)
                        <td class="status delayed text-nowrap">Delayed</td>
                    @elseif($au->status == 3)
                        <td class="status done text-nowrap">Done</td>
                    @endif
                    <td class="text-nowrap">{{$au->money}}</td>
                    <td class="text-nowrap">@if ($au->date) {{$au->date->format('d/m/Y')}} @endif</td>
                </tr>
                <tr class="update">
                    <td colspan=4>
                        <table class="table @if(!$review) mb-2 @else mb-0 @endif">
                            <tr>
                                <td>{!!$au->comment!!}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @if($review)
                    <tr class="update">
                        <td colspan=4>
                            <table class="table mb-2">
                                <tr>
                                    <td class="derived">{{$au->budgetstring}}<br/>{{$au->deadlinestring}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>
    @endif

    @if(!$output_updates->isEmpty())
        <h4>Affected outputs</h4>
        <table class="table mw-400" id="activities_table">
            <thead>
            <th>Output</th>
            <th>Value</th>
            </thead>
            @foreach($output_updates as $ou)
                <tr>
                    <td class="w-75">{{$ou->indicator}}</td>
                    <td class="w-25">{{$ou->value}}</td>
                </tr>
                @if($review)
                    <tr class="update">
                        <td colspan=2>
                            <table class="table mb-2">
                                <tr>
                                    <td class="derived">{{$ou->contributionstring}}<br/>{{$ou->totalstring}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>
    @endif

    @if(!$files->isEmpty())
        <div class="my-1">
        <h5>Attachments:</h5>
        <div>
            @foreach($files as $file)
                <span id="uploaded_file" class="d-block"><a href="{{$file->path}}">{{$file->name}}</a></span>
            @endforeach
        </div>
        </div>
    @endif

    @if ($project_update->summary)
        <div class="my-1">
        <h5>Summary</h5>
        <table class="table table-striped table-bordered">
            <tr>
                <td>{{$project_update->summary}}</td>
            </tr>
        </table>
        </div>
    @endif

    @if($review)
        <form action="{{ route('projectupdate_update', $project_update) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="form-group">
                <div class="form-row my-2">
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
                <div class="form-row my-2">
                    <label for="partner_comment">Partner</label>
                    <textarea rows=4 class="form-control form-control-sm @error('partner_comment') is-danger @enderror"
                              name="partner_comment">{{ old('partner_comment', empty($project_update) ? '' : $project_update->partner_comment) }}</textarea>
                    @error('partner_comment')
                    <div class="text-danger">{{ $errors->first('partner_comment') }}</div>
                    @enderror
                </div>
                <div class="form-row my-2">
                    <label for="internal_comment">Spider's internal</label>
                    <textarea rows=4 class="form-control form-control-sm @error('internal_comment') is-danger @enderror"
                              name="internal_comment">{{ old('internal_comment', empty($project_update) ? '' : $project_update->internal_comment) }}</textarea>
                    @error('internal_comment')
                    <div class="text-danger">{{ $errors->first('internal_comment') }}</div>
                    @enderror
                </div>
                <input class="btn btn-lg" value="SAVE" type="submit">
            </div>
        </form>
    @else
        @if ($project_update->partner_comment || $project_update->internal_comment)
            @if ($project_update->partner_comment)
                <h5>Partner's comment</h5>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>{{$project_update->partner_comment}}</td>
                    </tr>
                </table>
            @endif
            @if ($project_update->internal_comment)
                <h5>Spider's internal comment</h5>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>{{$project_update->internal_comment}}</td>
                    </tr>
                </table>
            @endif
        @endif
    @endif

    <script>
        $(document).on('click', '.approve', function () {
            if ($(this).attr('aria-pressed') === 'true') {
                $('input[name="approved"]').val(1);
            } else {
                $('input[name="approved"]').val(0);
            }
        });
    </script>

@endsection